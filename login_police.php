<?php
session_start();
include("includes/db.php");

if(isset($_POST['login'])){
    $email=$_POST['email'];
    $password=$_POST['password'];
    $sql="SELECT * FROM users WHERE email=? AND password=? AND role='police'";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param("ss",$email,$password);
    $stmt->execute();
    $result=$stmt->get_result();
    if($result->num_rows>0){
        $_SESSION['role']='police';
        $_SESSION['email']=$email;
        header("Location: police_dashboard.php");
        exit();
    } else {
        echo "❌ Invalid police credentials!";
    }
}
?>
<form method="POST">
  <input type="email" name="email" placeholder="Police Email" required>
  <input type="password" name="password" placeholder="Password" required>
  <button type="submit" name="login">Login as Police</button>
</form>
