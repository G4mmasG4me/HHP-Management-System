<?php 

include 'connect.php';

$id = $_GET['id'];
foreach ($_GET as $key => $value) {
  echo $key;
  echo $value;
  if ($key != 'id') {
    $column = $key;
    $column_value = $value;
  }
}

$sql = 'UPDATE note SET '.$column.' = ? WHERE id = ?';
$stmt = $conn->prepare($sql);
$stmt->bind_param('si', $column_value, $id);
$stmt->execute();

?>