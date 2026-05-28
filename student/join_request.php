<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: ../auth/login.php");
    exit;
}

$student_id = $_SESSION['user_id'];
$club_id    = $_GET['id'];

/* Prevent duplicate request */
$check = mysqli_query($conn, "
    SELECT * FROM join_requests 
    WHERE club_id='$club_id' AND student_id='$student_id'
");

if (mysqli_num_rows($check) == 0) {
    mysqli_query($conn,
        "INSERT INTO join_requests (club_id, student_id)
         VALUES ('$club_id','$student_id')"
    );
}

header("Location: dashboard.php");
