<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role']!='student'){
    header("Location: ../auth/login.php"); exit;
}

include "../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$student_id = $_SESSION['user_id'];

/* Counts */
$clubs = mysqli_fetch_row(mysqli_query($conn,"
    SELECT COUNT(*) FROM club_members WHERE student_id='$student_id'
"))[0];

$enrolled = mysqli_fetch_row(mysqli_query($conn,"
    SELECT COUNT(*) FROM event_participants WHERE student_id='$student_id'
"))[0];

$results = mysqli_fetch_row(mysqli_query($conn,"
    SELECT COUNT(*) FROM event_results WHERE student_id='$student_id'
"))[0];
?>

<h2>Student Dashboard</h2>

<div class="row mt-4">
    <div class="col-md-4"><div class="card p-3 text-center">Joined Clubs<br><h3><?= $clubs ?></h3></div></div>
    <div class="col-md-4"><div class="card p-3 text-center">Enrolled Events<br><h3><?= $enrolled ?></h3></div></div>
    <div class="col-md-4"><div class="card p-3 text-center">Results<br><h3><?= $results ?></h3></div></div>
</div>

<?php include "../includes/footer.php"; ?>
