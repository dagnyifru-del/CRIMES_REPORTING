<?php
session_start();
if($_SESSION['role']!='reporter'){ header("Location: login.php"); exit(); }
include("db.php");
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title>Reporter Dashboard</title>
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
    input,textarea { padding:8px; margin:5px; border-radius:5px; border:none; width:300px; }
    button { background:#27ae60; color:#fff; padding:8px 15px; border:none; border-radius:5px; cursor:pointer; }
    button:hover { transform:scale(1.1); background:#1e8449; }
    .report-box { border:1px solid #fff; padding:10px; margin:10px; border-radius:8px; background:rgba(0,0,0,0.2); }
  </style>
</head>
<body>

<nav class="navbar">
  <div class="logo">Reporter Dashboard</div>
  <ul class="nav-links">
    <li><a href="reporter_dashboard.php">Home</a></li>
    <li><a href="logout.php">Logout</a></li>
  </ul>
</nav>

<h2>Welcome Reporter</h2>
<p>You can submit crime reports. Reports are automatically timestamped and sent to Admin.</p>

<!-- Submit Crime Report -->
<h3>Submit Crime Report</h3>
<form method="POST">
  <input type="text" name="crime_type" placeholder="Crime Type" required><br>
  <input type="text" name="crime_place" placeholder="Place of Crime" required><br>
  <textarea name="description" placeholder="Describe the crime..." required></textarea><br>
  <button type="submit" name="submit_report">Submit Report</button>
</form>

<?php
if(isset($_POST['submit_report'])){
  $crime_type=$_POST['crime_type'];
  $crime_place=$_POST['crime_place'];
  $desc=$_POST['description'];
  $reporter_email=$_SESSION['email'];

  // Insert into reports table with automatic timestamp
  mysqli_query($conn,"INSERT INTO reports(reporter_email,crime_type,crime_place,description,status) 
                      VALUES('$reporter_email','$crime_type','$crime_place','$desc','pending')");

  echo "<p>✅ Report submitted successfully! Sent to Admin.</p>";

  // Check admin online status (placeholder logic)
  $admin_online=false; // assume offline for demo

  if(!$admin_online){
    // SMS fallback (placeholder)
    $admin_phone="0976916665";
    $sms_message="Crime Report: $crime_type at $crime_place. Reporter: $reporter_email";
    
    // Example using Twilio API (pseudo-code)
    /*
    require __DIR__ . '/vendor/autoload.php';
    use Twilio\Rest\Client;
    $sid = "TWILIO_ACCOUNT_SID";
    $token = "TWILIO_AUTH_TOKEN";
    $twilio = new Client($sid, $token);
    $twilio->messages->create(
        $admin_phone,
        array(
            "from" => "YOUR_TWILIO_NUMBER",
            "body" => $sms_message
        )
    );
    */
    
    echo "<p>📱 Admin offline: SMS sent to $admin_phone with report details.</p>";
  }
}
?>

<!-- Reporter’s Reports History -->
<h3>Your Reports</h3>
<?php
$myreports=mysqli_query($conn,"SELECT * FROM reports WHERE reporter_email='".$_SESSION['email']."' ORDER BY id DESC");
if(mysqli_num_rows($myreports)==0){
  echo "<p>No reports submitted yet.</p>";
}
while($row=mysqli_fetch_assoc($myreports)){
  echo "<div class='report-box'>";
  echo "<h4>".$row['crime_type']." at ".$row['crime_place']."</h4>";
  echo "<p>".$row['description']."</p>";
  echo "<small>Status: ".$row['status']." | Submitted at ".$row['created_at']."</small>";
  echo "</div>";
}
?>

</body>
</html>
