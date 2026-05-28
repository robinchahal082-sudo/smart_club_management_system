<?php
session_start();
include "../config/db.php";

if ($_SESSION['role'] != 'student') {
    header("Location: ../auth/login.php");
    exit;
}

$student_id = $_SESSION['user_id'];
$event_id   = intval($_GET['event_id']);

mysqli_query($conn,"
    DELETE FROM event_participants
    WHERE event_id='$event_id'
    AND student_id='$student_id'
");

header("Location: my_enrollments.php");
exit;
