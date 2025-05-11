<?php
// فقط ل طباعة دعوات دون تسجيل 
require_once('..\libs\tcpdf\tcpdf.php');
include '../includes/db.php'; 

// التحقق من إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $pickup_location = $_POST['pickup_location'];
    $pickup_date = $_POST['pickup_date'];
    $aid_type = $_POST['aid_type'];
    $selected_beneficiaries = $_POST['beneficiaries']; // قائمة المستفيدين المختارين

    // إنشاء ملف PDF
    $pdf = new TCPDF('P', 'mm', 'A4', 'UTF-8');

    $lg = array();
    $lg['a_meta_charset'] = 'UTF-8';
    $lg['a_meta_dir'] = 'rtl';
    $lg['a_meta_language'] = 'ar';
    $pdf->setLanguageArray($lg);
    $pdf->setRTL(true);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Aid Distribution System');
    $pdf->SetTitle('دعوات استلام المساعدات');
    $pdf->SetSubject('دعوات استلام');
    $pdf->SetKeywords('TCPDF, PDF, invitations');

    // إعداد الصفحة
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetMargins(10, 10, 10);
    $pdf->SetFont('dejavusans', '', 10);

    $cards_per_page = 8; // عدد الدعوات في الصفحة الواحدة
    $card_width = 90;    // عرض البطاقة
    $card_height = 50;   // ارتفاع البطاقة
    $current_card = 0;   // مؤشر البطاقة الحالية

    // البدء في إنشاء الدعوات
    foreach ($selected_beneficiaries as $beneficiary_id) {
        // جلب بيانات المستفيد بما في ذلك رقم الخيمة
        $sql = "SELECT full_name, primary_phone, tent_number FROM beneficiaries WHERE id = $beneficiary_id";
        $result = mysqli_query($con, $sql);
        $row = mysqli_fetch_assoc($result);

        // تحقق من بدء صفحة جديدة
        if ($current_card % $cards_per_page === 0) {
            $pdf->AddPage();
        }

        // تحديد موقع البطاقة في الصفحة
        $x = 10 + ($current_card % 2) * ($card_width + 10); // عمود البطاقة
        $y = 10 + floor(($current_card % $cards_per_page) / 2) * ($card_height + 10); // صف البطاقة

        // رسم البطاقة
        $pdf->SetXY($x, $y);
        $pdf->SetFont('dejavusans', 'B', 12);
        $pdf->Cell($card_width, 10, 'دعوة استلام مساعدة', 1, 1, 'C', false);

        $pdf->SetFont('dejavusans', '', 10);
        $pdf->SetXY($x, $y + 12);
        $pdf->MultiCell($card_width, 6, 
            "اسم المستفيد: " . $row['full_name'] . "\n" .
            "رقم الهاتف: " . $row['primary_phone'] . "\n" .
            "رقم الخيمة: " . $row['tent_number'] . "\n" .  // إضافة رقم الخيمة
            "نوع المساعدة: " . $aid_type . "\n" .
            "مكان الاستلام: " . $pickup_location . "\n" .
            "موعد الاستلام: " . $pickup_date, 
            1, false);

        $current_card++;
    }

    // إخراج ملف PDF
    $pdf->Output('aid_invitations.pdf', 'I'); // عرض PDF مباشرة في المتصفح
    exit;
}

// جلب قائمة المستفيدين مع رقم الخيمة
$sqlBeneficiaries = "SELECT id, full_name, tent_number FROM beneficiaries";
$result = mysqli_query($con, $sqlBeneficiaries);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إعداد دعوات الاستلام</title>
    <link rel="stylesheet" href="..\assets\css\adminstyles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h1 class="text-center">إعداد دعوات استلام المساعدات</h1>
    <h3 class="text-center">لن يتم التسجيل في القاعدة فقط طباعة</h3>

    <!-- نموذج إدخال المعلومات -->
    <form method="POST">
        <div class="mb-3">
            <label for="pickup_location" class="form-label">مكان الاستلام</label>
            <input type="text" name="pickup_location" id="pickup_location" class="form-control" placeholder="أدخل مكان الاستلام" required>
        </div>
        <div class="mb-3">
            <label for="pickup_date" class="form-label">موعد الاستلام</label>
            <input type="date" name="pickup_date" id="pickup_date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="aid_type" class="form-label">نوع المساعدة</label>
            <select name="aid_type" id="aid_type" class="form-select" required>
                <option value="مالية">مالية</option>
                <option value="غذائية">غذائية</option>
                <option value="صحية">صحية</option>
                <option value="24 لحمة">24 لحمة</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="beneficiaries" class="form-label">اختر المستفيدين</label>
            <div class="form-check">
                <!-- عرض المستفيدين مع رقم الخيمة -->
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <input type="checkbox" name="beneficiaries[]" value="<?= $row['id'] ?>" id="beneficiary_<?= $row['id'] ?>" class="form-check-input">
                    <label for="beneficiary_<?= $row['id'] ?>" class="form-check-label"><?= $row['full_name'] ?> (رقم الخيمة: <?= $row['tent_number'] ?>)</label><br>
                <?php endwhile; ?>
            </div>
        </div>
        <button type="submit" class="btn btn-primary w-100">إنشاء الدعوات</button>
    </form>
</div>
</body>
</html>