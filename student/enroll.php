<?php
session_start();
include "../config/db.php";

if ($_SESSION['role'] != 'student') {
    header("Location: ../auth/login.php");
    exit;
}

$student_id = $_SESSION['user_id'];
$event_id   = intval($_GET['event_id']);

/* Duplicate check */
$check = mysqli_query($conn,"
    SELECT id FROM event_participants
    WHERE event_id='$event_id'
    AND student_id='$student_id'
");

if (mysqli_num_rows($check) == 0) {
    mysqli_query($conn,"
        INSERT INTO event_participants (event_id, student_id)
        VALUES ('$event_id','$student_id')
    ");
}

header("Location: my_enrollments.php");
exit;
