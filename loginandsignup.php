<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

$host = "localhost";
$user = "root";
$password = "";
$db = "assignment"; 

session_start();
$data = mysqli_connect($host, $user, $password, $db);

// Check connection to database
if ($data->connect_error) {
    die("Connection failed: " . $data->connect_error);
}


if (isset($_POST['register'])) {
  $email = $_POST['email'];
  $username = $_POST['username'];
  $password = $_POST['password'];
  $confirmpassword = $_POST['confirmpassword'];
  $usertype = "user";

  // Check if the email already exists in the database
  $check_email_query = "SELECT * FROM login WHERE email = '$email'";
  $check_email_result = mysqli_query($data, $check_email_query);

  // Check if the username already exists in the database
  $check_username_query = "SELECT * FROM login WHERE username = '$username'";
  $check_username_result = mysqli_query($data, $check_username_query);

  if (mysqli_num_rows($check_email_result) > 0) {
    echo "<script>alert('This email is already registered. Please choose another email!')</script>";
  } elseif (mysqli_num_rows($check_username_result) > 0) {
      echo "<script>alert('This username is already taken. Please choose another username!')</script>";
  } elseif ($password != $confirmpassword) {
      echo "<script>alert('Passwords do not match. Please re-enter the same password!')</script>";
  } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      echo "<script>alert('Invalid email format!')</script>";
  } elseif (!preg_match('/@gmail\.com$/', $email)) {
      echo "<script>alert('Email domain must be @gmail.com!')</script>";
  } elseif (strlen($password) < 8) {
      echo "<script>alert('Password must be at least 8 characters long!')</script>";
  } else {
      // Proceed with registration
      $sql = "INSERT INTO login (email, password, username, usertype) VALUES ('$email', '$password', '$username', '$usertype')";
      $result = mysqli_query($data, $sql);
      
      if ($result) {
          echo "<script>alert('Registration successful, please log in on the login page!')</script>";
      } else {
          echo "Registration unsuccessful!";
      }
  }

}



if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["login"])) {
  $email = $_POST["email"];
  $password = $_POST["password"];

  $sql = "SELECT * FROM login WHERE email ='$email' AND password ='$password'";
  $result = mysqli_query($data, $sql);
  $row = mysqli_fetch_array($result);

  if ($row) {
      // Set a cookie to remember the email for future logins
      setcookie("remember_email", $email, time() + (86400 * 30), "/"); // Cookie lasts for 30 days

      $_SESSION["username"] = $row['username'];
      if ($row["usertype"] == "user") {
          header("location:home.html");
          exit();
      } elseif ($row["usertype"] == "admin") {
          header("location:admin.php");
          exit();
      }
  } else {
      echo "<script>document.addEventListener('DOMContentLoaded', function() {
          var popUpDiv = document.createElement('div');
          popUpDiv.style.position = 'fixed';
          popUpDiv.style.bottom = '20px';
          popUpDiv.style.left = '50%';
          popUpDiv.style.transform = 'translateX(-50%)';
          popUpDiv.style.backgroundColor = '#f2f2f2';
          popUpDiv.style.padding = '10px';
          popUpDiv.style.border = '1px solid #ccc';
          popUpDiv.style.borderRadius = '5px';
          popUpDiv.style.boxShadow = '0 2px 4px rgba(0, 0, 0, 0.1)';
          popUpDiv.innerText = 'Email or Password incorrect';
          document.body.appendChild(popUpDiv);
      });</script>";
  }
}
?>

<!DOCTYPE html>
<html lang ="en">
<head>
    <link rel = "stylesheet" href ="loginandsignup.css">
    <title>Login or sign up? </title>
</head>


<div class="wrapper">
    <div class="title-text">
      <div class="title login">Login Form</div>
      <div class="title signup">Signup Form</div>
    </div>
    
    <div class="form-container">
      <div class="slide-controls">
        <input type="radio" name="slide" id="login" checked>
        <input type="radio" name="slide" id="signup">
        <label for="login" class="slide login">Login</label>
        <label for="signup" class="slide signup">Signup</label>
        <div class="slider-tab"></div>
      </div>
      <div class="form-inner">
        <form action="#" method ="POST" class="login" >
          <div class="field">
          <input type="text" name="email" placeholder="Email Address" required value="<?php echo isset($_COOKIE['remember_email']) ? $_COOKIE['remember_email'] : ''; ?>">

          </div>
          <div class="field">
            <input type="password" name ="password" placeholder="Password" required>
          </div>
          <div class="pass-link"><a href="#">Forgot password?</a></div>
          <div class="field btn">
            <div class="btn-layer"></div>
            <input type="submit" name ="login" value="Login">
          </div>
          <div class="signup-link">Not a member? <a href="">Signup now</a></div>
        </form>
        <form action="#" method ="POST" class="signup">
          <div class="field">
            <input type="text" name ="email" placeholder="Email Address" required>
          </div>
          <div class="field">
            <input type="username"  name="username" placeholder="username" required>
          </div>
          <div class="field">
            <input type="password" name ="password" placeholder="Password" required>
          </div>
          <div class="field">
            <input type="password" name ="confirmpassword"placeholder="Confirm password" required>
          </div>
          <div class="field btn">
            <div class="btn-layer"></div>
            <input type="submit" name ="register" value="Signup">
          </div>
        </form>
        
      </div>
    </div>
  </div>
  <script src="loginandsignup.js"></script>

</html>