<?php
include '../includes/db.php'; 
 
$id = intval($_GET['id']);
$sqlSelect = "SELECT * FROM beneficiaries WHERE id = $id";
$result = mysqli_query($con, $sqlSelect);

// التحقق من وجود المستفيد
if (!$result || mysqli_num_rows($result) == 0) {
    die("لا يوجد مستفيد بهذا الرقم.");
}

$row = mysqli_fetch_assoc($result); // استخراج البيانات

// عند إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // استقبال البيانات مع التأمين
    $tent_number = mysqli_real_escape_string($con, $_POST['tent_number']);
    $full_name = mysqli_real_escape_string($con, $_POST['full_name']);
    $id_number = mysqli_real_escape_string($con, $_POST['id_number']);
    $spouse_name = mysqli_real_escape_string($con, $_POST['spouse_name']);
    $spouse_id = mysqli_real_escape_string($con, $_POST['spouse_id']);
    $family_count = intval($_POST['family_count']);
    $residence = mysqli_real_escape_string($con, $_POST['residence']);
    $housing_type = mysqli_real_escape_string($con, $_POST['housing_type']);
    $damage_type = mysqli_real_escape_string($con, $_POST['damage_type']);
    $primary_phone = mysqli_real_escape_string($con, $_POST['primary_phone']);
    $secondary_phone = mysqli_real_escape_string($con, $_POST['secondary_phone']);
    $children_count = intval($_POST['children_count']);

    // استعلام التحديث
    $sqlUpdate = "
        UPDATE beneficiaries SET 
        tent_number = '$tent_number',
        full_name = '$full_name',
        id_number = '$id_number',
        spouse_name = '$spouse_name',
        spouse_id = '$spouse_id',
        family_count = $family_count,
        residence = '$residence',
        housing_type = '$housing_type',
        damage_type = '$damage_type',
        primary_phone = '$primary_phone',
        secondary_phone = '$secondary_phone',
        children_count = $children_count
        WHERE id = $id
    ";

    // تنفيذ التحديث
    if (mysqli_query($con, $sqlUpdate)) {
        header("Location: dashboard.php?msg=updated");
        exit;
    } else {
        $error_message = "خطأ أثناء التحديث: " . mysqli_error($con);
    }
}
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تحديث بيانات مستفيد</title>
    <link rel="stylesheet" href="..\assets\css\bootstrap.min.css">
    <link rel="stylesheet" href="..\assets\css\styles.css">

    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        .header {
            background-color: #007bff;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 8px 8px 0 0;
        }
        h1 {
            margin: 0;
            font-size: 24px;
        }
        .form-label {
            font-weight: bold;
        }
        .btn-primary {
            background-color: #007bff;
            border: none;
        }
        .btn-primary:hover {
            background-color: #0056b3;
        }
        .alert {
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <header class="header">
        <h1>نظام توزيع المساعدات - إدارة المساعدات</h1>
    </header>

    <div class="container mt-4">
        <h2 class="text-center">تحديث بيانات المستفيد</h2>

        <!-- عرض رسالة الخطأ إن وجدت -->
        <?php if (isset($error_message)): ?>
            <div class="alert alert-danger"><?= $error_message ?></div>
        <?php endif; ?>

        <!-- نموذج التعديل -->
        <form method="POST">
        <div  class="text-right mb-3">
            <div class="mb-3 ">
                <label for="tent_number" class="form-label">رقم الخيمة</label>
                <input type="text" name="tent_number" id="tent_number" class="form-control" value="<?= htmlspecialchars($row['tent_number']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="full_name" class="form-label">الاسم الكامل</label>
                <input type="text" name="full_name" class="form-control" id="full_name" value="<?= $row['full_name'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="id_number" class="form-label">رقم الهوية</label>
                <input type="text" name="id_number" class="form-control" id="id_number" value="<?= $row['id_number'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="spouse_name" class="form-label">اسم الزوجة</label>
                <input type="text" name="spouse_name" class="form-control" id="spouse_name" value="<?= $row['spouse_name'] ?>">
            </div>
            <div class="mb-3">
                <label for="spouse_id" class="form-label">رقم هوية الزوجة</label>
                <input type="text" name="spouse_id" class="form-control" id="spouse_id" value="<?= $row['spouse_id'] ?>">
            </div>
            <div class="mb-3">
                <label for="family_count" class="form-label">عدد الأفراد</label>
                <input type="number" name="family_count" class="form-control" id="family_count" value="<?= $row['family_count'] ?>" required>
            </div>
            <div class="mb-3">
                <label for="residence" class="form-label">مكان السكن</label>
                <input type="text" name="residence" class="form-control" id="residence" value="<?= $row['residence'] ?>">
            </div>
            <div class="mb-3">
                <label for="housing_type" class="form-label">نوع السكن</label>
                <select name="housing_type" id="housing_type" class="form-control" required>
                    <option value="مقيم" <?= $row['housing_type'] == 'مقيم' ? 'selected' : '' ?>>مقيم</option>
                    <option value="نازح" <?= $row['housing_type'] == 'نازح' ? 'selected' : '' ?>>نازح</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="damage_type" class="form-label">نوع الضرر</label>
                <select name="damage_type" id="damage_type" class="form-control" required>
                    <option value="كلي" <?= $row['damage_type'] == 'كلي' ? 'selected' : '' ?>>كلي</option>
                    <option value="جزئي" <?= $row['damage_type'] == 'جزئي' ? 'selected' : '' ?>>جزئي</option>
                    <option value="جزئي بليغ" <?= $row['damage_type'] == 'جزئي بليغ' ? 'selected' : '' ?>>جزئي بليغ</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="primary_phone" class="form-label">رقم الجوال الأول</label>
                <input type="text" name="primary_phone" class="form-control" id="primary_phone" value="<?= $row['primary_phone'] ?>">
            </div>
            <div class="mb-3">
                <label for="secondary_phone" class="form-label">رقم الجوال البديل</label>
                <input type="text" name="secondary_phone" class="form-control" id="secondary_phone" value="<?= $row['secondary_phone'] ?>">
            </div>
            <div class="mb-3">
                <label for="children_count" class="form-label">عدد الأفراد أقل من 10 سنوات</label>
                <input type="number" name="children_count" class="form-control" id="children_count" value="<?= $row['children_count'] ?>">
            </div>
            <button type="submit" class="btn btn-primary w-100">تحديث</button>
        </form>
    </div>
</body>
</html>