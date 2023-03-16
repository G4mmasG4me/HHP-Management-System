<?php 

include 'connect.php';

$address_html = '
<button class="search-result" type="button" onclick="add_address(this)">
  <div class="info">
    <p>%s</p>
    <p>%s</p>
    <p>%s</p>
    <p>%s</p>
  </div>
  <input type="hidden" value="%s" name="address_id"> 
</button>';
$address_output = '';

$customer_sql = 'SELECT address.id AS id, address.line_1 AS line_1, address.line_2 AS line_2, address.postcode AS postcode, address.city AS city
FROM address 
WHERE address.line_1 LIKE ? OR address.line_2 LIKE ? OR address.postcode LIKE ? OR address.city LIKE ?';
$q = '%'.$_GET['q'].'%';

$stmt = $conn->prepare($customer_sql);
$stmt->bind_param('ssss', $q, $q, $q, $q);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $id = $row['id'];
    $line_1 = $row['line_1'];
    $line_2 = $row['line_2'];
    $postcode = $row['postcode'];
    $city = $row['city'];
    $address_output .= sprintf($address_html, $line_1, $line_2, $postcode, $city, $id);
  }
}
else {
  $address_output = 'No Address Found';
}
echo $address_output;
?>