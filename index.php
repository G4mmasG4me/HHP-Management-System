<?php

include 'connect.php';
include 'verify_session.php';
include 'console_log.php';

if (!verify_session($conn)) {
  header('Location: signin.php');
};

$trunc_customer_sql = 'TRUNCATE `customer`; ALTER TABLE `customer` AUTO_INCREMENT = 1';
$trunc_all = 'TRUNCATE `employee`; ALTER TABLE `employee` AUTO_INCREMENT = 1';

function add_customer($conn) {
  $add_customer_sql = 'INSERT INTO customer (first_name, last_name, email, mobile_phone, home_phone, shipping_address_id, billing_address_id) VALUES (?,?,?,?,?,?,?)'; # inital sql query with required information
  $stmt = $conn->prepare($add_customer_sql);
  $first_name = $_POST['first_name'];
  $last_name = $_POST['last_name'];
  $email = $_POST['email'];
  $mobile_phone = $_POST['mobile_phone'];
  $home_phone = $_POST['home_phone'];
  $shipping_address_id = $_POST['shipping_address_id'];
  $billing_address_id = $_POST['billing_address_id'];
  $stmt->bind_param('sssssii', $first_name, $last_name, $email, $mobile_phone, $home_phone, $shipping_address_id, $billing_address_id);
  $stmt->execute();
}

function add_customer_address($conn) {
  $add_customer_address_sql = 'INSERT INTO customer_address (customer_id, address_id) VALUES (?,?)';
  $stmt = $conn->prepare($add_customer_address_sql);
  $customer_id = $_POST['customer_id'];
  $address_id = $_POST['address_id'];
  $stmt->bind_param('ii', $customer_id, $address_id);
  $stmt->execute();
}

function add_address($conn) {
  $sql = 'INSERT INTO address (line_1, line_2, postcode, city) VALUES (?,?,?,?)';
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('ssss', $_POST['line_1'], $_POST['line_2'], $_POST['postcode'], $_POST['city']);
  $stmt->execute();
}
function add_service($conn) {
  $sql = 'INSERT INTO service (customer_id, service_date) VALUES (?, ?)';
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('ss', $_POST['customer_id'], $_POST['service_date']);
  $stmt->execute();
}

function add_employee($conn) {
  // sanitize input
  $sql = 'INSERT INTO employee (first_name, last_name, email, phone) VALUES (?,?,?,?)';
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('ssss', $_POST['first_name'], $_POST['last_name'], $_POST['email'], $_POST['phone']);
  $stmt->execute();

  $employee_id = $conn->insert_id;
  $password_hash = password_hash($_POST['password'], PASSWORD_ARGON2I);

  $sql = 'INSERT INTO password (employee_id, password) VALUES (?,?)';
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('is', $employee_id, $password_hash);
  $stmt->execute();
}

function add_product($conn) {
  $sql = 'INSERT INTO product (name, description) VALUES (?,?)';
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('ss', $_POST['name'], $_POST['description']);
  $stmt->execute();

  $product_id = $conn->insert_id;
  $sql = 'INSERT INTO product_price (product_id, price) VALUES (?,?)';
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('ii', $product_id, $_POST['price']);
  $stmt->execute();

  // add image
  $target_dir = 'product_img/'.$product_id;
  if (isset($_FILES['img'])) {
    $valid_file_types = array('jpg', 'jpeg', 'png');
    $image_file_type = strtolower(pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION));

    // check if valid file type
    if (in_array($image_file_type, $valid_file_types)) {
      // generate new img name and directory
      $image_file_type = strtolower(pathinfo($_FILES['img']['name'], PATHINFO_EXTENSION));
      $target_dir = 'product_img/'.$product_id.'.'.$image_file_type;
      
      // save file from temporary location to new location
      move_uploaded_file($_FILES['img']['tmp_name'], $target_dir);
      header("Location: " . $_SERVER["PHP_SELF"]);
    }
    else {
      echo 'Invalid File Type';
    }
  }
  
}

function truncate($conn, $database) {
  $sql = 'TRUNCATE TABLE ?; ALTER TABLE ? AUTO_INCREMENT = 1;';
  $stmt = $conn->prepare($sql);
  $stmt->bind_param('s', $database);
  $stmt->execute();
  header('Location: '.$_SERVER['PHP_SELF']);
}

function truncate_all($conn) {
  $trunc_all_sql = 'SELECT CONCAT("truncate table ",table_schema,".",table_name,"; ALTER TABLE ",table_schema,".",table_name," AUTO_INCREMENT = 1;") AS query FROM information_schema.tables  WHERE table_schema IN ("hothouseproperties")';
  $result = $conn->query($trunc_all_sql);
  $total_query = '';
  while ($row = $result->fetch_assoc()) {
    $total_query .= $row['query'];
  }
  $conn->multi_query($total_query);
  header('Location: '.$_SERVER['PHP_SELF']);
}

if (isset($_POST['add_customer_submit'])) {
  if (!empty($_POST['first_name']) && !empty($_POST['email']) && !empty($_POST['address_line_1']) && !empty($_POST['postcode'])) { # checks for required parameters
    add_customer($conn);
  }
  else {
    echo 'Missing Input';
  }
}
if (isset($_POST['add_customer_address_submit'])) {
  if (!empty($_POST['customer_id'] && !empty($_POST['address_id']))) {
    add_customer_address($conn);
  }
  else {
    echo 'Missing Input';
  }
}


if (isset($_POST['add_address_submit'])) {
  if (!empty($_POST['line_1']) && !empty($_POST['postcode']) && !empty($_POST['city'])) {
    add_address($conn);
  }
  else {
    echo 'Missing Input';
  }
}
if (isset($_POST['add_service_submit'])) {
  if (!empty($_POST['customer_id']) && !empty($_POST['service_date'])) {
    add_service($conn);
  }
  else {
    echo 'Missing Input';
  }
}
if (isset($_POST['add_employee_submit'])) {
  if (!empty($_POST['first_name']) && !empty($_POST['last_name']) && !empty($_POST['email']) && !empty($_POST['phone']) && !empty($_POST['password'])) {
    add_employee($conn);
  }
  else {
    echo 'Missing Input';
  }
}
if (isset($_POST['add_product_submit'])) {
  if (!empty($_POST['name']) && !empty($_POST['description']) && !empty($_POST['price'])) {
    add_product($conn);
  }
  else {
    echo 'Missing Input';
  }
}

if (isset($_POST['truncate_customer'])) {
  truncate($conn, 'customer');
}
if (isset($_POST['truncate_employee'])) {
  truncate($conn, 'employee');
  
}
if (isset($_POST['truncate_employee_session'])) {
  truncate($conn, 'employee_session');
}
if (isset($_POST['truncate_note'])) {
  truncate($conn, 'note');
}
if (isset($_POST['truncate_password'])) {
  truncate($conn, 'password');
}
if (isset($_POST['truncate_product'])) {
  truncate($conn, 'product');
}
if (isset($_POST['truncate_product_price'])) {
  truncate($conn, 'product_price');
}
if (isset($_POST['truncate_sent_email'])) {
  truncate($conn, 'sent_email');
}
if (isset($_POST['truncate_service'])) {
  truncate($conn, 'service');
}
if (isset($_POST['truncate_work'])) {
  truncate($conn, 'work');
}
if (isset($_POST['truncate_work_adjustment'])) {
  truncate($conn, 'work_adjustment');
}
if (isset($_POST['truncate_work_product'])) {
  truncate($conn, 'work_product');
}
if (isset($_POST['truncate_work_time'])) {
  truncate($conn, 'work_time');
}
if (isset($_POST['truncate_all'])) {
  truncate_all($conn);
}

?>

<!DOCTYPE HTML>
<html>
  <head>
    <link rel="stylesheet" href="styles/default.css">
    <link rel="stylesheet" href="styles/index.css">
    <link rel="stylesheet" href="styles/navbar.css">
    <script src="scripts/main.js"></script>
  </head>
  <body>
    <header>
      <?php include "navbar.php" ?>
    </header>
    <div class="main">
      <div class="side-bar">
        <div class="side-nav">
          <button class="row" onclick="open_close_sidebar()">&#8801</button>
        </div>
        <div class="links" style="display:none"> 
          <button class="row" onclick="open_section(this)">Customer</button>
          <button class="row" onclick="open_section(this)">Address</button>
          <button class="row" onclick="open_section(this)">Employee</button>
          <button class="row" onclick="open_section(this)">Product</button>
          <button class="row" onclick="open_section(this)">Work</button>
          <button class="row" onclick="open_section(this)">Truncate</button>
        </div>
        
      </div>
      <div class="content"> <!-- Customer -->
        <section style="display:flex">
          <form method="post">
            <fieldset>
              <legend>Add Customer</legend>
              <div class="form-input">
                <label for="title">Title</label>
                <input type="text" name="title">
              </div>

              <div class="form-input"> <!-- First Name -->
                <label for="first_name">First Name</label>
                <input type="text" name="first_name">
              </div>

              <div class="form-input"> <!-- Second Name -->
                <label for="last_name">Second Name</label>
                <input type="text" name="last_name">
              </div>

              <div class="form-input"> <!-- Email -->
                <label for="email">Email</label>
                <input type="text" name="email" placeholder="example@address.co.uk">
              </div>

              <div class="form-input"> <!-- Mobile Phone -->
                <label for="mobile_phone">Mobile Phone</label>
                <input type="text" name="mobile_phone">
              </div>

              <div class="form-input"> <!-- Home Phone -->
                <label for="home_phone">Home Phone</label>
                <input type="text" name="home_phone">
              </div>

              <div class="form-input"> <!-- Billing Address Search -->
                <label for="billing_address_search">Billing Address Search</label>
                <input type="text" name="billing_address_search">
              </div>
              <div class="search-results"> <!-- Billing Address Search Results -->
              </div>

              <div class="form-input"> <!-- Billing Address Value -->
                <input type="hidden" name="billing_address_id" value="">
              </div>

              <div class="form-input"> <!-- Add Customer Submit -->
                <input type="submit" name="add_customer_submit">
              </div>
            </fieldset>
          </form>

          <form method="post">
            <fieldset>
              <legend>Add Address To Customer</legend>
              <div class="form-input"> <!-- Customer Search -->
                <label for="customer_search">Customer Search</label>
                <input type="text" name="customer_search" onchange="search_customer(this)">
              </div>
              <div class="search-results"> <!-- Customer Search Results -->

              </div>
              <div class="form-input selected-result" style="display:none"> <!-- Customer Value -->
                <div class="info">
                  <p></p> <!-- Name -->
                  <p></p> <!-- Email -->
                  <p></p> <!-- Phone -->
                </div>
                <button type="button" onclick="remove_customer(this.parentElement)">X</button>
                <input type="hidden" name="customer_id" value="">
              </div>

              <div class="form-input"> <!-- Address Search -->
                <label for="shipping_address_search">Address Search</label>
                <input type="text" name="shipping_address_search" onchange="search_address(this)">
              </div>
              <div class="search-results"> <!-- Address Search Results -->

              </div>
              <div class="form-input selected-result" style="display:none"> <!-- Address Value -->
                <div class="info">
                  <p></p> <!-- Line 1 -->
                  <p></p> <!-- Line 2 -->
                  <p></p> <!-- Postcode -->
                  <p></p> <!-- City -->
                </div>
                <button type="button" onclick="remove_address(this.parentElement)">X</button>
                <input type="hidden" name="address_id" value="">
              </div>
              <div class="form-input"> <!-- Add Customer Address Submit -->
                <input type="submit" name="add_customer_address_submit">
              </div>

            </fieldset>
            
          </form>
        </section>

        <section style="display:none"> <!-- Address -->
          <form method="post">
            <fieldset>
              <legend>Add Service</legend>
              <div class="form-input">
                <label for="search_customer">Search</label>
                <input type="text" name="search-customer" onchange="showCustomer(this.value)">
              </div>
              <div id="search-results">
              </div>
              <div class="form-input">
                <label for="customer_id">Customer ID</label>
                <input type="text" name="customer_id">
              </div>

              <div class="form-input">
                <label for="first_name">Service Date</label>
                <input type="date" name="service_date">
              </div>

              <div class="form-input">
                <input type="submit" name="add_service_submit">
              </div>
            </fieldset>
          </form>
          <form method="post">
            <fieldset>
              <legend>Add Address</legend>
              <div class="form-input">
                <label for="line_1">Line 1</label>
                <input type="text" name="line_1">
              </div>
              <div id="search-results">
              </div>
              <div class="form-input">
                <label for="line_2">Line 2</label>
                <input type="text" name="line_2">
              </div>

              <div class="form-input">
                <label for="postcode">Postcode</label>
                <input type="text" name="postcode">
              </div>

              <div class="form-input">
                <label for="city">City</label>
                <input type="text" name="city">
              </div>

              <div class="form-input">
                <input type="submit" name="add_address_submit">
              </div>
            </fieldset>
          </form>
        </section>

        <section style="display:none">
          <form method="post">
            <fieldset>
              <legend>Add Employee</legend>
              <div class="form-input">
                <label for="first_name">First Name</label>
                <input type="text" name="first_name">
              </div>
              <div class="form-input">
                <label for="second_name">Second Name</label>
                <input type="text" name="last_name">
              </div>

              <div class="form-input">
                <label for="email">Email</label>
                <input type="text" name="email">
              </div>

              <div class="form-input">
                <label for="phone">Phone</label>
                <input type="text" name="phone">
              </div>

              <div class="form-input">
                <label for="phone">Password</label>
                <input type="text" name="password">
              </div>

              <div class="form-input">
                <input type="submit" name="add_employee_submit">
              </div>
            </fieldset>
          </form>
        </section>

        <section style="display:none">
          <form method="post" enctype="multipart/form-data">
            <fieldset>
              <legend>Add Product</legend>
              <div class="form-input">
                <label for="name">Name</label>
                <input type="text" name="name">
              </div>
              <div class="form-input">
                <label for="description">Description</label>
                <textarea type="text" name="description"></textarea>
              </div>

              <div class="form-input">
                <label for="price">Price</label>
                <input type="text" name="price">
              </div>

              <div class="form-input">
                <label for="img">Img</label>
                <input type="file" name="img">
              </div>

              <div class="form-input">
                <input type="submit" name="add_product_submit">
              </div>
            </fieldset>
          </form>
        </section>
        
        <section style="display:none">
          <form method="post" action="">
            <fieldset>
              <legend>Reset Databases</legend>
              <div class="form-input">
                <input type="submit" name="truncate_customer" value="Truncate `customer` Database">
              </div>
              <div class="form-input">
                <input type="submit" name="truncate_employee" value="Truncate `employee` Database">
              </div>
              <div class="form-input">
                <input type="submit" name="truncate_employee_session" value="Truncate `employee_session` Database">
              </div>
              <div class="form-input">
                <input type="submit" name="truncate_note" value="Truncate `note` Database">
              </div>
              <div class="form-input">
                <input type="submit" name="truncate_password" value="Truncate `password` Database">
              </div>
              <div class="form-input">
                <input type="submit" name="truncate_product" value="Truncate `product` Database">
              </div>
              <div class="form-input">
                <input type="submit" name="truncate_product_price" value="Truncate `product_price` Database">
              </div>
              <div class="form-input">
                <input type="submit" name="truncate_sent_email" value="Truncate `sent_email` Database">
              </div>
              <div class="form-input">
                <input type="submit" name="truncate_service" value="Truncate `service` Database">
              </div>
              <div class="form-input">
                <input type="submit" name="truncate_work" value="Truncate `work` Database">
              </div>
              <div class="form-input">
                <input type="submit" name="truncate_work_adjustment" value="Truncate `work_adjustment` Database">
              </div>
              <div class="form-input">
                <input type="submit" name="truncate_work_product" value="Truncate `work_product` Database">
              </div>
              <div class="form-input">
                <input type="submit" name="truncate_work_time" value="Truncate `work_time` Database">
              </div>
              <div class="form-input">
                <input type="submit" name="truncate_all" value="Truncate All Databases">
              </div>
            </fieldset>
          </form>
        </section>
      </div>
    </div>
  </body>
</html>