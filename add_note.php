<?php 

include_once 'connect.php';
include 'generate_notes.php';

$id = $_GET['work_id'];
$sql = 'INSERT INTO note (work_id) VALUES (?)';
$stmt = $conn->prepare($sql);
$stmt->bind_param('i', $id);
$stmt->execute();

echo generate_notes($id);
?>