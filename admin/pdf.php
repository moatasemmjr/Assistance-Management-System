<?php
require '../libs/fpdf.htm'; // تضمين مكتبة FPDF
include '../includes/db.php'; // الاتصال بقاعدة البيانات

class PDF extends FPDF
{
    // رأس الصفحة
    function Header()
    {
        $this->SetFont('Arial', 'B', 14);
        $this->Cell(0, 10, 'تقرير بيانات المستفيدين', 0, 1, 'C');
        $this->Ln(10);
    }

    // تذييل الصفحة
    function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 10);
        $this->Cell(0, 10, 'صفحة ' . $this->PageNo(), 0, 0, 'C');
    }
}

// استعلام لجلب كافة بيانات المستفيدين
$sql = "SELECT * FROM beneficiaries";
$result = mysqli_query($con, $sql);

// إنشاء ملف PDF
$pdf = new PDF();
$pdf->AddPage();
$pdf->SetFont('Arial', '', 12);

// ترويسة الجدول
$pdf->SetFillColor(200, 220, 255);
$pdf->Cell(10, 10, '#', 1, 0, 'C', true);
$pdf->Cell(50, 10, 'الاسم الكامل', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'رقم الهوية', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'عدد الأفراد', 1, 0, 'C', true);
$pdf->Cell(40, 10, 'مكان السكن', 1, 0, 'C', true);
$pdf->Cell(30, 10, 'نوع السكن', 1, 1, 'C', true);

// بيانات الجدول
$counter = 1;
while ($row = mysqli_fetch_assoc($result)) {
    $pdf->Cell(10, 10, $counter++, 1, 0, 'C');
    $pdf->Cell(50, 10, $row['full_name'], 1, 0, 'C');
    $pdf->Cell(30, 10, $row['id_number'], 1, 0, 'C');
    $pdf->Cell(30, 10, $row['family_count'], 1, 0, 'C');
    $pdf->Cell(40, 10, $row['residence'], 1, 0, 'C');
    $pdf->Cell(30, 10, $row['housing_type'], 1, 1, 'C');
}

// إخراج ملف PDF
$pdf->Output('D', 'beneficiaries_report.pdf'); // تنزيل الملف
?>
