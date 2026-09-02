<?php
include("db.php");
$message="";
if(isset($_POST['register'])){
    $email=$_POST['email'];
    $password=$_POST['password'];

    // check if email already exists
    $check=mysqli_query($conn,"SELECT * FROM users WHERE email='$email'");
    if(mysqli_num_rows($check)>0){
        $message="❌ Email already registered!";
    } else {
        mysqli_query($conn,"INSERT INTO users(email,password,role) VALUES('$email','$password','reporter')");
        $message="✅ Reporter account created! You can now login.";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Reporter Signup</title>
</head>
<body>
  <h2>Reporter Signup</h2>
  <form method="POST">
    <input type="email" name="email" placeholder="Your Real Email" required><br>
    <input type="password" name="password" placeholder="Password" required><br>
    <button type="submit" name="register">Signup</button>
  </form>
  <p><?php echo $message; ?></p>
</body>
</html>
