<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'faculty') {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$faculty_id = $_SESSION['user_id'];

/* FACULTY CLUB */
$club = mysqli_fetch_assoc(mysqli_query(
    $conn, "SELECT club_id FROM clubs WHERE faculty_id='$faculty_id'"
));
$club_id = $club['club_id'];

/* EVENTS WITH RESULT STATUS */
$events = mysqli_query($conn, "
    SELECT 
        e.event_id,
        e.title,
        e.event_date,
        (SELECT COUNT(*) FROM event_participants ep WHERE ep.event_id=e.event_id) AS enrolled,
        (SELECT COUNT(*) FROM event_results er WHERE er.event_id=e.event_id) AS result_count
    FROM events e
    WHERE e.club_id='$club_id'
    ORDER BY e.event_date DESC
");
?>

<div class="container-fluid">
<h2 class="mb-4">Event Results</h2>

<table class="table table-bordered table-striped">
<thead>
<tr>
    <th>Event</th>
    <th>Date</th>
    <th>Enrolled</th>
    <th>Results</th>
</tr>
</thead>
<tbody>
<?php if(mysqli_num_rows($events)>0){ ?>
<?php while($e=mysqli_fetch_assoc($events)){ ?>
<tr>
    <td><?= $e['title'] ?></td>
    <td><?= $e['event_date'] ?></td>
    <td><?= $e['enrolled'] ?></td>
    <td>
        <?php if($e['result_count']>0){ ?>
            <a href="results.php?event_id=<?= $e['event_id'] ?>" 
               class="btn btn-success btn-sm">
               View Results
            </a>
        <?php } else { ?>
            <span class="badge bg-warning">Not Published</span>
        <?php } ?>
    </td>
</tr>
<?php } ?>
<?php } else { ?>
<tr>
    <td colspan="4" class="text-center">No events found</td>
</tr>
<?php } ?>
</tbody>
</table>

<?php
/* ======================
   SHOW EVENT RESULTS
====================== */
if(isset($_GET['event_id'])){
    $event_id = $_GET['event_id'];

    $results = mysqli_query($conn, "
        SELECT u.name, er.score, er.position
        FROM event_results er
        JOIN users u ON er.student_id=u.user_id
        WHERE er.event_id='$event_id'
        ORDER BY er.position ASC
    ");
?>
<hr>
<h3>Results</h3>

<table class="table table-bordered table-striped">
<tr>
    <th>Student</th>
    <th>Score</th>
    <th>Rank</th>
</tr>

<?php if(mysqli_num_rows($results)>0){ ?>
<?php while($r=mysqli_fetch_assoc($results)){ ?>
<tr>
    <td><?= $r['name'] ?></td>
    <td><?= $r['score'] ?></td>
    <td><?= $r['position'] ?></td>
</tr>
<?php } ?>
<?php } else { ?>
<tr>
    <td colspan="3" class="text-center">No results available</td>
</tr>
<?php } ?>
</table>
<?php } ?>

</div>

<?php include "../includes/footer.php"; ?>
