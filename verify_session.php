<?php 

function verify_session($conn) {
  $sql = 'SELECT expiry FROM employee_session WHERE employee_id = ? AND token = ?';
  $stmt = $conn->prepare($sql);
  if (isset($_COOKIE['session'])) {
    $session = json_decode($_COOKIE['session'], true);
    $employee_id = $session['employee_id'];
    $token = $session['token'];
    $stmt->bind_param('is', $employee_id, $token);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows==1) { // session
      // check if token is expired
      $row = $result->fetch_assoc();
      if (time() >= strtotime($row['expiry'])) { // if after expiry, session expires
        // delete cookie
        setcookie('session', null, -1, '/');
        // direct to signin.php
        return false;
      }
      else {
        return true;
      }
    }
    else { // no session found in databse
      return false;
    }
  }
  else { // no cookie session
    return false;
  }
}


?>