<?php 

include_once('connect.php');
$note_id = $_POST['note_id'];

$delete_note_sql = 'DELETE FROM note WHERE id = ?';

$delete_note_stmt = $conn->prepare($delete_note_sql);

$delete_note_stmt->bind_param('i', $note_id);

$delete_note_stmt->execute();
$result = $delete_note_stmt->get_result();

header('Location: '.$_POST['redirect_url'].'?success=1?msg='.$result);
?>