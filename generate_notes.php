<?php

include_once 'connect.php';

$note_html = '
  <div>
    <input class="note_id" type="hidden" name="note_id" value="%s">
    <input type="text" name="title" placeholder="Title..." value="%s" onclick="editableText(this)" onchange="updateNote(this)" readonly>
    <textarea name="description" placeholder="Desc..." onclick="editableText(this)" onchange="updateNote(this)" readonly>%s</textarea>
    <form method="post" action="delete_note.php">
      <input type="hidden" name="note_id" value="%s">
      <input type="hidden" name="redirect_url" value="calendar.php">
      <input type="submit" value="&#9842;">
    </form>
  </div>';

$work_notes_sql = 'SELECT * FROM note WHERE work_id = ?';
$work_note_stmt = $conn->prepare($work_notes_sql);

function generate_notes($work_id) {
  global $work_note_stmt, $note_html;
  $work_notes = '';

  // get notes for each work
  $work_note_stmt->bind_param('i', $work_id);
  $work_note_stmt->execute();
  $result = $work_note_stmt->get_result();
  if ($result->num_rows>0) {
    while ($note_row = $result->fetch_assoc()) {
      $note_id = $note_row['id'];
      $note_title = $note_row['title'];
      $note_desc = $note_row['description'];

      $work_notes .= sprintf($note_html, $note_id, $note_title, $note_desc, $note_id);
    }
  }
  return $work_notes;
}

?>