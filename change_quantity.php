<?php 

// need to check if quantity equals 0, then remove, go through all cases
// do a test case

include 'connect.php';
include 'generate_receipt.php';

$product_id = $_GET['product_id'];
$work_id = $_GET['work_id'];
$changeby = $_GET['changeby'];

$amount_sql = 'SELECT quantity FROM work_product WHERE product_id = ? AND work_id = ?';
$update_sql = 'UPDATE work_product SET quantity = quantity + ? WHERE product_id = ? AND work_id = ?';
$remove_sql = 'DELETE FROM work_product WHERE product_id = ? AND work_id = ?';

$amount_stmt = $conn->prepare($amount_sql);
$update_stmt = $conn->prepare($update_sql);
$remove_stmt = $conn->prepare($remove_sql);

$amount_stmt->bind_param('ii', $product_id, $work_id);
$amount_stmt->execute();
$result = $amount_stmt->get_result();
if ($result->num_rows==1) {
  $quantity = ($result->fetch_assoc())['quantity'];
  $new_quantity = $quantity + $changeby;
  if ($new_quantity > 0) {
    // delete product from receipt
    $update_stmt->bind_param('iii', $changeby, $product_id, $work_id);
    $update_stmt->execute();
  }
  else {
    $remove_stmt = $conn->prepare($remove_sql);
    $remove_stmt->bind_param('ii', $product_id, $work_id);
    $remove_stmt->execute();;
  }
}
elseif ($result->num_rows>1) {
  // combine quantity of rows and edit db
}
elseif ($result->num_rows==0 && $changeby == 1) {
  // add to db
}

$update_stmt->bind_param('iii', $changeby, $product_id, $work_id);

echo generate_receipt($work_id);

?>