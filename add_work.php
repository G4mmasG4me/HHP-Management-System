<?php

include_once('connect.php');
if (isset($_POST['add_work_submit'])) {
  if (!empty($_POST['start_date']) && !empty($_POST['start_time']) && !empty($_POST['end_date']) && !empty($_POST['end_time'])) {
    $work_sql = 'INSERT INTO work (title, description, work_type_id, customer_id, colour, adjustment) VALUES (?,?,?,?,?,1)';
    $work_address_sql = 'INSERT INTO work_address (work_id, address_id) VALUES (?,?)';
    $work_time_sql = 'INSERT INTO work_time (work_id, start_datetime, end_datetime) VALUES (?,?,?)';

    $work_stmt = $conn->prepare($work_sql);
    $work_address_stmt = $conn->prepare($work_address_sql);
    $work_time_stmt = $conn->prepare($work_time_sql);

    $work_stmt->bind_param('ssiis', $_POST['title'], $_POST['description'], $_POST['work_type_id'], $_POST['customer_id'], substr($_POST['colour'], 1));
    $work_stmt->execute();

    $last_id = $conn->insert_id;
    
    $work_address_stmt->bind_param('ii', $last_id, $_POST['address_id']);
    $work_address_stmt->execute();

    $start_datetime = $_POST['start_date'].' '.$_POST['start_time'];
    $end_datetime = $_POST['end_date'].' '.$_POST['end_time'];
    $work_time_stmt->bind_param('iss', $last_id, $start_datetime, $end_datetime);
    $work_time_stmt->execute();

    header('Location: '.$_POST['redirect_url'].'?success=1');
  }
  else {
    header('Location: '.$_POST['redirect_url'].'?success=0');
  }
}

?>