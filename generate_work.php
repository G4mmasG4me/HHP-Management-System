<?php 

$sql = 'SELECT work.id FROM work INNER JOIN work_time ON work.id = work_time.work_id WHERE work_time.start_datetime BETWEEN ? AND ? OR work_time.end_datetime BETWEEN ? AND ?';
$stmt = $conn->prepare($sql);
$stmt->bind_param('ssss', $week_start_datetime, $week_end_datetime, $week_start_datetime, $week_end_datetime);
$stmt->execute();
$result = $stmt->get_result();

?>