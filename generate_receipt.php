<?php 

include_once 'connect.php';
include_once 'console_log.php';

$receipt_html = '
<h1>Receipt</h1>
<div class="products">
  %s
</div>
<input class="search-bar" type="text" placeholder="Search For Products..." onchange="searchProduct(this)">
<div class="search-results">

</div>
<div class="pricing">
  %s
</div>
<div class="buttons">
  <a href="receipt.php?work_id=%s">Receipt</a> 
</div>';

$receipt_product_html = '
  <div class="product">
    <input type="hidden" name="product_id" value="%s">
    <img class="product-img" src="%s">
    <div class="info">
      <h1>%s</h1>
      <p1>%s</p1>
    </div>
    <button class="minus" onclick="change_quantity(this.parentElement, -1)">-</button>
    <div class="amount">%s</div>
    <button class="add" onclick="change_quantity(this.parentElement, 1)">+</button>
    <div class="price">£%s</div>
  </div>';

$receipt_pricing_html = '
  <div class="left">
    <div>
      <label for="adjustment">Adjustment (%%):</label>
    </div>
    <div class="adjustment-container">
      <button onclick="change_adjustment(this.parentElement, -1)">-</button>
      <input type="number" name="adjustment" value="%s" onchange="change_adjustment_exact(this)">
      <button onclick="change_adjustment(this.parentElement, 1)">+</button>
    </div>
  </div>
  <div class="right">
    <div>
      <label for="total_price">Total Price (£):</label>
      <input type="number" name="total_price" value="%s" readonly>
    </div>
    <div>
      <label for="adjusted_price">Adjusted Price (£):</label>
      <input type="number" name="adjusted_price" value="%s" onchange="change_adjusted_price(this)">
    </div>
  </div>';



$product_sql = 'SELECT wp.product_id, p.name, p.description, pp.price, wp.quantity 
FROM work_product wp 
INNER JOIN product p ON wp.product_id = p.id 
INNER JOIN product_price pp ON wp.product_id = pp.product_id
AND pp.created = (
    SELECT MAX(created) 
    FROM product_price pp2 
    WHERE pp.product_id = pp2.product_id
)
WHERE wp.work_id = ?';
$product_stmt = $conn->prepare($product_sql);
$adjustment_sql = 'SELECT adjustment FROM work WHERE work.id = ?';
$adjustment_stmt = $conn->prepare($adjustment_sql);

function generate_receipt($work_id) {
  global $product_stmt, $adjustment_stmt, $receipt_html, $receipt_product_html, $receipt_pricing_html;

  $receipt_output = '';
  $receipt_product_output = '';
  $receipt_pricing_output = '';
  $total_price = 0;

  $product_stmt->bind_param('i', $work_id);
  $product_stmt->execute();
  $result = $product_stmt->get_result();
  
  if ($result->num_rows>0) {
    while ($product_row = $result->fetch_assoc()) {
      $product_id = $product_row['product_id'];
      $quantity = $product_row['quantity'];
      $name = $product_row['name'];
      $description = $product_row['description'];
      $price = $product_row['price'];

      $files = glob('product_img/'.$product_id.'.*');

      $img = $files ? $files[0] : 'https://picsum.photos/64/64'; // if product has img, then set that, else set default
      $receipt_product_output .= sprintf($receipt_product_html, $product_id, $img, $name, $description, $quantity, $price * $quantity);
      $total_price += $price * $quantity;
    }
  }

  $adjustment_stmt->bind_param('i', $work_id);
  $adjustment_stmt->execute();
  $result = $adjustment_stmt->get_result();
  $adjustment = ($result->fetch_assoc())['adjustment'];

  $receipt_pricing_output = sprintf($receipt_pricing_html, round(($adjustment * 100)-100, 2), $total_price, round($total_price * $adjustment, 2));
  $receipt_output = sprintf($receipt_html, $receipt_product_output, $receipt_pricing_output, $work_id);
  return $receipt_output;
}
?>