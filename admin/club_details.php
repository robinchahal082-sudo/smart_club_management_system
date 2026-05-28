<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role']!='admin'){
    header("Location: ../auth/login.php"); exit;
}

include "../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$club_id = $_GET['id'];

/* Club info */
$club = mysqli_fetch_assoc(
    mysqli_query($conn,"
        SELECT c.club_name, 
               l.name AS leader, 
               f.name AS faculty
        FROM clubs c
        LEFT JOIN users l ON c.leader_id=l.user_id
        LEFT JOIN users f ON c.faculty_id=f.user_id
        WHERE c.club_id='$club_id'
    ")
);

/* Counts */
$totalEvents = mysqli_fetch_row(
    mysqli_query($conn,"SELECT COUNT(*) FROM events WHERE club_id='$club_id'")
)[0];

$totalMembers = mysqli_fetch_row(
    mysqli_query($conn,"SELECT COUNT(*) FROM club_members WHERE club_id='$club_id'")
)[0];

$totalParticipants = mysqli_fetch_row(
    mysqli_query($conn,"
        SELECT COUNT(DISTINCT student_id) 
        FROM event_participants ep
        JOIN events e ON ep.event_id=e.event_id
        WHERE e.club_id='$club_id'
    ")
)[0];
?>

<h2>Club Full Report</h2>

<div class="card p-3">
    <h4><?= $club['club_name'] ?></h4>
    <p><b>Leader:</b> <?= $club['leader'] ?></p>
    <p><b>Faculty:</b> <?= $club['faculty'] ?></p>

    <hr>

    <p><b>Total Events Conducted:</b> <?= $totalEvents ?></p>
    <p><b>Total Club Members:</b> <?= $totalMembers ?></p>
    <p><b>Total Student Participation:</b> <?= $totalParticipants ?></p>
</div>

<a href="clubs_manage.php" class="btn btn-secondary mt-3">Back</a>

<?php include "../includes/footer.php"; ?>
