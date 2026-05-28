<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$id = $_GET['id'];
$a  = $_GET['a'];

if ($a == 'approve') {
    mysqli_query($conn, "UPDATE events SET approval_status='approved' WHERE event_id='$id'");
}
if ($a == 'reject') {
    mysqli_query($conn, "UPDATE events SET approval_status='rejected' WHERE event_id='$id'");
}

header("Location: events.php");
