<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$event_id = $_GET['id'] ?? 0;

/* EVENT DETAILS */
$event = mysqli_fetch_assoc(mysqli_query($conn, "
    SELECT e.*, c.club_name, u.name AS leader_name
    FROM events e
    JOIN clubs c ON e.club_id = c.club_id
    JOIN users u ON e.created_by = u.user_id
    WHERE e.event_id = '$event_id'
"));

if (!$event) {
    echo "<div class='container-fluid'><div class='alert alert-danger'>Invalid Event</div></div>";
    include "../includes/footer.php";
    exit;
}

/* PARTICIPANTS LIST */
$participants = mysqli_query($conn, "
    SELECT u.name, u.email, ep.enrolled_at
    FROM event_participants ep
    JOIN users u ON ep.student_id = u.user_id
    WHERE ep.event_id = '$event_id'
    ORDER BY u.name ASC
");

/* TOTAL PARTICIPANTS */
$total_participants = mysqli_num_rows($participants);
?>

<div class="container-fluid">
<h2 class="mb-4">Event Details</h2>

<!-- EVENT DETAILS -->
<table class="table table-bordered">
<tr><th>Event Title</th><td><?= $event['title'] ?></td></tr>
<tr><th>Club</th><td><?= $event['club_name'] ?></td></tr>
<tr><th>Leader</th><td><?= $event['leader_name'] ?></td></tr>
<tr><th>Date</th><td><?= $event['event_date'] ?></td></tr>
<tr><th>Description</th><td><?= $event['description'] ?></td></tr>
<tr><th>Status</th><td><?= ucfirst($event['approval_status']) ?></td></tr>
<tr><th>Total Participants</th><td><?= $total_participants ?></td></tr>
</table>

<!-- PARTICIPANTS LIST -->
<h4 class="mt-5">Participants</h4>

<?php if($total_participants == 0){ ?>
    <div class="alert alert-info">No students enrolled in this event.</div>
<?php } else { ?>
<table class="table table-bordered table-hover">
<tr>
    <th>#</th>
    <th>Student Name</th>
    <th>Email</th>
    <th>Enrolled At</th>
</tr>
<?php $i=1; while($p = mysqli_fetch_assoc($participants)){ ?>
<tr>
    <td><?= $i++ ?></td>
    <td><?= $p['name'] ?></td>
    <td><?= $p['email'] ?></td>
    <td><?= date("d M Y", strtotime($p['enrolled_at'])) ?></td>
</tr>
<?php } ?>
</table>
<?php } ?>

<a href="javascript:history.back()" class="btn btn-secondary mt-3">← Back</a>
</div>

<?php include "../includes/footer.php"; ?>
