<?php
require '../vendor/autoload.php';  
include '../includes/db.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


$spreadsheet = new Spreadsheet();

//   الصفحة الأولى: المستفيدين مع المساعدات
$sheet1 = $spreadsheet->getActiveSheet();
$sheet1->setTitle('المستفيدين مع المساعدات');

// عناوين الأعمدة
$headers = [
    'A1' => 'رقم الخيمة', 'B1' => 'الاسم الكامل', 'C1' => 'رقم الهوية', 
    'D1' => 'اسم الزوجة', 'E1' => 'رقم هوية الزوجة', 'F1' => 'عدد الأفراد',
    'G1' => 'مكان السكن', 'H1' => 'نوع السكن', 'I1' => 'رقم الجوال', 
    'J1' => 'رقم الجوال البديل', 'K1' => 'نوع المساعدة', 
    'L1' => 'تاريخ المساعدة', 'M1' => 'ملاحظات المساعدة'
];

foreach ($headers as $cell => $text) {
    $sheet1->setCellValue($cell, $text);
}

// استعلام لجلب بيانات المستفيدين مع المساعدات
$sql = "SELECT b.*, a.aid_type, a.aid_date, a.notes
        FROM beneficiaries b
        LEFT JOIN aids a ON b.id = a.beneficiary_id";
$result = mysqli_query($con, $sql);

$rowNum = 2; // بدء الكتابة من الصف الثاني
while ($row = mysqli_fetch_assoc($result)) {
    $sheet1->setCellValue("A$rowNum", $row['tent_number']);
    $sheet1->setCellValue("B$rowNum", $row['full_name']);
    $sheet1->setCellValue("C$rowNum", $row['id_number']);
    $sheet1->setCellValue("D$rowNum", $row['spouse_name']);
    $sheet1->setCellValue("E$rowNum", $row['spouse_id']);
    $sheet1->setCellValue("F$rowNum", $row['family_count']);
    $sheet1->setCellValue("G$rowNum", $row['residence']);
    $sheet1->setCellValue("H$rowNum", $row['housing_type']);
    $sheet1->setCellValue("I$rowNum", $row['primary_phone']);
    $sheet1->setCellValue("J$rowNum", $row['secondary_phone']);
    $sheet1->setCellValue("K$rowNum", $row['aid_type']);
    $sheet1->setCellValue("L$rowNum", $row['aid_date']);
    $sheet1->setCellValue("M$rowNum", $row['notes']);
    $rowNum++;
}

//  الصفحة الثانية: جدول المستفيدين
$sheet2 = $spreadsheet->createSheet();
$sheet2->setTitle('جدول المستفيدين');

// عناوين الأعمدة
$sheet2->fromArray([
     'رقم الخيمة', 'الاسم الكامل', 'رقم الهوية', 
    'اسم الزوجة', 'رقم هوية الزوجة', 'عدد الأفراد', 
    'مكان السكن', 'نوع السكن', 'رقم الجوال', 
    'رقم الجوال البديل', 'تاريخ الإنشاء'
], NULL, 'A1');

// جلب بيانات جدول المستفيدين
$sql_beneficiaries = "SELECT * FROM beneficiaries";
$result_beneficiaries = mysqli_query($con, $sql_beneficiaries);

$rowNum = 2;
while ($row = mysqli_fetch_assoc($result_beneficiaries)) {
    $sheet2->fromArray(array_values($row), NULL, "A$rowNum");
    $rowNum++;
}

// الصفحة الثالثة: جدول المساعدات مع المستفيد

$sheet3 = $spreadsheet->createSheet();
$sheet3->setTitle('جدول المساعدات');

$sheet3->fromArray([
     'نوع المساعدة', 'تاريخ المساعدة', 'ملاحظات', 
    'اسم المستفيد', 'رقم الخيمة'
], NULL, 'A1');

$sql_aids = "SELECT a.*, b.full_name, b.tent_number
             FROM aids a
             JOIN beneficiaries b ON a.beneficiary_id = b.id";
$result_aids = mysqli_query($con, $sql_aids);

$rowNum = 2;
while ($row = mysqli_fetch_assoc($result_aids)) {
    $sheet3->fromArray([
         $row['aid_type'], $row['aid_date'], 
        $row['notes'], $row['full_name'], $row['tent_number']
    ], NULL, "A$rowNum");
    $rowNum++;
}

 
// الصفحة الرابعة: جدول التبرعات
 
$sheet4 = $spreadsheet->createSheet();
$sheet4->setTitle('جدول التبرعات');

// عناوين الأعمدة
$sheet4->fromArray(['ID', 'اسم المتبرع', 'نوع التبرع', 'المبلغ', 'تاريخ التبرع'], NULL, 'A1');

$sql_donations = "SELECT * FROM donations";
$result_donations = mysqli_query($con, $sql_donations);

$rowNum = 2;
while ($row = mysqli_fetch_assoc($result_donations)) {
    $sheet4->fromArray(array_values($row), NULL, "A$rowNum");
    $rowNum++;
}

//  حفظ وتصدير ملف Excel
$filename = 'تقرير_المستفيدين_والمساعدات_' . date('Ymd') . '.xlsx';

header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header("Content-Disposition: attachment;filename=\"$filename\"");
header('Cache-Control: max-age=0');

$writer = new Xlsx($spreadsheet);
$writer->save('php://output');
exit;
?>
