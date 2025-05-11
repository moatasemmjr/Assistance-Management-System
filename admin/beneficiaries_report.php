<?php
require_once('..\libs\tcpdf\tcpdf.php');
include '../includes/db.php';  

// استعلام لجلب بيانات المستفيدين
$sql = "SELECT * FROM beneficiaries";
$result = mysqli_query($con, $sql);

// إنشاء ملف PDF
$pdf = new TCPDF();
$lg = array();
    $lg['a_meta_charset'] = 'UTF-8';
    $lg['a_meta_dir'] = 'rtl';
    $lg['a_meta_language'] = 'ar';
    $pdf->setLanguageArray($lg);
    $pdf->setRTL(true);
    
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Aid Distribution System');
$pdf->SetTitle('كشف بيانات المستفيدين');
$pdf->SetSubject('كشف المستفيدين');
$pdf->SetKeywords('TCPDF, PDF, beneficiaries');

// إعداد الصفحة
$pdf->setPrintHeader(false);
$pdf->setPrintFooter(false);
$pdf->SetMargins(10, 10, 10);
$pdf->SetFont('dejavusans', '', 12);
$pdf->AddPage();

// عنوان التقرير
$pdf->SetFont('dejavusans', 'B', 14);
$pdf->Cell(0, 10, 'كشف بيانات المستفيدين', 0, 1, 'C');
$pdf->Ln(5);

// إنشاء جدول البيانات
$pdf->SetFont('dejavusans', '', 10);
$html = '<table border="1" cellpadding="5">
            <thead>
                <tr style="background-color: #f2f2f2;">
                    <th width="5%">#</th>
                    <th width="30%">الاسم الكامل</th>
                    <th width="20%">رقم الهوية</th>
                    <th width="15%">عدد الأفراد</th>
                    <th width="30%">مكان السكن</th>
                    
                </tr>
            </thead>
            <tbody>';

// إضافة بيانات المستفيدين
$counter = 1;
while ($row = mysqli_fetch_assoc($result)) {
    $html .= '<tr>
                <td align="center">' . $counter++ . '</td>
                <td>' . htmlspecialchars($row['full_name']) . '</td>
                <td align="center">' . htmlspecialchars($row['id_number']) . '</td>
                <td align="center">' . htmlspecialchars($row['family_count']) . '</td>
                <td>' . htmlspecialchars($row['residence']) . '</td>
              </tr>';
}

$html .= '</tbody></table>';

// إضافة الجدول إلى PDF
$pdf->writeHTML($html, true, false, false, false, '');
$pdf->Output('beneficiaries_report.pdf', 'I');
?>
