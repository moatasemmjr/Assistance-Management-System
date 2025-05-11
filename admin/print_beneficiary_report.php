<?php
require_once('..\libs\tcpdf\tcpdf.php');
include '../includes/db.php';

// التحقق من إرسال معرف المستفيد
if (isset($_GET['id'])) {
    $beneficiary_id = intval($_GET['id']);

    // جلب بيانات المستفيد
    $sqlBeneficiary = "SELECT * FROM beneficiaries WHERE id = $beneficiary_id";
    $resultBeneficiary = mysqli_query($con, $sqlBeneficiary);
    $beneficiary = mysqli_fetch_assoc($resultBeneficiary);

    if (!$beneficiary) {
        die("المستفيد غير موجود.");
    }

    // جلب كشف المساعدات للمستفيد
    $sqlAids = "SELECT * FROM aids WHERE beneficiary_id = $beneficiary_id";
    $resultAids = mysqli_query($con, $sqlAids);

    // إعداد PDF
    $pdf = new TCPDF('P', 'mm', 'A4', 'UTF-8');
    $lg = array();
    $lg['a_meta_charset'] = 'UTF-8';
    $lg['a_meta_dir'] = 'rtl';
    $lg['a_meta_language'] = 'ar';
    $pdf->setLanguageArray($lg);
    $pdf->setRTL(true);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Aid Distribution System');
    $pdf->SetTitle('تقرير المستفيد');
    $pdf->SetSubject('تقرير مستفيد');
    $pdf->SetMargins(10, 10, 10);
    $pdf->AddPage();

    // عنوان التقرير
    $pdf->SetFont('dejavusans', 'B', 14);
    $pdf->Cell(0, 10, 'تقرير بيانات المستفيد', 0, 1, 'C');
    $pdf->Ln(5);

    // بيانات المستفيد
    $pdf->SetFont('dejavusans', '', 12);
    $pdf->Write(6, "رقم الخيمة: " . ($beneficiary['tent_number'] ?? 'مقيم') . "\n");
    $pdf->Write(6, "اسم المستفيد: " . $beneficiary['full_name'] . "\n");
    $pdf->Write(6, "رقم الهوية: " . $beneficiary['id_number'] . "\n");
    $pdf->Write(6, "مكان السكن: " . $beneficiary['residence'] . "\n");
    $pdf->Write(6, "نوع السكن: " . $beneficiary['housing_type'] . "\n");
    $pdf->Write(6, "عدد أفراد الأسرة: " . $beneficiary['family_count'] . "\n");
    $pdf->Write(6, "رقم الجوال: " . $beneficiary['primary_phone'] . "\n");
    $pdf->Ln(10);

    // عنوان كشف المساعدات
    $pdf->SetFont('dejavusans', 'B', 12);
    $pdf->Cell(0, 10, 'كشف المساعدات:', 0, 1, 'L');
    $pdf->Ln(5);

    // كشف المساعدات
    $pdf->SetFont('dejavusans', '', 10);
    $html = '<table border="1" cellpadding="5">
                <thead>
                    <tr>
                        <th width="5%">#</th>
                        <th width="30%">نوع المساعدة</th>
                        <th width="30%">تاريخ المساعدة</th>
                        <th width="30%">ملاحظات</th>
                    </tr>
                </thead>
                <tbody>';

    $counter = 1;
    while ($row = mysqli_fetch_assoc($resultAids)) {
        $html .= '<tr>
                    <td align="center">' . $counter++ . '</td>
                    <td align="center">' . $row['aid_type'] . '</td>
                    <td align="center">' . $row['aid_date'] . '</td>
                    <td align="center">' . htmlspecialchars($row['notes']) . '</td>
                  </tr>';
    }

    $html .= '</tbody></table>';
    $pdf->writeHTML($html, true, false, false, false, '');

    // إخراج PDF
    $pdf->Output('beneficiary_report_' . $beneficiary_id . '.pdf', 'I');
    exit;
} else {
    die("معرف المستفيد غير موجود.");
}
?>
