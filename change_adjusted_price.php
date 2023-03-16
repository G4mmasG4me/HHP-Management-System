<?php 
include_once('connect.php');
include_once('generate_receipt.php');
include_once('console_log.php');


$work_id = $_GET['work_id'];
$new_total_price = $_GET['adjusted_price'];

// calculate new adjustment percentage
$current_total_price_sql = 'SELECT wp.work_id, SUM(wp.quantity * pp.price) AS total_price
FROM work_product wp
INNER JOIN product_price pp ON wp.product_id = pp.product_id
AND pp.created = (
    SELECT MAX(created) 
    FROM product_price pp2 
    WHERE pp.product_id = pp2.product_id
)
WHERE wp.work_id = ?';

$current_total_price_stmt = $conn->prepare($current_total_price_sql);
$current_total_price_stmt->bind_param('i', $_GET['work_id']);
$current_total_price_stmt->execute();
$result = $current_total_price_stmt->get_result();
$current_total_price = $result->fetch_assoc()['total_price'];

$new_adjustment = $new_total_price / $current_total_price;

$change_adjustment_sql = 'UPDATE work SET adjustment = ? WHERE id = ?';
$change_adjustment_stmt = $conn->prepare($change_adjustment_sql);
$change_adjustment_stmt->bind_param('di', $new_adjustment, $work_id);
$status = $change_adjustment_stmt->execute();

echo generate_receipt($work_id);




?>