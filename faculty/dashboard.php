<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role']!='faculty'){
    header("Location: ../auth/login.php"); exit;
}

include "../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$faculty_id = $_SESSION['user_id'];

/* Counts */
$clubs = mysqli_fetch_row(mysqli_query($conn,"
    SELECT COUNT(*) FROM clubs WHERE faculty_id='$faculty_id'
"))[0];

$events = mysqli_fetch_row(mysqli_query($conn,"
    SELECT COUNT(*) FROM events e 
    JOIN clubs c ON e.club_id=c.club_id 
    WHERE c.faculty_id='$faculty_id'
"))[0];

$members = mysqli_fetch_row(mysqli_query($conn,"
    SELECT COUNT(*) FROM club_members cm 
    JOIN clubs c ON cm.club_id=c.club_id 
    WHERE c.faculty_id='$faculty_id'
"))[0];

$results = mysqli_fetch_row(mysqli_query($conn,"
    SELECT COUNT(*) FROM event_results er 
    JOIN events e ON er.event_id=e.event_id 
    JOIN clubs c ON e.club_id=c.club_id 
    WHERE c.faculty_id='$faculty_id'
"))[0];
?>

<h2>Faculty Dashboard</h2>

<div class="row mt-4">
    <div class="col-md-3"><div class="card p-3 text-center">Clubs<br><h3><?= $clubs ?></h3></div></div>
    <div class="col-md-3"><div class="card p-3 text-center">Events<br><h3><?= $events ?></h3></div></div>
    <div class="col-md-3"><div class="card p-3 text-center">Members<br><h3><?= $members ?></h3></div></div>
    <div class="col-md-3"><div class="card p-3 text-center">Results<br><h3><?= $results ?></h3></div></div>
</div>

<?php include "../includes/footer.php"; ?>
