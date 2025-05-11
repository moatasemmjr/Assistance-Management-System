<?php 
include '..\includes\db.php';

// فلترة المساعدات حسب نوع المساعدة
$aid_type_filter = isset($_POST['aid_type_filter']) ? $_POST['aid_type_filter'] : '';
$sqlsel = "SELECT aids.id, aids.aid_type, aids.aid_date, aids.notes, beneficiaries.full_name, beneficiaries.tent_number 
           FROM aids
           INNER JOIN beneficiaries ON aids.beneficiary_id = beneficiaries.id";

if ($aid_type_filter) {
    $sqlsel .= " WHERE aids.aid_type = '$aid_type_filter'";
}

$result = mysqli_query($con, $sqlsel);
$beneficiaries = mysqli_query($con, "SELECT id, full_name FROM beneficiaries");

// إضافة مساعدة جديدة
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['add'])) {
    $beneficiary_id = $_POST['beneficiary_id'];
    $aid_type = $_POST['aid_type'];
    $aid_date = $_POST['aid_date'];
    $notes = $_POST['notes'];

    $sql = "INSERT INTO aids (beneficiary_id, aid_type, aid_date, notes) 
            VALUES ('$beneficiary_id', '$aid_type', '$aid_date', '$notes')";
    if (mysqli_query($con, $sql)) {
        header("Location: assistance.php?msg=added");
    } else {
        echo "Error: " . mysqli_error($con);
    }
}

// البحث
$search_query = '';
if (isset($_POST['search'])) {
    $search_query = $_POST['search_query'];
    $sqlsel .= " WHERE beneficiaries.full_name LIKE '%$search_query%' OR beneficiaries.tent_number LIKE '%$search_query%'";
    $result = mysqli_query($con, $sqlsel);
}

// معرفة المستفيدين الذين لم يستلموا مساعدة معينة
if (isset($_POST['check_non_recipients'])) {
    $aid_type_check = $_POST['aid_type_check'];
    $non_recipients_sql = "
        SELECT beneficiaries.id, beneficiaries.full_name
        FROM beneficiaries
        LEFT JOIN aids ON beneficiaries.id = aids.beneficiary_id AND aids.aid_type = '$aid_type_check'
        WHERE aids.id IS NULL
    ";
    $non_recipients_result = mysqli_query($con, $non_recipients_sql);
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>إدارة المساعدات</title>
    <link rel="stylesheet" href="..\assets\css\adminstyles.css">
    <link rel="stylesheet" href="..\assets\css\bootstrap.min.css">
    <style>
        thead th {
            color: black;  
        }
        .form-section {
            margin-top: 20px;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 5px;
            background-color: #f9f9f9;
        }
        .btn-custom {
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>إدارة المساعدات</h1>
        <a class="btn btn-secondary" href="\assistance\admin\dashboard.php"> الصفحة الرئيسة</a>
    </div>
    <div class="container">
        <!-- فلتر نوع المساعدة -->
        <form method="POST">
            <select name="aid_type_filter" onchange="this.form.submit()" class="form-control">
                <option value="">اختر نوع المساعدة</option>
                <option value="مالية" <?= $aid_type_filter == 'مالية' ? 'selected' : '' ?>>مالية</option>
                <option value="غذائية" <?= $aid_type_filter == 'غذائية' ? 'selected' : '' ?>>غذائية</option>
                <option value="صحية" <?= $aid_type_filter == 'صحية' ? 'selected' : '' ?>>صحية</option>
                <option value="24 لحمة" <?= $aid_type_filter == '24 لحمة' ? 'selected' : '' ?>>24 لحمة</option>
            </select>
        </form>

        <!-- شريط البحث -->
        <form method="POST" class="mt-3">
            <input type="text" name="search_query" placeholder="ابحث باستخدام اسم المستفيد أو رقم الخيمة" class="form-control">
            <button type="submit" name="search" class="btn btn-primary mt-2">بحث</button>
        </form>

        <!-- زر إضافة مساعدة جديدة -->
        <div class="text-right mt-3">
            <a class="btn btn-info" href="addall.php">إضافة مساعدة جديدة لمجموعة مستفدين</a>
            <a class="btn btn-warning" href="Print_invitations_ben.php">طباعة دعوات استلام</a>
            <a class="btn btn-primary" href="aids_and_beneficiaries_dataExcel.php">تصدير ملف Excel</a>
        </div>

        <!-- جدول المساعدات -->
        <table class="table mt-3">
            <thead>
                <tr>
                    <th>رقم الخيمة</th>
                    <th>اسم المستفيد</th>
                    <th>نوع المساعدة</th>
                    <th>تاريخ المساعدة</th>
                    <th>ملاحظات</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody id="aidTableBody">
            <?php if (mysqli_num_rows($result) > 0): ?>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                         <td><?= htmlspecialchars($row['tent_number']) ?></td>
                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                       
                        <td><?= htmlspecialchars($row['aid_type']) ?></td>
                        <td><?= htmlspecialchars($row['aid_date']) ?></td>
                        <td><?= htmlspecialchars($row['notes']) ?></td>
                        <td class="actions">
                            <a href="edit_aid.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">تعديل</a>
                            <a href="delete_aid.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من حذف هذه المساعدة؟')">حذف</a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="6" class="text-center">لا توجد مساعدات</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>

        <!-- نموذج إضافة مساعدة جديدة -->
        <div class=" text-right form-section">
            <h5>إضافة مساعدة جديدة</h5>
            <form method="POST">
                <div class="mb-3">
                    <label for="beneficiary_id" class="form-label">اختر المستفيد</label>
                    <select name="beneficiary_id" id="beneficiary_id" class="form-control" required>
                        <option value="">اختر المستفيد</option>
                        <?php while ($row = mysqli_fetch_assoc($beneficiaries)): ?>
                            <option value="<?= $row['id'] ?>"><?= $row['full_name'] ?></option>
                        <?php endwhile; ?>
                    </select>
                </div>

                <div class="mb-3">
                    <label for="aid_type" class="form-label">نوع المساعدة</label>
                    <input type="text" name="aid_type" id="aid_type" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="aid_date" class="form-label">تاريخ المساعدة</label>
                    <input type="date" name="aid_date" id="aid_date" class="form-control" required>
                </div>
                <div class="mb-3">
                    <label for="notes" class="form-label">ملاحظات</label>
                    <textarea name="notes" id="notes" class="form-control"></textarea>
                </div>
                <button type="submit" name="add" class="btn btn-primary btn-custom">إضافة</button>
            </form>
        </div>

        <!-- معرفة المستفيدين الذين لم يستلموا مساعدة معينة -->
        <form method="POST" class="text-right mt-3">
            <h5>معرفة المستفيدين الذين لم يستلموا مساعدة:</h5>
            <select name="aid_type_check" class="form-control" required>
                <option value="">اختر نوع المساعدة</option>
                <option value="مالية">مالية</option>
                <option value="غذائية">غذائية</option>
                <option value="صحية">صحية</option>
                <option value="24 لحمة">24 لحمة</option>
            </select>
            <button type="submit" name="check_non_recipients" class="btn btn-info mt-2">تحقق</button>
        </form>

        <!-- عرض المستفيدين الذين لم يستلموا -->
        <?php if (isset($non_recipients_result)): ?>
            <h5 class=" text-right mt-3">المستفيدين الذين لم يستلموا المساعدة:</h5>
            <ul>
                <?php while ($non_recipient = mysqli_fetch_assoc($non_recipients_result)): ?>
                    <li class="text-right"><?= htmlspecialchars($non_recipient['full_name']) ?></li>
                <?php endwhile; ?>
            </ul>
        <?php endif; ?>
    </div>

    <script src="..\assets\js\bootstrap.bundle.min.js"></script>
</body>
</html>