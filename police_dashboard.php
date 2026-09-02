<?php
session_start();
if($_SESSION['role']!='police'){ header("Location: login.php"); exit(); }
include("db.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Police Dashboard</title>
  <link rel="stylesheet" href="style.css">
  <style>
    body { font-family: Arial; background: linear-gradient(135deg,#1abc9c,#16a085); color:#fff; }
    .navbar { background:#2c3e50; padding:15px; }
    .navbar .logo { font-size:20px; font-weight:bold; color:#ecf0f1; }
    .nav-links { list-style:none; }
    .nav-links li { display:inline; margin:0 10px; }
    .nav-links a { color:#ecf0f1; text-decoration:none; }
    h2,h3 { color:#f1c40f; }
    form { margin:10px 0; }
    textarea { width:400px; height:100px; padding:8px; border-radius:5px; border:none; }
    button { background:#2980b9; color:#fff; padding:8px 15px; border:none; border-radius:5px; cursor:pointer; }
    button:hover { transform:scale(1.1); background:#1f6391; }
    .report-box { border:1px solid #fff; padding:10px; margin:10px; border-radius:8px; background:rgba(0,0,0,0.2); }
    .feedback-box { border:1px dashed #fff; padding:10px; margin:10px; border-radius:8px; background:rgba(255,255,255,0.1); }
  </style>
</head>
<body>

<nav class="navbar">
  <div class="logo">Police Dashboard</div>
  <ul class="nav-links">
    <li><a href="police_dashboard.php">Home</a></li>
    <li><a href="logout.php">Logout</a></li>
  </ul>
</nav>

<h2>Welcome Police</h2>
<p>You can view forwarded reports and send feedback to Admin.</p>

<!-- Forwarded Reports -->
<h3>Forwarded Reports</h3>
<?php
$q=mysqli_query($conn,"SELECT * FROM reports WHERE status='forwarded' ORDER BY id DESC");
if(mysqli_num_rows($q)==0){
  echo "<p>No forwarded reports yet.</p>";
}
while($row=mysqli_fetch_assoc($q)){
  echo "<div class='report-box'>";
  echo "<h4>".$row['crime_type']." at ".$row['crime_place']."</h4>";
  echo "<p>".$row['description']."</p>";
  echo "<small>Reported by: ".$row['reporter_email']." at ".$row['created_at']."</small><br><br>";
  echo "<form method='POST'>
          <input type='hidden' name='report_id' value='".$row['id']."'>
          <textarea name='feedback' placeholder='Write feedback here...' required></textarea><br>
          <button type='submit' name='send_feedback'>Send Feedback</button>
        </form>";
  echo "</div>";
}

if(isset($_POST['send_feedback'])){
  $rid=$_POST['report_id'];
  $fb=$_POST['feedback'];
  $police_email=$_SESSION['email'];
  mysqli_query($conn,"INSERT INTO feedback(police_email,report_id,feedback_text) VALUES('$police_email','$rid','$fb')");
  echo "<p>✅ Feedback sent to Admin!</p>";
}
?>

<!-- Feedback History -->
<h3>Your Feedback History</h3>
<?php
$fq=mysqli_query($conn,"SELECT f.*, r.crime_type, r.crime_place FROM feedback f 
                        JOIN reports r ON f.report_id=r.id 
                        WHERE f.police_email='".$_SESSION['email']."' ORDER BY f.id DESC");
if(mysqli_num_rows($fq)==0){
  echo "<p>No feedback submitted yet.</p>";
}
while($f=mysqli_fetch_assoc($fq)){
  echo "<div class='feedback-box'>";
  echo "<strong>Report:</strong> ".$f['crime_type']." at ".$f['crime_place']."<br>";
  echo "<strong>Feedback:</strong> ".$f['feedback_text']."<br>";
  echo "<small>Submitted at ".$f['created_at']."</small>";
  echo "</div>";
}
?>

</body>
</html>
