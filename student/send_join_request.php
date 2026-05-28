<?php
session_start();
if ($_SESSION['role'] != 'student') {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";

$student_id = $_SESSION['user_id'];
$club_id    = $_GET['id'] ?? null;

if (!$club_id) {
    header("Location: join_clubs.php");
    exit;
}

/* 1️⃣ Already member check */
$member = mysqli_num_rows(mysqli_query($conn,"
    SELECT id FROM club_members
    WHERE club_id='$club_id'
    AND student_id='$student_id'
"));

if ($member > 0) {
    header("Location: join_clubs.php");
    exit;
}

/* 2️⃣ Pending request check */
$pending = mysqli_num_rows(mysqli_query($conn,"
    SELECT request_id FROM join_requests
    WHERE club_id='$club_id'
    AND student_id='$student_id'
    AND status='pending'
"));

if ($pending > 0) {
    header("Location: join_clubs.php");
    exit;
}

/* ✅ Allow rejoin even if removed earlier */
mysqli_query($conn,"
    INSERT INTO join_requests (club_id, student_id, status, request_date)
    VALUES ('$club_id','$student_id','pending',NOW())
");

header("Location: join_clubs.php");
exit;
