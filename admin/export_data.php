<?php
include '../includes/db.php';

 
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $selected_tables = $_POST['tables'] ?? []; 
    $selected_columns = $_POST['columns'] ?? []; 

    if (!empty($selected_tables) && !empty($selected_columns)) {
        // إعداد ملف Excel
        header("Content-Type: application/vnd.ms-excel");
        header("Content-Disposition: attachment; filename=exported_data.xls");
        echo "<table border='1'>";

        foreach ($selected_tables as $table) {
            echo "<tr><th colspan='" . count($selected_columns) . "'>جدول: " . htmlspecialchars($table) . "</th></tr>";

            
            $columns = implode(',', array_map('htmlspecialchars', $selected_columns));
            $sql = "SELECT $columns FROM $table";
            $result = mysqli_query($con, $sql);

          
            echo "<tr>";
            foreach ($selected_columns as $column) {
                echo "<th>" . htmlspecialchars($column) . "</th>";
            }
            echo "</tr>";

             
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                foreach ($selected_columns as $column) {
                    echo "<td>" . htmlspecialchars($row[$column]) . "</td>";
                }
                echo "</tr>";
            }
        }

        echo "</table>";
        exit;
    } else {
        $error_message = "يرجى اختيار جدول وعمود واحد على الأقل.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="..\assets\css\adminstyles.css">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">

    <title>استخراج البيانات</title>
</head>
<body>
<div class="container mt-4">
    <h1 class="text-center">استخراج البيانات كملف Excel</h1>

    <!-- عرض رسالة الخطأ -->
    <?php if (isset($error_message)): ?>
        <div class="alert alert-danger"><?= $error_message ?></div>
    <?php endif; ?>

 
    <form method="POST">
        <div class="mb-3">
            <label for="tables" class="form-label">اختر الجداول</label>
            <div class="form-check">
                <input type="checkbox" name="tables[]" value="beneficiaries" id="table_beneficiaries" class="form-check-input">
                <label for="table_beneficiaries" class="form-check-label">المستفيدين</label><br>
                <input type="checkbox" name="tables[]" value="aids" id="table_aids" class="form-check-input">
                <label for="table_aids" class="form-check-label">المساعدات</label><br>
            </div>
        </div>
        <div class="mb-3">
            <label for="columns" class="form-label">اختر الأعمدة</label>
            <div class="form-check">
                <!-- أعمدة المستفيدين -->
                <input type="checkbox" name="columns[]" value="id" id="column_id" class="form-check-input">
                <label for="column_id" class="form-check-label">ID</label><br>
                <input type="checkbox" name="columns[]" value="full_name" id="column_full_name" class="form-check-input">
                <label for="column_full_name" class="form-check-label">الاسم الكامل</label><br>
                <input type="checkbox" name="columns[]" value="id_number" id="column_id_number" class="form-check-input">
                <label for="column_id_number" class="form-check-label">رقم الهوية</label><br>
                <input type="checkbox" name="columns[]" value="residence" id="column_residence" class="form-check-input">
                <label for="column_residence" class="form-check-label">مكان السكن</label><br>
                <!-- أعمدة المساعدات -->
                <input type="checkbox" name="columns[]" value="aid_type" id="column_aid_type" class="form-check-input">
                <label for="column_aid_type" class="form-check-label">نوع المساعدة</label><br>
                <input type="checkbox" name="columns[]" value="aid_date" id="column_aid_date" class="form-check-input">
                <label for="column_aid_date" class="form-check-label">تاريخ المساعدة</label><br>
                <input type="checkbox" name="columns[]" value="notes" id="column_notes" class="form-check-input">
                <label for="column_notes" class="form-check-label">ملاحظات</label><br>
            </div>
        </div>
        <button type="submit" class="btn btn-primary w-100">تنزيل الملف</button>
    </form>
</div>
</body>
</html>
