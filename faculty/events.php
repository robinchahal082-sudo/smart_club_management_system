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

/* EVENTS */
$events = mysqli_query($conn, "
    SELECT 
        e.event_id,
        e.title,
        c.club_name,
        e.event_date,
        (SELECT COUNT(*) FROM event_participants ep WHERE ep.event_id=e.event_id) AS enrolled,
        (SELECT COUNT(*) FROM event_results er WHERE er.event_id=e.event_id) AS result_count
    FROM events e
    JOIN clubs c ON e.club_id=c.club_id
    WHERE e.club_id='$club_id'
    ORDER BY e.event_date DESC
");
?>

<div class="container-fluid">
<h2 class="mb-4">Event Monitoring</h2>

<table class="table table-bordered table-striped">
<thead>
<tr>
    <th>Event</th>
    <th>Club</th>
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
    <td><?= $e['club_name'] ?></td>
    <td><?= $e['event_date'] ?></td>
    <td><?= $e['enrolled'] ?></td>
    <td>
        <?php if($e['result_count'] > 0){ ?>
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
    <td colspan="5" class="text-center">No events found</td>
</tr>
<?php } ?>
</tbody>
</table>

</div>

<?php include "../includes/footer.php"; ?>
