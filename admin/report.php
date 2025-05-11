<?php
require_once('..\libs\tcpdf\tcpdf.php');
include '../includes/db.php';

// التحقق من إرسال نموذج إدخال المعلومات
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_donation'])) {
    $association_name = $_POST['association_name'];
    $aid_type = $_POST['aid_type'];
    $quantity = $_POST['quantity'];
    $beneficiaries_count = $_POST['beneficiaries_count'];
    $date = $_POST['date'];
    $description = $_POST['description'];
    
    // إدخال البيانات إلى جدول donations
    $sql = "INSERT INTO donations (association_name, aid_type, quantity, beneficiaries_count, date, description) VALUES ('$association_name', '$aid_type', $quantity, $beneficiaries_count, '$date', '$description')";
    mysqli_query($con, $sql);
}

// التحقق من إرسال نموذج التقرير
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_report'])) {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

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
    $pdf->SetTitle('تقارير المساعدات');
    $pdf->SetSubject('تقارير');
    $pdf->SetKeywords('TCPDF, PDF, reports');

    // إعداد الصفحة
    $pdf->setPrintHeader(false);
    $pdf->setPrintFooter(false);
    $pdf->SetMargins(10, 10, 10);
    $pdf->SetFont('dejavusans', '', 10);
    $pdf->AddPage();

    // استعلام لجلب بيانات المساعدات
    $sql = "SELECT * FROM donations WHERE date BETWEEN '$start_date' AND '$end_date'";
    $result = mysqli_query($con, $sql);

    // إضافة محتوى التقرير إلى PDF
    $pdf->Cell(0, 10, 'تقارير المساعدات من ' . $start_date . ' إلى ' . $end_date, 0, 1, 'C');
    $pdf->Ln(10);
    $pdf->SetFont('dejavusans', 'B', 10);
    $pdf->Cell(30, 10, 'التاريخ', 1);
    $pdf->Cell(50, 10, 'اسم الجمعية', 1);
    $pdf->Cell(50, 10, 'نوع المساعدة', 1);
    $pdf->Cell(30, 10, 'الكمية', 1);
    $pdf->Cell(30, 10, 'عدد المستفيدين', 1);
    $pdf->Ln();

    $pdf->SetFont('dejavusans', '', 10);
    while ($row = mysqli_fetch_assoc($result)) {
        $pdf->Cell(30, 10, $row['date'], 1);
        $pdf->Cell(50, 10, $row['association_name'], 1);
        $pdf->Cell(50, 10, $row['aid_type'], 1);
        $pdf->Cell(30, 10, $row['quantity'], 1);
        $pdf->Cell(30, 10, $row['beneficiaries_count'], 1);
        $pdf->Ln();
    }

    // إخراج ملف PDF
    $pdf->Output('aid_report.pdf', 'I'); // عرض PDF مباشرة في المتصفح
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تقارير المساعدات</title>
    <link rel="stylesheet" href="..\assets\css\adminstyles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
</head>
<body>
<div class="container mt-4">
    <h1 class="text-center">تقارير المساعدات</h1>

    <!-- نموذج إدخال المعلومات -->
    <h3>إدخال بيانات الجمعيات</h3>
    <form method="POST">
        <div class="mb-3">
            <label for="association_name" class="form-label">اسم الجمعية</label>
            <input type="text" name="association_name" id="association_name" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="aid_type" class="form-label">نوع المساعدة</label>
            <input type="text" name="aid_type" id="aid_type" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="quantity" class="form-label">الكمية</label>
            <input type="number" name="quantity" id="quantity" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="beneficiaries_count" class="form-label">عدد المستفيدين</label>
            <input type="number" name="beneficiaries_count" id="beneficiaries_count" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="date" class="form-label">تاريخ المساعدة</label>
            <input type="date" name="date" id="date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="description" class="form-label">وصف إضافي</label>
            <textarea name="description" id="description" class="form-control"></textarea>
        </div>
        <button type="submit" name="submit_donation" class="btn btn-success w-100">إدخال بيانات الجمعيات</button>
    </form>

    <hr>

    <!-- نموذج إنشاء التقرير -->
    <h3>إنشاء تقرير المساعدات</h3>
    <form method="POST">
        <div class="mb-3">
            <label for="start_date" class="form-label">تاريخ البداية</label>
            <input type="date" name="start_date" id="start_date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="end_date" class="form-label">تاريخ النهاية</label>
            <input type="date" name="end_date" id="end_date" class="form-control" required>
        </div>
        <button type="submit" name="submit_report" class="btn btn-primary w-100">إنشاء التقرير</button>
    </form>
</div>
</body>
</html>