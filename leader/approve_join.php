<?php
session_start();
include "../config/db.php";

if ($_SESSION['role'] != 'leader') {
    header("Location: ../auth/login.php");
    exit;
}

$request_id = intval($_GET['request_id']);

$r = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT student_id, club_id
    FROM join_requests
    WHERE request_id='$request_id'
"));

mysqli_query($conn, "
    INSERT INTO club_members (student_id, club_id)
    VALUES ('{$r['student_id']}', '{$r['club_id']}')
");

mysqli_query($conn, "
    UPDATE join_requests
    SET status='approved'
    WHERE request_id='$request_id'
");

header("Location: join_requests.php");
exit;
