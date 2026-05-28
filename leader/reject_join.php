<?php
session_start();
include "../config/db.php";

if ($_SESSION['role'] != 'leader') {
    header("Location: ../auth/login.php");
    exit;
}

$request_id = intval($_GET['request_id']);

mysqli_query($conn, "
    UPDATE join_requests
    SET status='rejected'
    WHERE request_id='$request_id'
");

header("Location: join_requests.php");
exit;
