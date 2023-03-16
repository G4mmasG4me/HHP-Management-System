<?php 

include 'connect.php';
include 'generate_receipt.php';

$adjustment = $_GET['adjustment'];
$work_id = $_GET['work_id'];

$adjustment = (($adjustment + 100) / 100);

$update_sql = 'UPDATE work SET adjustment = ? WHERE id = ?';
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param('di', $adjustment, $work_id);
$update_stmt->execute();

echo generate_receipt($work_id);
?>