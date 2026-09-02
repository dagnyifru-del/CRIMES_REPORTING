<?php
session_start();
include("db.php");

$message="";
if(isset($_POST['login'])){
    $role=$_POST['role'];
    $email=$_POST['email'];
    $password=$_POST['password'];

    $sql="SELECT * FROM users WHERE email=? AND password=? AND role=?";
    $stmt=$conn->prepare($sql);
    $stmt->bind_param("sss",$email,$password,$role);
    $stmt->execute();
    $result=$stmt->get_result();

    if($result->num_rows>0){
        $_SESSION['role']=$role;
        $_SESSION['email']=$email;

        if($role=='admin'){
            header("Location: admin_dashboard.php");
        } elseif($role=='police'){
            header("Location: police_dashboard.php");
        } else {
            header("Location: reporter_dashboard.php");
        }
        exit();
    } else {
        $message="❌ Invalid credentials for ".$role."!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <link rel="stylesheet" href="styles.css">
  <script>
    function validateForm(){
      let role=document.forms["loginForm"]["role"].value;
      let email=document.forms["loginForm"]["email"].value;
      let pass=document.forms["loginForm"]["password"].value;
      if(role=="" || email=="" || pass==""){
        alert("⚠️ Please fill all fields!");
        return false;
      }
      return true;
    }
  </script>
</head>
<body>
  <nav class="navbar">
    <div class="logo">Crime Reporting System</div>
    <ul class="nav-links">
      <li><a href="index.html">Home</a></li>
      <li><a href="about.html">About</a></li>
      <li><a href="contact.html">Contact Us</a></li>
      <li><a href="login.php">Login</a></li>
    </ul>
  </nav>

  <section class="hero">
    <h2>Login</h2>
    <form name="loginForm" method="POST" onsubmit="return validateForm()">
      <label>Select Role:</label>
      <select name="role" required>
        <option value="">--Choose Role--</option>
        <option value="admin">Admin</option>
        <option value="police">Police</option>
        <option value="reporter">Reporter</option>
      </select><br><br>
      <input type="email" name="email" placeholder="Email" required><br>
      <input type="password" name="password" placeholder="Password" required><br>
      <button type="submit" name="login">Login</button>
    </form>
    <p style="color:red;"><?php echo $message; ?></p>
  </section>
</body>
</html>
