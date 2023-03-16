<?php

include_once('connect.php');

$q = $_GET['q'];

$employee_sql = 'SELECT * FROM employee WHERE employee.first_name LIKE ? OR employee.last_name LIKE ? OR employee.job LIKE ?';
$employee_stmt = $conn->prepare($employee_sql);
$employee_stmt->bind_param('sss', $q, $q, $q);
$employee_stmt->execute();

$employee_type_sql = 'SELECT * FROM employee_type WHERE employee_type.title LIKE ?';
$employee_type_stmt = $conn->prepare($employee_type_sql);
$employee_type_stmt->bind_param('s', $q);
$employee_type_stmt->execute();
?>