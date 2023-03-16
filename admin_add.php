<?php 

include 'connect.php';

$insert_admin_sql = 'INSERT INTO employee (first_name, email) VALUES (?,?)';
$insert_admin_stmt = $conn->prepare($insert_admin_sql); 

$insert_password_sql = 'INSERT INTO password (password, employee_id) VALUES (?,?)';
$insert_password_stmt = $conn->prepare($insert_password_sql);

$name = 'admin';
$email = 'domhough@hotmail.co.uk';
$password = 'AdminPass';
$email_check_sql = 'SELECT email FROM employee WHERE email = ?';
$stmt = $conn->prepare($email_check_sql);
$stmt->bind_param('s', $email);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows==0) {
  $hashed_password = password_hash($password, 'argon2i');
  $insert_admin_stmt->bind_param('ss', $name, $email);
  $insert_admin_stmt->execute();
  $last_id = $conn->insert_id;
  $insert_password_stmt->bind_param('si', $hashed_password, $last_id);
  $insert_password_stmt->execute();
  echo 'Success';

}
else {
  echo 'Admin Exists';
}

?>