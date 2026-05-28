<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

$club_id = $_GET['id'];

/* STEP 1: Delete event results */
mysqli_query($conn, "
    DELETE er FROM event_results er
    JOIN events e ON er.event_id = e.event_id
    WHERE e.club_id = '$club_id'
");

/* STEP 2: Delete event participants */
mysqli_query($conn, "
    DELETE ep FROM event_participants ep
    JOIN events e ON ep.event_id = e.event_id
    WHERE e.club_id = '$club_id'
");

/* STEP 3: Delete events */
mysqli_query($conn, "DELETE FROM events WHERE club_id='$club_id'");

/* STEP 4: Delete club members */
mysqli_query($conn, "DELETE FROM club_members WHERE club_id='$club_id'");

/* STEP 5: Delete join requests */
mysqli_query($conn, "DELETE FROM join_requests WHERE club_id='$club_id'");

/* STEP 6: Finally delete club */
mysqli_query($conn, "DELETE FROM clubs WHERE club_id='$club_id'");

header("Location: clubs_manage.php");
exit;
