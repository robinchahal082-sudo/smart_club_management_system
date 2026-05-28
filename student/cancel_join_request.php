<?php
session_start();
include "../config/db.php";

if ($_SESSION['role'] != 'student') {
    header("Location: ../auth/login.php");
    exit;
}

$student_id = $_SESSION['user_id'];
$club_id = $_GET['id'];

mysqli_query($conn,"
    DELETE FROM join_requests
    WHERE student_id='$student_id'
      AND club_id='$club_id'
      AND status='pending'
");

header("Location: join_clubs.php");
exit;
