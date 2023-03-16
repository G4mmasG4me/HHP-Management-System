<?php 
if (isset($_COOKIE['session'])) {
  setcookie('session', null, -1, '/');
  header('Location: signin.php');
}
?>