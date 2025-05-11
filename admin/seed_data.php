<?php
include '../includes/db.php'; // الاتصال بقاعدة البيانات

// دالة لتوليد أرقام عشوائية بطول معين
function generateRandomNumber($length = 9) {
    return str_pad(mt_rand(1, 999999999), $length, '0', STR_PAD_LEFT);
}

// بيانات وهمية باللغة العربية
$firstNames = ["محمد", "أحمد", "يوسف", "علي", "حسن", "عمر", "عبدالله", "رامي", "محمود", "سعيد"];
$lastNames = ["الخطيب", "الصالح", "الدين", "العطار", "الشامي", "النجار", "العلي", "الزيني", "المصري", "البلخي"];
$residences = ["غزة", "خان يونس", "رفح", "النصيرات", "دير البلح"];
$housingTypes = ["مقيم", "نازح"];
$damageTypes = ["كلي", "جزئي", "جزئي بليغ"];

// إدخال 50 سجل وهمي
for ($i = 1; $i <= 100; $i++) {
    $full_name = $firstNames[array_rand($firstNames)] . " " .
                 $firstNames[array_rand($firstNames)] . " " .
                 $lastNames[array_rand($lastNames)] . " " .
                 $lastNames[array_rand($lastNames)];

    $tent_number =  mt_rand(1, 120);
    $id_number = generateRandomNumber(9);
    $spouse_name = $firstNames[array_rand($firstNames)] . " " . $lastNames[array_rand($lastNames)];
    $spouse_id = generateRandomNumber(9);
    $family_count = mt_rand(2, 10);
    $residence = $residences[array_rand($residences)];
    $housing_type = $housingTypes[array_rand($housingTypes)];
    $damage_type = $damageTypes[array_rand($damageTypes)];
    $primary_phone = "059" . mt_rand(1000000, 9999999);
    $secondary_phone = "056" . mt_rand(1000000, 9999999);
    $children_count = mt_rand(0, $family_count - 1); // عدد الأطفال أقل من عدد العائلة

    // استعلام الإدخال
    $sql = "INSERT INTO beneficiaries (
                full_name, tent_number, id_number, spouse_name, spouse_id,
                family_count, residence, housing_type, damage_type,
                primary_phone, secondary_phone, children_count
            ) VALUES (
                '$full_name', '$tent_number', '$id_number', '$spouse_name', '$spouse_id',
                $family_count, '$residence', '$housing_type', '$damage_type',
                '$primary_phone', '$secondary_phone', $children_count
            )";

    // تنفيذ الإدخال
    if (mysqli_query($con, $sql)) {
        echo "✅ تم إدخال السجل رقم $i بنجاح.<br>";
    } else {
        echo "❌ خطأ في السجل رقم $i: " . mysqli_error($con) . "<br>";
    }
}

mysqli_close($con);
?>
