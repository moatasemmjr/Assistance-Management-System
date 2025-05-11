
<?php
// تضمين مكتبة TCPDF
require_once('..\libs\tcpdf\examples\tcpdf_include.php');
// "C:\xampp\htdocs\assistance\libs\tcpdf\examples\tcpdf_include.php"

include '..\includes\db.php';
// الحصول على معرف المستفيد من الرابط أو النموذج
$beneficiaryId = isset($_GET['beneficiary_id'])? $_GET['beneficiary_id'] : 0;

// استعلام للحصول على بيانات المستفيدs
$sqlBeneficiary = "SELECT * FROM beneficiaries WHERE id = :id";
$stmtBeneficiary = $con -> prepare($sqlBeneficiary);
$stmtBeneficiary-> $bindParam(':id', $beneficiaryId, PDO::PARAM_INT);
$stmtBeneficiary->execute();
$beneficiary =$stmtBeneficiary->fetch(PDO::FETCH_ASSOC);

// استعلام للحصول على جميع المساعدات التي حصل عليها المستفيد
$sqlAssistance = "SELECT * FROM assistance WHERE beneficiary_id = :id";
$stmtAssistance = $con ->prepare($sqlAssistance);
$stmtAssistance->$bindParam(':id',$beneficiaryId, PDO::PARAM_INT);
$stmtAssistance->execute();
$assistanceRecords = $stmtAssistance->fetchAll(PDO::FETCH_ASSOC);

// تحقق إذا كان المستفيد موجودًا
if (!beneficiary) {
    echo "المستفيد غير موجود.";
    exit;
}

// إعداد TCPDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('كشف المساعدات');
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();

// إضافة عنوان المستفيد
$pdf->SetFont('helvetica', 'B', 14);
$pdf->Cell(0, 10, 'كشف المساعدات للمستفيد: ' . beneficiary['beneficiary_name'], 0, 1, 'C');
$pdf->Ln(10);

// إضافة تفاصيل المستفيد
$pdf->SetFont('helvetica', ”, 12);
$pdf->Cell(30, 10, 'الاسم:');
$pdf->Cell(0, 10,beneficiary['beneficiary_name'], 0, 1);
$pdf->Cell(30, 10, 'العنوان:');
$pdf->Cell(0, 10, beneficiary['address'], 0, 1);pdf->Cell(30, 10, 'رقم الهاتف:');
$pdf->Cell(0, 10,beneficiary['phone'], 0, 1);
$pdf->Ln(10);

// إضافة جدول المساعداتpdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(30, 10, 'تاريخ المساعدة', 1, 0, 'C');
$pdf->Cell(50, 10, 'نوع المساعدة', 1, 0, 'C');
$pdf->Cell(30, 10, 'المبلغ', 1, 1, 'C');
$pdf->SetFont('helvetica', '', 12);

// إضافة بيانات المساعدات
foreach ($assistanceRecords as $record) {
    $pdf->Cell(30, 10,$record['assistance_date'], 1, 0, 'C');
    $pdf->Cell(50, 10,$record['assistance_type'], 1, 0, 'C');
    $pdf->Cell(30, 10,$record['amount'], 1, 1, 'C');
}

// إخراج ملف PDF
$pdf->Output('aid_report_' .beneficiary['beneficiary_name'] . '.pdf', 'I');
?>