<?php
include '..\includes\db.php';
// select php
$sqlselect = "SELECT * FROM beneficiaries";
$result = mysqli_query($con, $sqlselect);

// البحث
$q = isset($_GET['q']) ? $_GET['q'] : '';
$sqlselect = "SELECT * FROM beneficiaries WHERE 
    full_name LIKE '%$q%' OR 
    id_number LIKE '%$q%' OR 
    tent_number LIKE '%$q%'";
$result = mysqli_query($con, $sqlselect);

?>


<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>بيانات المستفيدين    </title>
    <link rel="stylesheet" href="..\assets\css\adminstyles.css">
    <link rel="stylesheet" href="..\assets\css\bootstrap.min.css">
    

    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css"> -->

</head>
<body>
    <div class="header">
        <h1>بيانات المستفيدين</h1>
        <a  class="btn btn-secondary" href="\assistance\admin\dashboard.php"> الصفحة الرئيسة</a>
        <a  class="btn btn-success" href="beneficiaries_report.php">طباعة بيانات المستفدين</a>
        <a class="btn btn-success" href="export_beneficiaries.php">ملف اكسل لبيانات المستفيدين</a>

    </div>

   
    <div class="container">
           <!-- شريط البحث -->
      <form method="GET" action="view.php" class="mb-4">
            <div class="input-group">
                <input type="text" name="q" class=" mb-4 form-control" placeholder="ابحث باستخدام الاسم، رقم الهوية، أو رقم الخيمة" value="<?= htmlspecialchars($q) ?>">
                <button type="submit" class="btn btn-primary">بحث</button>
            </div>
        </form>
        <table>
            <thead>
                <tr>
                    <!-- <th>#</th> -->
                    <th>رقم الخيمة</th>
                    <th>الاسم الكامل</th>
                    <th>رقم الهوية</th>
                    <th>اسم الزوجة</th>
                    <th>رقم هوية الزوجة</th>
                    <th>عدد الافراد</th>
                    <th>مكان السكن</th>
                    <th>نوع السكن</th>
                    <th>رقم الجوال</th>
                    <th>رقم الجوال البديل</th>
                    <th>تعديل</th>
                </tr>
            </thead>
            <tbody>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
            <td><?= htmlspecialchars($row['tent_number'] ?? 'مقيم') ?></td>

                        <td><?= $row['full_name'] ?></td>
                        <td><?= $row['id_number'] ?></td>
    <td><?= $row['spouse_name'] ?></td>
    <td><?= $row['spouse_id'] ?></td>
    <td><?= $row['family_count'] ?></td>
    <td><?= $row['residence'] ?></td>
    <td><?= $row['housing_type'] ?></td>
    <td><?= $row['primary_phone'] ?></td>
    <td><?= $row['secondary_phone'] ?></td>
                    
    <td class="actions">
                    <a href="edit_beneficiary.php?id=<?= $row['id'] ?>"  class="edit btn btn-warning">تعديل</a>
                    </td>
                   
                </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div >

    <?php
include '..\includes\footer.php';
?>
</body>
</html>

