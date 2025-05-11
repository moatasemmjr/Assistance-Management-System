<?php
include '../includes/db.php';
// require '../includes/tcpdf/tcpdf.php';
require_once('..\libs\tcpdf\tcpdf.php');

// التعامل مع نوع التقرير
$filter = isset($_GET['filter']) ? $_GET['filter'] : 'daily';
$today = date('Y-m-d');

// تحديد المدة الزمنية بناءً على الفلتر
switch ($filter) {
    case 'weekly':
        $start_date = date('Y-m-d', strtotime('-7 days', strtotime($today)));
        break;
    case 'monthly':
        $start_date = date('Y-m-d', strtotime('-30 days', strtotime($today)));
        break;
    default: // daily
        $start_date = $today;
        break;
}

// جلب البيانات من قاعدة البيانات
$sql = "SELECT aids.*, beneficiaries.full_name 
        FROM aids 
        LEFT JOIN beneficiaries ON aids.beneficiary_id = beneficiaries.id
        WHERE aids.aid_date BETWEEN '$start_date' AND '$today'
        ORDER BY aids.aid_date DESC";

$result = mysqli_query($con, $sql);

// إنشاء ملف PDF عند الطلب
if (isset($_GET['generate_pdf'])) {

    $pdf = new TCPDF('P', 'mm', 'A4', 'UTF-8');
    $lg = array();
    $lg['a_meta_charset'] = 'UTF-8';
    $lg['a_meta_dir'] = 'rtl';
    $lg['a_meta_language'] = 'ar';
    $pdf->setLanguageArray($lg);
    $pdf->setRTL(true);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('Aid Distribution System');
    $pdf->SetTitle('تقرير المساعدات');
    $pdf->SetSubject('تقرير المساعدات اليومية / الأسبوعية / الشهرية');
    $pdf->SetHeaderData('', 0, 'تقرير المساعدات', "الفترة: $start_date إلى $today");
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));
    $pdf->SetMargins(10, 20, 10);
    $pdf->SetAutoPageBreak(TRUE, 10);
    $pdf->SetFont('dejavusans', '', 12);
    $pdf->AddPage();

    // عنوان التقرير
    $pdf->Cell(0, 10, "تقرير المساعدات ($filter)", 0, 1, 'C');

    // جدول البيانات
    $tbl = '<table border="1" cellpadding="4">
        <thead>
            <tr>
                <th>#</th>
                <th>اسم المستفيد</th>
                <th>نوع المساعدة</th>
                <th>تاريخ المساعدة</th>
                <th>ملاحظات</th>
            </tr>
        </thead>
        <tbody>';
    $count = 1;
    while ($row = mysqli_fetch_assoc($result)) {
        $tbl .= '<tr>
            <td>' . $count++ . '</td>
            <td>' . $row['full_name'] . '</td>
            <td>' . $row['aid_type'] . '</td>
            <td>' . $row['aid_date'] . '</td>
            <td>' . $row['notes'] . '</td>
        </tr>';
    }
    $tbl .= '</tbody></table>';

    $pdf->writeHTML($tbl, true, false, false, false, '');

    // إخراج ملف PDF
    $pdf->Output('aids_report_' . date('Y-m-d') . '.pdf', 'D');
    exit;
}
?>

<!DOCTYPE html>
<html lang="ar"  dir="rtl">
<head>
    <title>التقارير</title>
    <link rel="stylesheet" href="..\assets\css\bootstrap.min.css">

</head>
<body>
<div class="container mt-4">
    <h1 class="text-center">التقارير اليومية / الأسبوعية / الشهرية</h1>

    <!-- اختيار نوع التقرير -->
    <form method="GET" action="reports.php" class="mb-3">
        <div class="row">
            <div class="col-md-6">
                <select name="filter" class="form-select">
                    <option value="daily" <?= $filter == 'daily' ? 'selected' : '' ?>>تقارير اليوم</option>
                    <option value="weekly" <?= $filter == 'weekly' ? 'selected' : '' ?>>تقارير الأسبوع</option>
                    <option value="monthly" <?= $filter == 'monthly' ? 'selected' : '' ?>>تقارير الشهر</option>
                </select>
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary">عرض</button>
            </div>
            <div class="col-md-2">
                <button type="submit" name="generate_pdf" class="btn btn-success">تصدير PDF</button>
            </div>
        </div>
    </form>

    <!-- عرض المساعدات -->
    <table class="text-right table table-bordered">
        <thead>
            <tr>
                <th>#</th>
                <th>اسم المستفيد</th>
                <th>نوع المساعدة</th>
                <th>تاريخ المساعدة</th>
                <th>ملاحظات</th>
            </tr>
        </thead>
        <tbody>
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php $count = 1; while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $count++ ?></td>
                        <td><?= $row['full_name'] ?></td>
                        <td><?= $row['aid_type'] ?></td>
                        <td><?= $row['aid_date'] ?></td>
                        <td><?= $row['notes'] ?></td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">لا توجد مساعدات</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>
