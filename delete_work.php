<?php 

include_once('connect.php');
$work_id = $_POST['work_id'];

$delete_work_sql = 'DELETE FROM work WHERE id = ?';
$delete_work_time_sql = 'DELETE FROM work_time WHERE work_id = ?';
$delete_work_product_sql = 'DELETE FROM work_product WHERE work_id = ?';
$delete_work_address_sql = 'DELETE FROM work_address WHERE work_id = ?';
$delete_work_employee_sql = 'DELETE FROM work_employee WHERE work_id = ?';

$delete_work_stmt = $conn->prepare($delete_work_sql);
$delete_work_time_stmt = $conn->prepare($delete_work_time_sql);
$delete_work_product_stmt = $conn->prepare($delete_work_product_sql);
$delete_work_address_stmt = $conn->prepare($delete_work_address_sql);
$delete_work_employee_stmt = $conn->prepare($delete_work_employee_sql);

$delete_work_stmt->bind_param('i', $work_id);
$delete_work_time_stmt->bind_param('i', $work_id);
$delete_work_product_stmt->bind_param('i', $work_id);
$delete_work_address_stmt->bind_param('i', $work_id);
$delete_work_employee_stmt->bind_param('i', $work_id);

$delete_work_stmt->execute();
$result = $delete_work_stmt->get_result();

$delete_work_time_stmt->execute();
$result = $delete_work_time_stmt->get_result();

$delete_work_product_stmt->execute();
$result = $delete_work_product_stmt->get_result();

$delete_work_address_stmt->execute();
$result = $delete_work_address_stmt->get_result();

$delete_work_employee_stmt->execute();
$result = $delete_work_employee_stmt->get_result();

header('Location: '.$_POST['redirect_url'].'?success=1');
?>