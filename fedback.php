<?php
include 'db.php';
session_start();
if ($_SESSION['role'] !== 'police') { die("Access denied"); }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $report_id = $_POST['report_id'];
    $feedback_text = $_POST['feedback_text'];
    $police_id = $_SESSION['id'];

    $stmt = $conn->prepare("INSERT INTO feedback(report_id,police_id,feedback_text) VALUES(?,?,?)");
    $stmt->bind_param("iis", $report_id, $police_id, $feedback_text);
    $stmt->execute();

    $conn->query("UPDATE reports SET status='closed' WHERE id=$report_id");
    echo "Feedback sent!";
}
?>
