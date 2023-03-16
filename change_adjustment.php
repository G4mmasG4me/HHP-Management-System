<?php

include 'connect.php';
include 'generate_receipt.php';
include_once 'console_log.php';

$work_id = $_GET['work_id'];
$changeby = $_GET['changeby'];

$select_sql = 'SELECT adjustment FROM work WHERE id = ?';
$select_stmt = $conn->prepare($select_sql);
$select_stmt->bind_param('i', $work_id);
$select_stmt->execute();
$result = $select_stmt->get_result();

$current_adjustment = $result->fetch_assoc()['adjustment'];

$new_adjustment = round($current_adjustment + ($changeby / 100), 2); // round it 2 places, because we want buttons to only have 1% degree accuracy

$update_sql = 'UPDATE work SET adjustment = ? WHERE id = ?';
$update_stmt = $conn->prepare($update_sql);
$update_stmt->bind_param('di', $new_adjustment, $work_id);
$update_stmt->execute();

echo generate_receipt($work_id);
?>