<?php 
include_once 'connect.php';
include_once 'verify_session.php';

if (verify_session($conn)) {
  $end_navbar = '<a href="signout.php">Sign Out</a>';
}
else {
  $end_navbar = '<a href="signin.php">Sign In</a>';
}


?>

<div class="navbar">
  <div class="start">
    <a href="https://hothouseproperties.co.uk">Home</a>
  </div>
  <div class="center">
    <a href="calendar.php">Calendar</a>
    <a href="index.php">Control DBs</a>

  </div>
  <div class="end">
    <?php echo $end_navbar ?>
    
  </div>
</div>