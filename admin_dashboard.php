<?php
session_start();
if($_SESSION['role']!='admin'){ header("Location: login.php"); exit(); }
include("db.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Admin Dashboard</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body { font-family: Arial; background: linear-gradient(135deg,#1abc9c,#16a085); color:#fff; }
    .navbar { background:#34495e; padding:15px; }
    .navbar .logo { font-size:20px; font-weight:bold; color:#ecf0f1; }
    .nav-links { list-style:none; }
    .nav-links li { display:inline; margin:0 10px; }
    .nav-links a { color:#ecf0f1; text-decoration:none; }
    h2,h3 { color:#f1c40f; }
    form { margin:10px 0; }
    input,textarea { padding:8px; margin:5px; border-radius:5px; border:none; }
    button { background:#e74c3c; color:#fff; padding:8px 15px; border:none; border-radius:5px; cursor:pointer; }
    button:hover { transform:scale(1.1); background:#c0392b; }
    .report-box { border:1px solid #fff; padding:10px; margin:10px; border-radius:8px; background:rgba(0,0,0,0.2); }
  </style>
</head>
<body>

<nav class="navbar">
  <div class="logo">Admin Dashboard</div>
  <ul class="nav-links">
    <li><a href="admin_dashboard.php">Home</a></li>
    <li><a href="logout.php">Logout</a></li>
  </ul>
</nav>

<h2>Welcome Admin</h2>

<!-- Change Password -->
<h3>Change Admin Password</h3>
<form method="POST">
  <input type="password" name="new_pass" placeholder="New Password" required>
  <button type="submit" name="change_pass">Change Password</button>
</form>

<?php
if(isset($_POST['change_pass'])){
  $new=$_POST['new_pass'];
  $email=$_SESSION['email'];
  mysqli_query($conn,"UPDATE users SET password='$new' WHERE email='$email' AND role='admin'");
  echo "<p>✅ Password changed!</p>";
}
?>

<!-- Add Police -->
<h3>Add Police</h3>
<form method="POST">
  <input type="email" name="police_email" placeholder="Police Email" required>
  <input type="password" name="police_pass" placeholder="Password" required>
  <button type="submit" name="add_police">Add Police</button>
</form>

<?php
if(isset($_POST['add_police'])){
  $e=$_POST['police_email'];
  $p=$_POST['police_pass'];
  mysqli_query($conn,"INSERT INTO users(email,password,role) VALUES('$e','$p','police')");
  echo "<p>✅ Police added!</p>";
}
?>

<!-- List Police -->
<h3>Police Accounts</h3>
<?php
$q=mysqli_query($conn,"SELECT * FROM users WHERE role='police'");
while($row=mysqli_fetch_assoc($q)){
  echo "<p>".$row['email']." 
        <form method='POST' style='display:inline'>
          <input type='hidden' name='police_id' value='".$row['id']."'>
          <button type='submit' name='delete_police'>Delete</button>
        </form></p>";
}
if(isset($_POST['delete_police'])){
  $pid=$_POST['police_id'];
  mysqli_query($conn,"DELETE FROM users WHERE id='$pid'");
  echo "<p>❌ Police deleted!</p>";
}
?>

<!-- Reports -->
<h3>Reports</h3>
<?php
$r=mysqli_query($conn,"SELECT * FROM reports ORDER BY id DESC");
while($row=mysqli_fetch_assoc($r)){
  echo "<div class='report-box'>";
  echo "<h4>".$row['crime_type']." at ".$row['crime_place']."</h4>";
  echo "<p>".$row['description']."</p>";
  echo "<small>Reported by: ".$row['reporter_email']." at ".$row['created_at']."</small><br><br>";
  echo "<form method='POST'>
          <input type='hidden' name='report_id' value='".$row['id']."'>
          <button type='submit' name='forward'>Forward to Police</button>
          <button type='submit' name='delete_report'>Delete Report</button>
        </form>";
  echo "</div>";
}

if(isset($_POST['forward'])){
  $rid=$_POST['report_id'];
  mysqli_query($conn,"UPDATE reports SET status='forwarded' WHERE id='$rid'");
  echo "<p>✅ Report forwarded!</p>";
}

if(isset($_POST['delete_report'])){
  $rid=$_POST['report_id'];
  // Haquu feedback walqabatu
  mysqli_query($conn,"DELETE FROM feedback WHERE report_id='$rid'");
  // Booda report haquu
  mysqli_query($conn,"DELETE FROM reports WHERE id='$rid'");
  echo "<p>❌ Report deleted!</p>";
}
?>

<!-- Feedback Section -->
<h3>Feedback</h3>
<?php
$fq=mysqli_query($conn,"SELECT f.*, r.crime_type, r.crime_place 
                        FROM feedback f 
                        JOIN reports r ON f.report_id=r.id 
                        ORDER BY f.id DESC");
while($row=mysqli_fetch_assoc($fq)){
  echo "<div class='report-box'>";
  echo "<h4>Feedback on: ".$row['crime_type']." at ".$row['crime_place']."</h4>";
  echo "<p>".$row['feedback_text']."</p>";
  echo "<small>By: ".$row['police_email']." at ".$row['created_at']."</small>";
  echo "</div>";
}
?>

</body>
</html>
