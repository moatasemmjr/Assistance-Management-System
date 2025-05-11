<?php
 
require '../vendor/autoload.php'; 
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

include '../includes/db.php'; 

 
$sql = "SELECT id, full_name, id_number, residence, tent_number, family_count, primary_phone, secondary_phone, spouse_name, spouse_id, housing_type, damage_type, children_count FROM beneficiaries";
$result = mysqli_query($con, $sql);
$data = mysqli_fetch_all($result, MYSQLI_ASSOC);

 
if ($data) {
 
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

 
    $sheet->setCellValue('A1', 'ID');
    $sheet->setCellValue('B1', 'الاسم الكامل');
    $sheet->setCellValue('C1', 'رقم الهوية');
    $sheet->setCellValue('D1', 'مكان السكن');
    $sheet->setCellValue('E1', 'رقم الخيمة');
    $sheet->setCellValue('F1', 'عدد الأفراد');
    $sheet->setCellValue('G1', 'رقم الهاتف');
    $sheet->setCellValue('H1', 'رقم الهاتف الثانوي');
    $sheet->setCellValue('I1', 'اسم الزوج/الزوجة');
    $sheet->setCellValue('J1', 'رقم هوية الزوج/الزوجة');
    $sheet->setCellValue('K1', 'نوع السكن');
    $sheet->setCellValue('L1', 'نوع الضرر');
    $sheet->setCellValue('M1', 'عدد الأطفال');

     $row_num = 2;  
    foreach ($data as $row) {
        $sheet->setCellValue('A' . $row_num, $row['id']);
        $sheet->setCellValue('B' . $row_num, $row['full_name']);
        $sheet->setCellValue('C' . $row_num, $row['id_number']);
        $sheet->setCellValue('D' . $row_num, $row['residence']);
        $sheet->setCellValue('E' . $row_num, $row['tent_number']);
        $sheet->setCellValue('F' . $row_num, $row['family_count']);
        $sheet->setCellValue('G' . $row_num, $row['primary_phone']);
        $sheet->setCellValue('H' . $row_num, $row['secondary_phone']);
        $sheet->setCellValue('I' . $row_num, $row['spouse_name']);
        $sheet->setCellValue('J' . $row_num, $row['spouse_id']);
        $sheet->setCellValue('K' . $row_num, $row['housing_type']);
        $sheet->setCellValue('L' . $row_num, $row['damage_type']);
        $sheet->setCellValue('M' . $row_num, $row['children_count']);
        $row_num++;
    }

    
    $writer = new Xlsx($spreadsheet);
    $filename = 'beneficiaries_data.xlsx';

   
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer->save('php://output');
    exit;
} else {
    echo "لا توجد بيانات للتصدير.";
}
?>

<!-- <!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تصدير بيانات المستفيدين</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h1 class="text-center">تصدير بيانات المستفيدين إلى Excel</h1>
    <form method="POST">
        <button type="submit" class="btn btn-success w-100">تصدير البيانات</button>
    </form>
</div>
<script src="../assets/js/bootstrap.bundle.min.js"></script>
</body>
</html> -->