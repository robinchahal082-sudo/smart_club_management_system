<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role']!='leader'){
    header("Location: ../auth/login.php"); exit;
}

include "../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$leader_id = $_SESSION['user_id'];

/* Club */
$club = mysqli_fetch_assoc(
    mysqli_query($conn,"SELECT club_id, club_name FROM clubs WHERE leader_id='$leader_id'")
);
$club_id = $club['club_id'] ?? 0;

/* Counts */
$members = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM club_members WHERE club_id='$club_id'"))[0];
$events  = mysqli_fetch_row(mysqli_query($conn,"SELECT COUNT(*) FROM events WHERE club_id='$club_id'"))[0];
$enrolls = mysqli_fetch_row(mysqli_query($conn,"
    SELECT COUNT(DISTINCT student_id) 
    FROM event_participants ep 
    JOIN events e ON ep.event_id=e.event_id 
    WHERE e.club_id='$club_id'
"))[0];
$results = mysqli_fetch_row(mysqli_query($conn,"
    SELECT COUNT(*) 
    FROM event_results er 
    JOIN events e ON er.event_id=e.event_id 
    WHERE e.club_id='$club_id'
"))[0];
?>

<h2>Leader Dashboard</h2>
<h5>Club: <?= $club['club_name'] ?? 'Not Assigned' ?></h5>

<div class="row mt-4">
    <div class="col-md-3"><div class="card p-3 text-center">Members<br><h3><?= $members ?></h3></div></div>
    <div class="col-md-3"><div class="card p-3 text-center">Events<br><h3><?= $events ?></h3></div></div>
    <div class="col-md-3"><div class="card p-3 text-center">Enrollments<br><h3><?= $enrolls ?></h3></div></div>
    <div class="col-md-3"><div class="card p-3 text-center">Results<br><h3><?= $results ?></h3></div></div>
</div>

<?php include "../includes/footer.php"; ?>
