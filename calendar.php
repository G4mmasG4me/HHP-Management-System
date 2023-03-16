<?php 

$page_start = microtime();
include 'connect.php';
include 'verify_session.php';
include 'generate_notes.php';
include 'generate_receipt.php';
include 'generate_work_info.php';

if (!verify_session($conn)) {
  header('Location: signin.php');
};


date_default_timezone_set('UTC');

if (isset($_GET['week'])) { # if week start is set, set week_start to first day of that week, else set week start to first day of this week
  // 2 different way of calculating the first day of the week for a given day
  // $week_start = date('Y-m-d', strtotime($_GET['week']) - (date('w', strtotime($_GET['week']))*60*60*24));
  $week_start = date('Y-m-d', strtotime($_GET['week'].'-'.date('w', strtotime($_GET['week'])).' days'));
}
else {
  $week_start = date('Y-m-d', strtotime('-'.date('w').' days'));
}
$previous_week_start = date('Y-m-d', strtotime($week_start.'-7 days'));
$next_week_start = date('Y-m-d', strtotime($week_start.'+7 days'));

$week_end = date('Y-m-d', strtotime($week_start.'+'.(6-date('w', strtotime($week_start))).' days'));

$week_start_datetime = $week_start.' 00:00:00';
$week_end_datetime = $week_end.' 23:59:59';

$month_text = (date('F', strtotime($week_start)) == date('F', strtotime($week_end))) ? date('F', strtotime($week_start)) : (date('F', strtotime($week_start)).' - '.date('F', strtotime($week_end)));

$days = '';
// saves the week days into a list


$day_word_output = '';
$day_number_output = '';
for ($i = 0; $i < 7; $i++) {
  $date = strtotime($week_start." +".$i." days");
  $day_number = date('d', $date);
  $day_word = date('l', $date);
  if ($date == strtotime(date('Y/m/d'))) {
    $day_word_output .= '<div class="today"><h1>'.$day_word.'</h1></div>';
    $day_number_output .= '<div class="today"><h1>'.$day_number.'</h1></div>';
  }
  else {
    $day_word_output .= '<div><h1>'.$day_word.'</h1></div>';
    $day_number_output .= '<div><h1>'.$day_number.'</h1></div>';
  }
}

if (true) { // if employee privelleges allows you to view other employees schedule
  if (isset($_GET['employee_id'])) { // if has specific employee id, view that employees schedule

  }
  else { // view all employees schedule as one

  }
}
else { // not employee schedule viewing privelleges, view own schedule

}


$work_html = '
  <div class="work" onclick="openChangeWorkPopup(%s)" style="top:%s; left:%s; height:%s; background:#%sBF">
    %s
  </div>';

$work_output = '';

$work_popout_html = '
  <div class="popouts change-event" id="work-%s">
    <input type="hidden" name="work_id" value="%s">
    <div class="work-popout-navbar">
      <button class="item" onclick="changeWorkPopoutContent(this)">Details</button>
      <button class="item" onclick="changeWorkPopoutContent(this)">Receipt</button>
      <button class="item" onclick="changeWorkPopoutContent(this)">Notes</button>
      <button class="item" onclick="changeWorkPopoutContent(this)">Operations</button>
      <button class="cover-container-close" onclick="closeChangeWorkPopup(this)">X</button>
    </div>
    <div class="work-popout-content">
      <div>
        %s
      </div>
      <div style="display: none;">
        %s
      </div>
      <div class="notes-container" style="display: none;">
        <h1>Notes</h1>
        <div class="notes">
        %s
        </div>
        <button onclick="addNote(this.previousElementSibling)"> A New Note</button>
      </div>
      <div class="" style="display: none;">
        <h1>Operations</h1>
        <form method="post" action="delete_work.php">
          <input type="hidden" name="redirect_url" value="calendar.php">
          <input type="hidden" name="work_id" value="%s">
          <input type="submit" value="Delete Work">
        </form>
      </div>
    </div>
  </div>';

$work_popout_output = '';


$sql = 'SELECT work.id, work.colour, work_time.start_datetime, work_time.end_datetime FROM work INNER JOIN work_time ON work.id = work_time.work_id WHERE work_time.start_datetime BETWEEN ? AND ? OR work_time.end_datetime BETWEEN ? AND ?';
$stmt = $conn->prepare($sql);
$stmt->bind_param('ssss', $week_start_datetime, $week_end_datetime, $week_start_datetime, $week_end_datetime);
$stmt->execute();
$result = $stmt->get_result();


// 
if ($result->num_rows>0) {
  while ($row = $result->fetch_assoc()) {
    $work_id = $row['id'];
    $work_colour = $row['colour'];
    $start_datetime = strtotime($row['start_datetime']);
    $end_datetime = strtotime($row['end_datetime']);

    // get info output 
    $info_output = generate_work_info($work_id);

    // get receipt output
    $receipt_output = generate_receipt($work_id);
    
    // get notes for each work
    $note_output = generate_notes($work_id);

    // loop through days between work start and (end of work, or end of week (whichever is smaller))
    $current_day = max(strtotime($week_start), $start_datetime);
    while (date('y-m-d', $current_day) <= date('y-m-d', min(strtotime($week_end), $end_datetime))) {
      $start_of_current_day = strtotime(date('y-m-d', $current_day).' 00:00');
      $end_of_current_day = strtotime(date('y-m-d', $current_day).' 23:59');

      $current_day_start_time = (max($start_datetime, $start_of_current_day) % 86400);
      $current_day_end_time = (min($end_datetime, $end_of_current_day) % 86400);
      $start_time = ($current_day_start_time % 86400);
      
      $margin_top = ($current_day_start_time / 86400 * 100).'%';
      $margin_left = (date('w', $current_day) * (1/7) * 100).'%';

      $height = (($current_day_end_time - $current_day_start_time) / 86400 * 100).'%';
      $work_output .= sprintf($work_html, $work_id, $margin_top, $margin_left, $height, $work_colour, $info_output);
      $current_day = $current_day + 86400;
    }
    
    $work_popout_output .= sprintf($work_popout_html, $work_id, $work_id, $info_output, $receipt_output, $note_output, $work_id);
    
  }
}
?>

<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" href="styles/default.css">
    <link rel="stylesheet" href="styles/calendar.css">
    <link rel="stylesheet" href="styles/navbar.css">
    <link rel="stylesheet" href="styles/add_work_popout.css">
    <script src="scripts/calendar.js"></script>
  </head>
  <body>
    <header>
      <?php include("navbar.php") ?>
    </header>
    <div class="main">
      <div class="calendar-control-bar">
        <div class="left-side">
          <button class="add-work-button" onclick="open_add_work_popout()">Add Work</button>
          <form method="get">
            <input type="hidden" name="week" value="<?php echo $previous_week_start; ?>">
            <input class="week-selector-submit" type="submit" value="<">
          </form>
          <form method="get">
            <input type="hidden" name="week" value="<?php echo $next_week_start; ?>">
            <input class="week-selector-submit" type="submit" value=">">
          </form>
          <div class="month"><?php echo $month_text; ?></div>
        </div>
        <div class="right-side">

        </div>
      </div>
      <div class="calendar">
        <input type="hidden" name="week_start" value="<?php echo $week_start ?>">
        <div class="top">
          <div class="calendar-tl">GMT</div>
          <div class="dates">
            <div class="day">
              <?php echo $day_word_output ?>
            </div>
            <div class="date">
              <?php echo $day_number_output ?>
            </div>
          </div>
        </div>
        <div class="calendar-subsection">
          <div class="times">
            <div>00:00</div>
            <div>01:00</div>
            <div>02:00</div>
            <div>03:00</div>
            <div>04:00</div>
            <div>05:00</div>
            <div>06:00</div>
            <div>07:00</div>
            <div>08:00</div>
            <div>09:00</div>
            <div>10:00</div>
            <div>11:00</div>
            <div>12:00</div>
            <div>13:00</div>
            <div>14:00</div>
            <div>15:00</div>
            <div>16:00</div>
            <div>17:00</div>
            <div>18:00</div>
            <div>19:00</div>
            <div>20:00</div>
            <div>21:00</div>
            <div>22:00</div>
            <div>23:00</div>
          </div>
          <div class="calendar-content">
            <div class="row">
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
            </div>
            <div class="row">
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
            </div>
            <div class="row">
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
            </div>
            <div class="row">
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
            </div>
            <div class="row">
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
            </div>
            <div class="row">
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
            </div>
            <div class="row">
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
            </div>
            <div class="row">
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
            </div>
            <div class="row">
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
            </div>
            <div class="row">
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
            </div>
            <div class="row">
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
            </div>
            <div class="row">
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
            </div>
            <div class="row">
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
            </div>
            <div class="row">
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
            </div>
            <div class="row">
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
            </div>
            <div class="row">
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
            </div>
            <div class="row">
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
            </div>
            <div class="row">
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
            </div>
            <div class="row">
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
            </div>
            <div class="row">
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
            </div>
            <div class="row">
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
            </div>
            <div class="row">
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
            </div>
            <div class="row">
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
            </div>
            <div class="row">
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
              <div></div>
            </div>
            <div class="events">
              <?php echo $work_output ?>
              <div class="current-time">
                <div class="line"></div>
                <div class="circle"></div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="cover-container" id="cover-container">
        <div class="popouts" id="add-work">
          <div class="work-popout-navbar">
            <div class="left">
            </div>
            <div class="center">
              <h1>Add Work</h1>
            </div>
            <div class="right">
              <button class="cover-container-close" onclick="close_add_work_popout()">&#10005;</button>
            </div>
            
          </div>
          <div class="work-popout-content">
            <form method="post" action="add_work.php">
              <input type="hidden" name="redirect_url" value="calendar.php">
              <fieldset>
                <div class="form-input">
                  <label for="title">Title</label>
                  <input type="text" name="title">
                </div>

                <div class="form-input">
                  <label for="description">Description</label>
                  <input type="text" name="description">
                </div>

                <div class="form-input"> <!-- Work Type Search -->
                  <label for="work_type_search">Work Type Search</label>
                  <input type="text" name="work_type_search" onchange="search_work_type(this)">
                </div>
                <div class="search-results"> <!-- Work Type Search Results -->

                </div>
                <div class="form-input selected-result" style="display:none"> <!-- Work Type Value -->
                  <div class="info">
                    <p></p> <!-- Name -->
                    <p></p> <!-- Description -->
                    <p></p> <!-- Colour -->
                  </div>
                  <button type="button" onclick="remove_work_type(this.parentElement)">X</button>
                  <input type="hidden" name="work_type_id" value="">
                </div>


                <div class="form-input"> <!-- Customer Search -->
                  <label for="customer_search">Customer Search</label>
                  <input type="text" name="customer_search" onchange="search_customer(this)">
                </div>
                <div class="search-results"> <!-- Customer Search Results -->

                </div>
                <div class="form-input selected-result" style="display:none"> <!-- Customer Value -->
                  <div class="info">
                    <p></p> <!-- Name -->
                    <p></p> <!-- Email -->
                    <p></p> <!-- Phone -->
                  </div>
                  <button type="button" onclick="remove_customer(this.parentElement)">X</button>
                  <input type="hidden" name="customer_id" value="">
                </div>

                <div class="form-input"> <!-- Address Search -->
                  <label for="shipping_address_search">Address Search</label>
                  <input type="text" name="shipping_address_search" onchange="search_address(this)">
                </div>
                <div class="search-results"> <!-- Address Search Results -->

                </div>
                <div class="form-input selected-result" style="display:none"> <!-- Address Value -->
                  <div class="info">
                    <p></p> <!-- Line 1 -->
                    <p></p> <!-- Line 2 -->
                    <p></p> <!-- Postcode -->
                    <p></p> <!-- City -->
                  </div>
                  <button type="button" onclick="remove_address(this.parentElement)">X</button>
                  <input type="hidden" name="address_id" value="">
                </div>

                <div class="form-input">
                  <label for="start_date">Start Date</label>
                  <input type="date" name="start_date" onchange="setLowestDateTime(this.parentElement)">
                  <select name="start_time" onchange="setLowestDateTime(this.parentElement)">
                  </select>
                </div>

                <div class="form-input">
                  <label for="end_date">End Date</label>
                  <input type="date" name="end_date" onchange="setHighestDateTime(this.parentElement)">
                  <select name="end_time" onchange="setHighestDateTime(this.parentElement)">
                  </select>
                </div>

                <div class="form-input">
                  <label for="colour">Event Colour</label>
                  <input type="color" name="colour" value="#f7c800">
                </div>

                <div class="form-input">
                  <input type="submit" name="add_work_submit">
                </div>
              </fieldset>
            </form>
          </div>
          
        </div>
        <?php echo $work_popout_output; ?>
        
      </div>
    </div>
    <footer>

    </footer>
  </body>
</html>

<?php 
?>