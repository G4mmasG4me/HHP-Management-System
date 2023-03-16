<?php 

include 'connect.php';

$logo_html = '<img src="https://hothouseproperties.co.uk/wp-content/uploads/2022/07/HotHouse_Improvementslogo-1.png">';
$company_html = '
<div>Hot House Properties</div>
<div>Address 123</div>
<div>Postcode</div>
<div>City</div>';

$company_output = $company_html;

$customer_html = '
<div>%s</div>
<div>%s</div>
<div>%s</div>
<div>%s</div>
<div>%s</div>
<div>%s</div>
';

$work_html = '
<div>%s</div>
<div>%s</div>
<div>Work Type: </div>
<div>%s</div>
<div>%s</div>';

$product_html = '
<div class="product">
  <div>%s</div>
  <div>%s</div>
  <div>£%s</div>
  <div>£%s</div>
</div>';

$pricing_html = '
<div>Sub Total: £%s</div>
<div>VAT: £%s</div>
<div>Amount Due: £%s</div>';

$receipt_html = '
<head>
  <link rel="stylesheet" href="styles/default.css">
  <link rel="stylesheet" href="styles/receipt.css">
</head>
<body>
  <div class="content">
    <div class="row">
      <div class="logo">%s</div>
    </div>
    <div class="row">
      <div class="company">%s</div>
    </div>

    <div class="row">
      <div class="work-details">
        <div class="left">
          <div class="customer">%s</div>
        </div>
        <div class="right">
          <div class="work">%s</div>
        </div>
      </div>
    </div>

    <div class="row">
      <div class="products">%s</div>
    </div>
    <div class="row">
      <div class="pricing-container">
        <div class="pricing">%s</div>
      </div>
    </div>
  </div>
</body>';

$work_id = $_GET['work_id'];

$work_details_sql = 'SELECT work.title AS work_title, work.description AS work_description, work.adjustment AS adjustment, work_type.name AS work_type_name, work_type.description AS work_type_description, CONCAT_WS(" ", customer.first_name, customer.last_name) AS name, customer.email AS email, CONCAT_WS(", ", customer.mobile_phone, customer.home_phone) AS phones, CONCAT_WS(", ", address.line_1, address.line_2) AS address_line, address.postcode AS postcode, address.city AS city
FROM work 
INNER JOIN work_type ON work_type.id = work.work_type_id
INNER JOIN customer ON customer.id = work.customer_id 
INNER JOIN work_address on work_address.work_id = work.id
INNER JOIN address on address.id = work_address.address_id
WHERE work.id = ?';

$work_details_stmt = $conn->prepare($work_details_sql);
$work_details_stmt->bind_param('i', $work_id);
$work_details_stmt->execute();
$result = $work_details_stmt->get_result();
$row = $result->fetch_assoc();
$work_title = $row['work_title'];
$work_description = $row['work_description'];
$work_type_name = $row['work_type_name'];
$work_type_description = $row['work_type_description'];
$adjustment = $row['adjustment'];
$name = $row['name'];
$phones = $row['phones'];
$email = $row['email'];
$address_line = $row['address_line'];
$address_postcode = $row['postcode'];
$address_city = $row['city'];

$customer_output = sprintf($customer_html, $name, $email, $phones, $address_line, $address_postcode, $address_city);
$work_output = sprintf($work_html, $work_title, $work_description, $work_type_name, $work_type_description);


$products_sql = 'SELECT p.name, p.description, wp.quantity, pp.price
FROM work_product wp
INNER JOIN product_price pp ON wp.product_id = pp.product_id
AND pp.created = (
    SELECT MAX(created) 
    FROM product_price pp2 
    WHERE pp.product_id = pp2.product_id
)
INNER JOIN product p ON wp.product_id = p.id
WHERE wp.work_id = ?';
$products_stmt = $conn->prepare($products_sql);
$products_stmt->bind_param('i', $work_id);
$products_stmt->execute();
$result = $products_stmt->get_result();

$total_price = 0;
$product_output = '<div class="product"><div>Product Name</div><div>Quantity</div><div>Single Unit Price</div><div>Total Price</div></div>';
while ($row = $result->fetch_assoc()) {
  $product_name = $row['name'];
  $product_description = $row['description'];
  $quantity = $row['quantity'];
  $price = $row['price'];
  $total_price += $price * $adjustment * $quantity;

  $product_output .= sprintf($product_html, $product_name, $quantity, round($price * $adjustment, 2), round($price * $adjustment * $quantity, 2));
}

$logo_output = $logo_html;

$pricing_output = sprintf($pricing_html, round($total_price, 2), round($total_price*0.2, 2), round($total_price*1.2, 2));

$receipt = sprintf($receipt_html, $logo_output, $company_output, $customer_output, $work_output, $product_output, $pricing_output);

echo $receipt;
?>