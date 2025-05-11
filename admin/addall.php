<?php
include '../includes/db.php';

// جلب قائمة المستفيدين
$sqlBeneficiaries = "SELECT id, full_name, tent_number FROM beneficiaries"; // إضافة رقم الخيمة هنا
$result = mysqli_query($con, $sqlBeneficiaries);

$beneficiaries_for_pdf = []; // قائمة المستفيدين للطباعة

// عند إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $aid_type = mysqli_real_escape_string($con, $_POST['aid_type']);
    $aid_date = mysqli_real_escape_string($con, $_POST['aid_date']);
    $pickup_location = mysqli_real_escape_string($con, $_POST['pickup_location']);
    $pickup_datetime = mysqli_real_escape_string($con, $_POST['pickup_datetime']);
    $notes = mysqli_real_escape_string($con, $_POST['notes']);
    $beneficiaries = $_POST['beneficiaries']; // مصفوفة المستفيدين المختارين

    if (!empty($beneficiaries)) {
        foreach ($beneficiaries as $beneficiary_id) {
            $beneficiary_id = intval($beneficiary_id); // تأمين البيانات
            $sqlInsert = "INSERT INTO aids (beneficiary_id, aid_type, aid_date, notes) 
                          VALUES ($beneficiary_id, '$aid_type', '$aid_date', '$notes')";
            mysqli_query($con, $sqlInsert);
            $beneficiaries_for_pdf[] = $beneficiary_id; // إضافة المستفيد للطباعة
        }
        $success_message = "تم تسجيل المساعدة لـ " . count($beneficiaries) . " مستفيد بنجاح.";
    } else {
        $error_message = "يرجى اختيار مستفيد واحد على الأقل.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>تسجيل مساعدات جماعية</title>
    <link rel="stylesheet" href="..\assets\css\styles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <style>
        body {
            background-color: #f4f4f9;
            font-family: Arial, sans-serif;
        }
        .container {
            max-width: 800px;
            margin: auto;
            padding: 20px;
            background: white;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .form-label {
            font-weight: bold;
        }
        .btn-info {
            margin-bottom: 15px;
        }
        .alert {
            margin-bottom: 20px;
        }
        .form-check {
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
<div class="container mt-4">
    <h1>تسجيل مساعدات جماعية</h1>

    <!-- عرض الرسائل -->
    <?php if (isset($success_message)): ?>
        <div class="alert alert-success"><?= $success_message ?></div>
    <?php elseif (isset($error_message)): ?>
        <div class="alert alert-danger"><?= $error_message ?></div>
    <?php endif; ?>

    <!-- نموذج تسجيل المساعدة -->
    <form method="POST">
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
            <label for="aid_date" class="form-label">تاريخ المساعدة</label>
            <input type="date" name="aid_date" id="aid_date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="pickup_location" class="form-label">مكان التسليم</label>
            <input type="text" name="pickup_location" id="pickup_location" class="form-control" placeholder="أدخل مكان التسليم" required>
        </div>
        <div class="mb-3">
            <label for="pickup_datetime" class="form-label">موعد التسليم</label>
            <input type="datetime-local" name="pickup_datetime" id="pickup_datetime" class="form-control" required>
        </div>
        <div class="mb-3">
            <label for="notes" class="form-label">ملاحظات</label>
            <textarea name="notes" id="notes" class="form-control"></textarea>
        </div>
        <div class="mb-3">
            <label for="beneficiaries" class="form-label">اختر المستفيدين</label>
            <button class="btn btn-info" type="button" onclick="selectAll()">تحديد الكل</button>

            <div class="form-check">
                <!-- عرض المستفيدين مع رقم الخيمة -->
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <input type="checkbox" name="beneficiaries[]" value="<?= $row['id'] ?>" id="beneficiary_<?= $row['id'] ?>" class="form-check-input">
                    <label for="beneficiary_<?= $row['id'] ?>" class="form-check-label"><?= htmlspecialchars($row['full_name']) ?> (رقم الخيمة: <?= htmlspecialchars($row['tent_number']) ?>)</label><br>
                <?php endwhile; ?>
            </div>
        </div>
        <button type="submit" class="btn btn-primary w-100">تسجيل المساعدة</button>
    </form>

    <!-- زر طباعة الدعوات -->
    <?php if (!empty($beneficiaries_for_pdf)): ?>
        <form method="POST" action="print_invitations.php" target="_blank">
            <input type="hidden" name="beneficiaries" value="<?= implode(',', $beneficiaries_for_pdf) ?>">
            <input type="hidden" name="aid_type" value="<?= htmlspecialchars($aid_type) ?>">
            <input type="hidden" name="pickup_location" value="<?= htmlspecialchars($pickup_location) ?>">
            <input type="hidden" name="pickup_datetime" value="<?= htmlspecialchars($pickup_datetime) ?>">
            <button type="submit" class="btn btn-success w-100 mt-3">طباعة كشف الدعوات</button>
        </form>
    <?php endif; ?>
</div>

<script>
    function selectAll() {
        const checkboxes = document.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => checkbox.checked = !checkbox.checked);
    }
</script>
</body>
</html>