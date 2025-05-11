<?php
require_once('..\libs\tcpdf\tcpdf.php');

include '../includes/db.php';

// التحقق من إرسال البيانات
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $beneficiaries = explode(',', $_POST['beneficiaries']);
    $aid_type = $_POST['aid_type'];
    $pickup_location = $_POST['pickup_location'];
    $pickup_datetime = $_POST['pickup_datetime'];

    // إعداد PDF
    $pdf = new TCPDF('P', 'mm', 'A4');

    $lg = array();
    $lg['a_meta_charset'] = 'UTF-8';
    $lg['a_meta_dir'] = 'rtl';
    $lg['a_meta_language'] = 'ar';
    $pdf->setLanguageArray($lg);
    $pdf->setRTL(true);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Aid Distribution System');
    $pdf->SetTitle('كشف الدعوات');
    $pdf->SetSubject('دعوات الاستلام');
    $pdf->SetMargins(10, 10, 10);

    $cards_per_page = 8;
    $card_width = 90;
    $card_height = 50;
    $current_card = 0;

    foreach ($beneficiaries as $beneficiary_id) {
        // تعديل الاستعلام لجلب رقم الخيمة
        $sql = "SELECT full_name, primary_phone, tent_number FROM beneficiaries WHERE id = $beneficiary_id";
        $result = mysqli_query($con, $sql);
        $row = mysqli_fetch_assoc($result);

        if ($current_card % $cards_per_page === 0) {
            $pdf->AddPage();
        }

        $x = 10 + ($current_card % 2) * ($card_width + 10);
        $y = 10 + floor(($current_card % $cards_per_page) / 2) * ($card_height + 10);

        $pdf->SetXY($x, $y);
        $pdf->Cell($card_width, 10, 'دعوة استلام مساعدة', 1, 1, 'C');
        $pdf->SetFont('dejavusans', 'B', 10);
        $pdf->SetXY($x, $y + 12);
        $pdf->MultiCell($card_width, 6, 
            "اسم المستفيد: " . $row['full_name'] . "\n" .
            "رقم الهاتف: " . $row['primary_phone'] . "\n" .
            "رقم الخيمة: " . $row['tent_number'] . "\n" .  // إضافة رقم الخيمة
            "نوع المساعدة: " . $aid_type . "\n" .
            "مكان التسليم: " . $pickup_location . "\n" .
            "موعد التسليم: " . date('Y-m-d H:i', strtotime($pickup_datetime)), 
            1);
        $current_card++;
    }

    $pdf->Output('aid_invitations.pdf', 'I');
    exit;
}