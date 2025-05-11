<?php
include '../includes/db.php';

// إضافة مستفيد جديد
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tent_number = trim($_POST['tent_number']);
    $full_name = trim($_POST['full_name']);
    $id_number = trim($_POST['id_number']);
    $spouse_name = trim($_POST['spouse_name']);
    $spouse_id = trim($_POST['spouse_id']);
    $family_count = intval($_POST['family_count']);
    $residence = trim($_POST['residence']);
    $housing_type = trim($_POST['housing_type']);
    $damage_type = trim($_POST['damage_type']);
    $primary_phone = trim($_POST['primary_phone']);
    $secondary_phone = trim($_POST['secondary_phone']);
    $children_count = intval($_POST['children_count']);

    if (empty($full_name) || empty($id_number) || empty($tent_number) || $family_count <= 0) {
        echo "<script>alert('يجب إدخال جميع البيانات المطلوبة بشكل صحيح!');</script>";
    } else {
        $sqladd = "INSERT INTO beneficiaries (tent_number, full_name, id_number, spouse_name, spouse_id, family_count, residence, 
                    housing_type, damage_type, primary_phone, secondary_phone, children_count)
                    VALUES ('$tent_number', '$full_name', '$id_number', '$spouse_name', '$spouse_id', $family_count, '$residence', 
                    '$housing_type', '$damage_type', '$primary_phone', '$secondary_phone', $children_count)";
        if (mysqli_query($con, $sqladd)) {
            echo "<script>alert('تم تسجيل المستفيد بنجاح!');</script>";
            header("Location: dashboard.php");
            exit;
        } else {
            echo "<script>alert('حدث خطأ أثناء التسجيل: " . mysqli_error($con) . "');</script>";
        }
    }
}

// البحث
$q = isset($_GET['q']) ? $_GET['q'] : '';
$sqlselect = "SELECT * FROM beneficiaries WHERE 
    full_name LIKE '%$q%' OR 
    id_number LIKE '%$q%' OR 
    tent_number LIKE '%$q%'";
$result = mysqli_query($con, $sqlselect);

// استعلام عدد المستفيدين
$sqlBeneficiariesCount = "SELECT COUNT(*) AS total_beneficiaries FROM beneficiaries";
$resultBeneficiaries = mysqli_query($con, $sqlBeneficiariesCount);
$totalBeneficiaries = mysqli_fetch_assoc($resultBeneficiaries)['total_beneficiaries'];

// استعلام عدد المساعدات
$sqlAidsCount = "SELECT COUNT(*) AS total_aids FROM aids";
$resultAids = mysqli_query($con, $sqlAidsCount);
$totalAids = mysqli_fetch_assoc($resultAids)['total_aids'];
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>لوحة التحكم</title>
    <link rel="stylesheet" href="..\assets\css\bootstrap.min.css">
    <script src="..\assets\js\bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css">

    <style>
        thead th {
            color: black; /* تغيير لون النص في رأس الجدول */
        }
    </style>

</head>
<body>
    <div class="container mt-4">
        <h1 class="text-center mb-4">لوحة التحكم</h1>

        <!-- إحصائيات -->
        <div class="row text-center mb-4">
            <div class="col-md-6">
                <div class="card text-white bg-primary mb-3">
                    <div class="card-header">عدد المستفيدين</div>
                    <div class="card-body">
                        <h5 class="card-title"><?= $totalBeneficiaries ?></h5>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card text-white bg-success mb-3">
                    <div class="card-header">عدد المساعدات الموزعة</div>
                    <div class="card-body">
                        <h5 class="card-title"><?= $totalAids ?></h5>
                    </div>
                </div>
            </div>
        </div>


        <!-- شريط البحث -->
        <form method="GET" action="dashboard.php"  class="mb-4">
            <div class="input-group">
                <input type="text" name="q" class="form-control" placeholder="ابحث باستخدام الاسم، رقم الهوية، أو رقم الخيمة" value="<?= htmlspecialchars($q) ?>">
                <button type="submit" class="btn btn-primary">بحث</button>
            </div>
        </form>

        <!-- أزرار الإجراءات -->
        <div  class="text-right mb-3">
            <a class="btn btn-secondary mb-1" href="assistance.php">
                <i class="bi bi-tools"></i> إدارة المساعدات
            </a>
            <a class="btn btn-secondary mb-1" href="view.php">
                <i class="bi bi-eye"></i> عرض بيانات المستفيدين
            </a>
            <a class="btn btn-success mb-1" href="beneficiaries_report.php">
                <i class="bi bi-file-earmark-text"></i> طباعة بيانات المستفيدين
            </a>
            <a class="btn btn-success mb-1" href="export_beneficiaries.php">
                <i class="bi bi-file-earmark-excel"></i> ملف إكسل لبيانات المستفيدين
            </a>
            <a class="btn btn-info mb-1" href="addall.php">
                <i class="bi bi-people"></i> إضافة مساعدة جديدة لمجموعة مستفيدين
            </a>
            <a class="btn btn-info mb-1" href="Print_invitations_ben.php">
                <i class="bi bi-card-text"></i> طباعة دعوات استلام
            </a>

            <a class="btn btn-primary mb-1" href="reports.php">
                <i class="bi bi-file-earmark-pdf"></i>  التقارير اليومية / الأسبوعية / الشهرية
            </a>

            <a class="btn btn-warning mb-1" href="report.php">
                <i class="bi bi-file-earmark-pdf"></i> تقارير المساعدات - الجمعيات
            </a>
            <a href="export_alldata.php" class="btn btn-success">
    <i class="bi bi-file-earmark-excel"></i> تصدير البيانات إلى Excel
</a>
           
        </div>

                <!-- نموذج إضافة مستفيد جديد -->
                <div class="text-right form-section mb-4 p-3 border rounded bg-light">
            <h5 class="text-center mb-4">إضافة مستفيد جديد</h5>
            <form method="POST">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="tent_number" class="form-label">رقم الخيمة</label>
                        <input type="text" name="tent_number" id="tent_number" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="full_name" class="form-label">الاسم الكامل</label>
                        <input type="text" name="full_name" id="full_name" class="form-control" required>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="id_number" class="form-label">رقم الهوية</label>
                        <input type="text" name="id_number" id="id_number" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label for="spouse_name" class="form-label">اسم الزوجة</label>
                        <input type="text" name="spouse_name" id="spouse_name" class="form-control">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="spouse_id" class="form-label">رقم هوية الزوجة</label>
                        <input type="text" name="spouse_id" id="spouse_id" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="family_count" class="form-label">عدد أفراد الأسرة</label>
                        <input type="number" name="family_count" id="family_count" class="form-control" required>
                    </div>
                </div>
                <div class="row mb-3">
                <div class="col-md-6">
                    <label for="residence" class="form-label">مكان الإقامة</label>
                    <input type="text" name="residence" id="residence" class="form-control">
                </div>
                <div class="col-md-6">
                    <label for="housing_type" class="form-label">نوع السكن</label>
                    <select name="housing_type" id="housing_type" class="form-control" required>
                        <option value="">اختر نوع الإقامة</option>
                        <option value="مقيم">مقيم</option>
                        <option value="نازح">نازح</option>
                    </select>
                </div>
            </div>
            <div class="row mb-3">
                <div class="col-md-6">
                    <label for="damage_type" class="form-label">نوع الأضرار</label>
                    <select name="damage_type" id="damage_type" class="form-control" required>
                        <option value="">اختر نوع الضرر</option>
                        <option value="جزئي">جزئي</option>
                        <option value="كلي">كلي</option>
                        <option value="جزئي بليغ">جزئي بليغ</option>
                    </select>
                </div>
                    <div class="col-md-6">
                        <label for="primary_phone" class="form-label">الهاتف الأساسي</label>
                        <input type="text" name="primary_phone" id="primary_phone" class="form-control">
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label for="secondary_phone" class="form-label">الهاتف الثانوي</label>
                        <input type="text" name="secondary_phone" id="secondary_phone" class="form-control">
                    </div>
                    <div class="col-md-6">
                        <label for="children_count" class="form-label">عدد الأطفال</label>
                        <input type="number" name="children_count" id="children_count" class="form-control">
                    </div>
                </div>
                <button type="submit" class="btn btn-primary w-100">إضافة</button>
            </form>
        </div>


        <!-- جدول المستفيدين -->
        <table class="table table-bordered text-center">
            <thead>
                <tr>
                    <th>رقم الخيمة</th>
                    <th>الاسم الكامل</th>
                    <th>رقم الهوية</th>
                    <th>نوع السكن</th>
                    <th>عدد الأفراد</th>
                    <th>آخر مرة استفاد</th>
                    <th>الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['tent_number'] ?? 'N/A') ?></td>
                        <td><?= htmlspecialchars($row['full_name']) ?></td>
                        <td><?= htmlspecialchars($row['id_number']) ?></td>
                        <td><?= htmlspecialchars($row['housing_type']) ?></td>
                        <td><?= htmlspecialchars($row['family_count']) ?></td>
                        <td><?= date('Y-m-d', strtotime($row['created_at'])) ?></td>
                        <td>
                            <a href="edit_beneficiary.php?id=<?= $row['id'] ?>" class="btn btn-warning btn-sm">تعديل</a>
                            <a href="delete_beneficiary.php?id=<?= $row['id'] ?>" class="btn btn-danger btn-sm" onclick="return confirm('هل أنت متأكد من الحذف؟')">حذف</a>
                            <a href="print_beneficiary_report.php?id=<?= $row['id'] ?>" class="btn btn-info btn-sm" target="_blank">
                                <i class="bi bi-printer"></i> طباعة كشف المساعدات
                            </a>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    </div>

    <!-- <script src="..\assets\js\bootstrap.bundle.min.js"></script> -->
</body>
</html>