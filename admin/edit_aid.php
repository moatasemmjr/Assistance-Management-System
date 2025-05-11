<?php
include '../includes/db.php';

$id = intval($_GET['id']);
$sql = "SELECT * FROM aids WHERE id = $id";
$result = mysqli_query($con, $sql);
$aid = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $aid_type = $_POST['aid_type'];
    $aid_date = $_POST['aid_date'];
    $notes = $_POST['notes'];

    $sql = "UPDATE aids SET aid_type = '$aid_type', aid_date = '$aid_date', notes = '$notes' WHERE id = $id";
    if (mysqli_query($con, $sql)) {
        header("Location: assistance.php?msg=updated");
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <title>تعديل مساعدة</title>

    <link rel="stylesheet" href="..\assets\css\styles.css">
    <link rel="stylesheet" href="..\assets\css\bootstrap.min.css">

    <!-- <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">     -->
</head>
<body>
<div class="container mt-4">
    <h1 class="text-center">تعديل مساعدة</h1>
    <form method="POST">
        <div class="mb-3">
            <label for="aid_type" class="form-label">نوع المساعدة</label>
            <select name="aid_type" id="aid_type" class="form-control">
                <option value="مالية" <?= $aid['aid_type'] == 'مالية' ? 'selected' : '' ?>>مالية</option>
                <option value="غذائية" <?= $aid['aid_type'] == 'غذائية' ? 'selected' : '' ?>>غذائية</option>
                <option value="صحية" <?= $aid['aid_type'] == 'صحية' ? 'selected' : '' ?>>صحية</option>
            </select>
        </div>
        <div class="mb-3">
            <label for="aid_date" class="form-label">تاريخ المساعدة</label>
            <input type="date" name="aid_date" id="aid_date" value="<?= $aid['aid_date'] ?>" class="form-control">
        </div>
        <div class="mb-3">
            <label for="notes" class="form-label">ملاحظات</label>
            <textarea name="notes" id="notes" class="form-control"><?= $aid['notes'] ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">تحديث</button>
    </form>
</div>
</body>
</html>
