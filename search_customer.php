<?php 

include 'connect.php';

$customer_html = '
<button class="search-result" type="button" onclick="add_customer(this)">
  <div class="info">
    <p>%s</p>
    <p>%s</p>
    <p>%s</p>
  </div>
  <input type="hidden" value="%s" name="customer_id"> 
</button>';
$customer_output = '';

$customer_sql = 'SELECT customer.id AS id, customer.title AS title, CONCAT_WS(" ", customer.title, customer.first_name, customer.last_name) AS name, customer.email AS email, CONCAT_WS(", ", customer.mobile_phone, customer.home_phone) AS phones
FROM customer 
WHERE customer.first_name LIKE ? OR customer.last_name LIKE ? OR customer.email LIKE ? OR customer.mobile_phone LIKE ? OR customer.home_phone LIKE ?';
$q = '%'.$_GET['q'].'%';

$stmt = $conn->prepare($customer_sql);
$stmt->bind_param('sssss', $q, $q, $q, $q, $q);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $id = $row['id'];
    $name = $row['name'];
    $email = $row['email'];
    $phones = $row['phones'];
    $customer_output .= sprintf($customer_html, $name, $email, $phones, $id);
  }
}
else {
  $customer_output = 'No Customers Found';
}
echo $customer_output;
?>