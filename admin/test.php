
<?php
include '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $full_name = $_POST['full_name'];
    $id_number = $_POST['id_number'];
    $spouse_name = $_POST['spouse_name'];
    $spouse_id = $_POST['spouse_id'];
    $family_count = $_POST['family_count'];
    $residence = $_POST['residence'];
    $housing_type = $_POST['housing_type'];
    $damage_type = $_POST['damage_type'];
    $primary_phone = $_POST['primary_phone'];
    $secondary_phone = $_POST['secondary_phone'];
    $children_count = $_POST['children_count'];

    $sql = "INSERT INTO beneficiaries (full_name, id_number, spouse_name, spouse_id, family_count, residence, 
            housing_type, damage_type, primary_phone, secondary_phone, children_count)
            VALUES ('$full_name', '$id_number', '$spouse_name', '$spouse_id', $family_count, '$residence', 
            '$housing_type', '$damage_type', '$primary_phone', '$secondary_phone', $children_count)";
    
    if (mysqli_query($con, $sql)) {
        header("Location: beneficiaries.php?msg=added");
    } else {
        echo "Error: " . mysqli_error($con);
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <title>إضافة مستفيد</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="text-center">إضافة مستفيد</h1>
        <form method="POST">
            <!-- الحقول مع تعليقات -->
            <div class="mb-3">
                <label for="full_name">الاسم الكامل</label>
                <input type="text" name="full_name" class="form-control" required>
            </div>
            <!-- باقي الحقول مشابهة -->
            <button type="submit" class="btn btn-primary">إضافة</button>
        </form>
    </div>
</body>
</html>

---

2. كود العرض (View)

عرض المستفيدين:

ملف beneficiaries.php:

<?php
include '../includes/db.php';
$sql = "SELECT * FROM beneficiaries";
$result = mysqli_query($con, $sql);
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <title>إدارة المستفيدين</title>
    <link rel="stylesheet" href="../assets/css/bootstrap.min.css">
</head>
<body>
    <div class="container">
        <h1 class="text-center">إدارة المستفيدين</h1>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>الاسم الكامل</th>
                    <th>رقم الهوية</th>
                    <th>عدد الأفراد</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= $row['full_name'] ?></td>
                        <td><?= $row['id_number'] ?></td>
                        <td><?= $row['family_count'] ?></td>
                        <td>
                            <a href="edit_beneficiary.php?id=<?= $row['id'] ?>" class="btn btn-warning">تعديل</a>
                            <a href="delete_beneficiary.php?id=<?= $row['id'] ?>" class="btn btn-danger">حذف</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>
</body>
</html>



