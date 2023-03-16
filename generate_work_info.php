<?php

include_once 'connect.php';

$work_info_html = '
<h1>%s</h1>
<p1>%s</p1>
<p1>%s</p1>
<p1>%s</p1>
<p1>%s</p1>
<p1>%s</p1>
<p1>%s</p1>
<p1>%s</p1>
<div class="employees">
  %s
</div>';

$employee_html = '
<div class="employee">
  <input type="number" name="employee_id" value="%s"
  <img class="employee-img" src="%s">
  <div class="employee-desc">
    <h1>%s</h1> <--! Employee Name -->
    <h1>%s</h1> <!-- Employee Job -->
    <h1>%s</h1> <!-- Employee Contact -->
    <h1>%s</h1>
  </div>
  <button class="remove-employee">X</button>
</div>';



$work_info_sql = 'SELECT work.title AS work_title, work.description AS work_description, work_type.name AS work_type_name, work_type.description AS work_type_description, work_time.start_datetime as work_start, work_time.end_datetime AS work_end, CONCAT_WS(" ", customer.title, customer.first_name, customer.last_name) AS customer_name, CONCAT_WS(" ", customer.email, customer.home_phone) AS contact, CONCAT_WS(", ", address.line_1, address.line_2, address.postcode, address.city) AS address 
FROM work 
INNER JOIN work_type ON work.work_type_id = work_type.id
INNER JOIN work_time ON work.id = work_time.work_id 
INNER JOIN customer ON work.customer_id = customer.id 
INNER JOIN work_address ON work.id = work_address.work_id
INNER JOIN address ON work_address.address_id = address.id
WHERE work.id = ?';
$work_info_stmt = $conn->prepare($work_info_sql);

$employee_sql = 'SELECT work_employee.employee_id, CONCAT_WS(" ", employee.first_name, employee.last_name) AS employee_name, employee_job, CONCAT_WS(", ", employee.phone, employee.email) AS employee_contact 
FROM work_employee 
INNER JOIN employee ON work_employee.employee_id = employee.id 
WHERE work_employee.work_id = ?';
$employee_stmt = $conn->prepare($employee_sql);

function generate_work_info($work_id) {
  global $work_info_stmt, $employee_stmt, $employee_html, $work_info_html;
  $employee_output = '';
  $work_info_output = '';
  
  $work_info_stmt->bind_param('i', $work_id);
  $work_info_stmt->execute();
  $result = $work_info_stmt->get_result();
  if ($result->num_rows>0) {
    $work_row = $result->fetch_assoc();

    $work_title = $work_row['work_title'];
    $work_description = $work_row['work_description'];
    $work_start = $work_row['work_start'];
    $work_end = $work_row['work_end'];
    $work_type_name = $work_row['work_type_name'];
    $work_type_description = $work_row['work_type_description'];
    $customer_name = $work_row['customer_name'];
    $contact = $work_row['contact'];
    $address = $work_row['address'];
    
    $employee_stmt->bind_param('i', $work_id);
    $employee_stmt->execute();
    $result = $employee_stmt->get_result();
    while ($employee_row = $result->fetch_assoc()) {
      $employee_id = $employee_row['employee_id'];
      $employee_name = $employee_row['employee_name'];
      $employee_title = $employee_row['employee_title'];
      $employee_contact = $employee_row['employee_contact'];

      $files = glob('employee_img/'.$employee_id.'.*');

      $employee_img = $files ? $files[0] : 'https://picsum.photos/64/64'; // if product has img, then set that, else set default
      $employee_output .= sprintf($employee_html, $employee_id, $employee_img, $employee_name, $employee_title);
    }

    $work_info_output = sprintf($work_info_html, $work_title, $work_description, ($work_start.' - '.$work_end), $work_type_name, $work_type_description, $customer_name, $contact, $address, $employee_output);
  }
  return $work_info_output;
}


?>