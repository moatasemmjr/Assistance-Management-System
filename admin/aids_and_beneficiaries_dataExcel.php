<?php
require '../vendor/autoload.php'; 

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;


include '../includes/db.php'; 

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // جلب بيانات المساعدات مع معلومات المستفيدين
    $sql = "SELECT 
                a.id, 
                a.beneficiary_id, 
                a.aid_type, 
                a.aid_date, 
                a.notes, 
                a.created_at, 
                b.full_name, 
                b.id_number 
            FROM aids a
            JOIN beneficiaries b ON a.beneficiary_id = b.id";
    $result = mysqli_query($con, $sql);
    $data = mysqli_fetch_all($result, MYSQLI_ASSOC);

    // إذا كانت هناك بيانات
    if ($data) {
        // إنشاء مستند Excel جديد
        $spreadsheet = new Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // كتابة العناوين (Header) في الصف الأول
        $sheet->setCellValue('A1', 'رقم الخيمة');
        $sheet->setCellValue('G1', 'الاسم الكامل');
        $sheet->setCellValue('H1', 'رقم الهوية');
        $sheet->setCellValue('C1', 'نوع المساعدة');
        $sheet->setCellValue('D1', 'تاريخ المساعدة');
        $sheet->setCellValue('E1', 'الملاحظات');
        $sheet->setCellValue('F1', 'تاريخ الإنشاء');
        

        // إضافة البيانات إلى الجدول
        $row_num = 2; // يبدأ من الصف الثاني
        foreach ($data as $row) {
            $sheet->setCellValue('A' . $row_num, $row['id']);
            $sheet->setCellValue('G' . $row_num, $row['full_name']);
            $sheet->setCellValue('H' . $row_num, $row['id_number']);
            $sheet->setCellValue('C' . $row_num, $row['aid_type']);
            $sheet->setCellValue('D' . $row_num, $row['aid_date']);
            $sheet->setCellValue('E' . $row_num, $row['notes']);
            $sheet->setCellValue('F' . $row_num, $row['created_at']);
           
            $row_num++;
        }

        // كتابة الملف إلى تنسيق Excel
        $writer = new Xlsx($spreadsheet);
        $filename = 'aids_and_beneficiaries_data.xlsx';

        // إرسال الملف للمستخدم للتنزيل
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        $writer->save('php://output');
        exit;
    } else {
        echo "لا توجد بيانات للتصدير.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تصدير بيانات المساعدات والمستفيدين</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h1 class="text-center">تصدير بيانات المساعدات والمستفيدين إلى Excel</h1>
    <form method="POST">
        <button type="submit" class="btn btn-success w-100">تصدير البيانات</button>
    </form>
</div>
</body>
</html>