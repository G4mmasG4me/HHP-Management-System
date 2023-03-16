<?php 
include 'connect.php';
include 'verify_session.php';

if (verify_session($conn)) {
  header('Location: index.php');
};


function generateToken() {
  $token = '';
  $characters = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
  $characters = str_split($characters);
  $token_length = 16;
  for ($i=0;$i<$token_length;$i++) {
    $token .= $characters[array_rand($characters)];
  }
  return $token;
}

$signin_error = '';

$signin_sql = 'SELECT employee.id, employee.email, password.password FROM employee INNER JOIN password ON employee.id = password.employee_id WHERE employee.email = ?';
$signin_stmt = $conn->prepare($signin_sql);

$add_token_sql = 'INSERT INTO employee_session (employee_id, token, expiry) VALUES (?,?,?)';
$add_token_stmt = $conn->prepare($add_token_sql);

if (isset($_POST['signin-submit'])) {
  if (!empty($_POST['email']) && !empty($_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    // sanitise input

    // bind parameters and execute query
    $signin_stmt->bind_param('s', $email);
    $signin_stmt->execute();
    $result = $signin_stmt->get_result();
    if ($result->num_rows==1) { // account found
      $row = $result->fetch_assoc();
      $password_hash = $row['password'];
      if (password_verify($password, $password_hash)) { // valid password sign in
        $session_expiry_time = 60*60*24*28; // 28 days
        $expiration_datetime = time() + $session_expiry_time;
        $expiration_timestamp = date('Y-m-d H:i:s', time() + $session_expiry_time);


        $employee_id = $row['id'];
        // generate token
        $token = generateToken();

        // save token to database
        $add_token_stmt->bind_param('iss', $employee_id, $token, $expiration_timestamp);
        $add_token_stmt->execute();

        // save token and session to cookie
        $cookie_value = array(
          'employee_id' => $employee_id,
          'token' => $token
        );
        setcookie('session', json_encode($cookie_value), $expiration_datetime, '/');
        header('Location: index.php');
      }
      else { // invalid password
        $signin_error = '<p>Invalid Password!</p>';
      }
    }
    elseif ($result->num_rows==0) { // no account
      $signin_error =  '<p>No Account</p>';
    }
    elseif ($result->num_rows>1) { // more than one email, error
      $signin_error = '<p>Error In Database</p>';
    }
  }
  else {
    echo 'Invalid Inputs';
  }
}






?>

<!DOCTYPE html>
<html>
  <head>
    <link rel="stylesheet" href="styles/default.css">
    <link rel="stylesheet" href="styles/signin.css">
    <link rel="stylesheet" href="styles/navbar.css">
  </head>
  <body>
    <header>
      <?php include "navbar.php"; ?>
    </header>
    <div class="main">
      <div class="signin-container">
        <h1>Sign In</h1>
        <form method="post">
          <label for="email">Email</label>
          <input type="text" name="email">
          <label for="password">Password</label>
          <input type="password" name="password">
          <input type="submit" name="signin-submit" value="Submit">
        </form>
        <?php echo $signin_error; ?>
      </div>


    </div>
  </body>
  
</html>