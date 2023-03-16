<?php 

include 'connect.php';

$work_type_html = '
<button class="search-result" type="button" onclick="add_work_type(this)">
  <div class="info">
    <p>%s</p>
    <p>%s</p>
    <p>%s</p>
  </div>
  <input type="hidden" value="%s" name="work_type_id"> 
</button>';
$work_type_output = '';

$work_type_sql = 'SELECT work_type.id AS id, work_type.name AS name, work_type.description AS description, work_type.colour AS colour
FROM work_type 
WHERE work_type.name LIKE ? OR work_type.description LIKE ? OR work_type.colour LIKE ?';
$q = '%'.$_GET['q'].'%';

$stmt = $conn->prepare($work_type_sql);
$stmt->bind_param('sss', $q, $q, $q);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
  while ($row = $result->fetch_assoc()) {
    $id = $row['id'];
    $name = $row['name'];
    $description = $row['description'];
    $colour = $row['colour'];
    $work_type_output .= sprintf($work_type_html, $name, $description, $colour, $id);
  }
}
else {
  $work_type_output = 'No Work Type Found';
}
echo $work_type_output;
?>