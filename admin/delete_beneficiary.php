<?php
include '../includes/db.php';

$id = intval($_GET['id']);
$sqldel = "DELETE FROM beneficiaries WHERE id = $id";

if (mysqli_query($con, $sqldel)) {
    // header("Location: beneficiaries.php?msg=deleted");
    header("Location: dashboard.php?msg=deleted");
}
?>
