
<?php
// require_once('tcpdf_include.php');  
require_once('..\libs\tcpdf\examples\tcpdf_include.php');

$hostname = 'localhost';
$username = 'root';
$password = '';
$dbname = 'assistancedb';

try {
    $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "خطأ في الاتصال: " . $e->getMessage();
    exit;
}

// استعلام للحصول على بيانات المستفيدين مع المساعدات
$sql = "SELECT 
            b.id AS beneficiary_id,
            b.full_name,
            b.id_number,
            a.assistance_type,
            a.pickup_location,
            a.pickup_date
        FROM beneficiaries b
        JOIN assistance_beneficiaries ab ON b.id = ab.beneficiary_id
        JOIN assistance a ON ab.assistance_id = a.id
        WHERE ab.is_received = 0
        ORDER BY b.full_name";
$stmt = $pdo->prepare($sql);
$stmt-> execute();
$records = $stmt->fetchAll(PDO::FETCH_ASSOC);

// إعداد TCPDF
$pdf = new TCPDF();
$pdf->SetCreator(PDF_CREATOR);
$pdf->SetAuthor('Your Name');
$pdf->SetTitle('دعوة لاستلام المساعدة');
$pdf->SetMargins(10, 10, 10);
$pdf->AddPage();

// إضافة عنوان التقرير
$pdf->SetFont('helvetica', 'B', 16);
$pdf->Cell(0, 10, 'دعوة لاستلام المساعدة', 0, 1, 'C');
$pdf->Ln(10);

// إعداد الجدول لعرض بيانات الدعوة
$pdf->SetFont('helvetica', 'B', 12);
$pdf->Cell(40, 10, 'اسم المستفيد', 1, 0, 'C');
$pdf->Cell(50, 10, 'رقم الهاتف', 1, 0, 'C');
$pdf->Cell(50, 10, 'نوع المساعدة', 1, 0, 'C');
$pdf->Cell(50, 10, 'مكان الاستلام', 1, 0, 'C');
$pdf->Cell(50, 10, 'موعد الاستلام', 1, 1, 'C');

// استرجاع البيانات من قاعدة البيانات وطباعتها في الجدول
$pdf->SetFont('helvetica', '', 12);
foreach ($records as $row) {
    $pdf->Cell(40, 10, $row['full_name'], 1, 0, 'C');
    $pdf->Cell(50, 10, $row['id_number'], 1, 0, 'C');
    $pdf->Cell(50, 10, $row['assistance_type'], 1, 0, 'C');
    $pdf->Cell(50, 10, $row['pickup_location'], 1, 0, 'C');
    $pdf->Cell(50, 10, $row['pickup_date'], 1, 1, 'C');
}
$pdf->Output('assistance_invitations.pdf', 'I');
?>







<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Document</title>
</head>
<body>
<form action="generate_invitations.php" method="POST">
    <table>
        <thead>
            <tr>
                <th>اسم المستفيد</th>
                <th>رقم الهاتف</th>
                <th>اختيار للطباعة</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // الاتصال بقاعدة البيانات
            // $hostname = 'localhost';
            // $username = 'root';
            // $password = '';
            // $dbname = 'assistancedb';

            // try {
            //     $pdo = new PDO("mysql:host=$hostname;dbname=$dbname", $username, $password);
            //     $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            // } catch (PDOException $e) {
            //     echo "خطأ في الاتصال: " . $e->getMessage();
            //     exit;
            // }

            // استعلام لعرض جميع المستفيدين مع حالة استلام المساعدة
            $sql = "SELECT id, full_name, id_number FROM beneficiaries WHERE is_received = 0";
            $stmt = $pdo->prepare($sql);
            $stmt->execute();
            $beneficiaries = $stmt->fetchAll(PDO::FETCH_ASSOC);

            foreach ($beneficiaries as $beneficiary) {
                echo '<tr>';
                echo '<td>' . $beneficiary['full_name'] . '</td>';
                echo '<td>' . $beneficiary['id_number'] . '</td>';
                echo '<td><input type="checkbox" name="beneficiaries[]" value="' . $beneficiary['id'] . '"></td>';
                echo '</tr>';
            }
            ?>
        </tbody>
    </table>
    <button type="submit">طباعة الدعوات المحددة</button>
</form>
</body>
</html>