<?php
include '../includes/db.php';

$id = intval($_GET['id']);
$sqldel = "DELETE FROM aids WHERE id = $id";
if (mysqli_query($con, $sqldel)) {
    header("Location: assistance.php?msg=deleted");
}
?>