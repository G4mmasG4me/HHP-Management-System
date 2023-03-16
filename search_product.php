<?php 

$search_product_html = '
  <div class="product">
    <input type="hidden" name="product_id" value="%s">
    <img class="product-img" src="%s">
    <div class="info">
      <h1>%s</h1>
      <p1>%s</p1>
    </div>
    <div class="price">
      £%s
    </div>
    <button class="add" onclick="add_product(this.parentElement)">+</button>
  </div>';

$search_product_output = '';
include 'connect.php';

$query = '%'.$_GET['query'].'%';
$sql  = 'SELECT product.id, product.name, product.description, product_price.price FROM product INNER JOIN product_price ON product.id = product_price.product_id WHERE name LIKE ? OR description LIKE ?';
$stmt = $conn->prepare($sql);
$stmt->bind_param('ss', $query, $query);
$stmt->execute();

$result = $stmt->get_result();

if ($result->num_rows>0) {
  while ($row = $result->fetch_assoc()) {
    $id = $row['id'];
    $name = $row['name'];
    $desc = $row['description'];
    $price = $row['price'];

    $files = glob('product_img/'.$id.'.*');

    $img_url = $files ? $files[0] : 'https://picsum.photos/64/64'; // if product has img, then set that, else set default
    $search_product_output .= sprintf($search_product_html, $id, $img_url, $name, $desc, $price);
  }
}
else {
  $search_product_output = 'No Products Found!';
}
echo $search_product_output;


?>