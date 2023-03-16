<?php 

include 'connect.php';
include 'console_log.php';
include 'generate_receipt.php';

$product_id = $_GET['product_id'];
$work_id = $_GET['work_id'];

$amount_sql = 'SELECT id, quantity FROM work_product WHERE product_id = ? AND work_id = ?';
$update_sql = 'UPDATE work_product SET quantity = quantity + 1 WHERE id = ?';
$insert_sql = 'INSERT INTO work_product (work_id, product_id, quantity) VALUES (?,?,1)';

$amount_stmt = $conn->prepare($amount_sql);
$amount_stmt->bind_param('ii', $product_id, $work_id);
$amount_stmt->execute();

$result = $amount_stmt->get_result();

if ($result->num_rows==1) { // if one already in db, adds 1 to current row
  console_log('already one in db');

  // get work_product_id
  $work_product_id = ($result->fetch_assoc())['id'];

  // prepare and execute statement
  $update_stmt = $conn->prepare($update_sql);
  $update_stmt->bind_param('i', $work_product_id);
  $update_stmt->execute();
}
elseif ($result->num_rows==0) {
  console_log('none in db');
  // add to db
  $insert_stmt = $conn->prepare($insert_sql);
  $insert_stmt->bind_param('ii', $work_id, $product_id);
  $insert_stmt->execute();
}


echo generate_receipt($work_id);
?>