<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

/* PENDING EVENTS */
$pending_events = mysqli_query($conn, "
    SELECT e.*, c.club_name, u.name AS leader_name
    FROM events e
    JOIN clubs c ON e.club_id = c.club_id
    JOIN users u ON e.created_by = u.user_id
    WHERE e.approval_status = 'pending'
    ORDER BY e.event_date ASC
");

/* APPROVED EVENTS */
$approved_events = mysqli_query($conn, "
    SELECT e.*, c.club_name, u.name AS leader_name
    FROM events e
    JOIN clubs c ON e.club_id = c.club_id
    JOIN users u ON e.created_by = u.user_id
    WHERE e.approval_status = 'approved'
    ORDER BY e.event_date DESC
");
?>

<div class="container-fluid">
<h2 class="mb-4">Event Approval</h2>

<!-- PENDING EVENTS -->
<h4>Pending Events</h4>
<?php if(mysqli_num_rows($pending_events) == 0){ ?>
    <div class="alert alert-info">No pending events.</div>
<?php } else { ?>
<table class="table table-bordered table-striped">
<tr>
    <th>Event</th>
    <th>Club</th>
    <th>Leader</th>
    <th>Date</th>
    <th>Action</th>
</tr>
<?php while($e = mysqli_fetch_assoc($pending_events)){ ?>
<tr>
    <td><?= $e['title'] ?></td>
    <td><?= $e['club_name'] ?></td>
    <td><?= $e['leader_name'] ?></td>
    <td><?= $e['event_date'] ?></td>
    <td>
        <a href="event_view.php?id=<?= $e['event_id'] ?>" class="btn btn-info btn-sm">View</a>
        <a href="event_action.php?id=<?= $e['event_id'] ?>&a=approve"
           class="btn btn-success btn-sm"
           onclick="return confirm('Approve this event?')">Approve</a>
        <a href="event_action.php?id=<?= $e['event_id'] ?>&a=reject"
           class="btn btn-danger btn-sm"
           onclick="return confirm('Reject this event?')">Reject</a>
    </td>
</tr>
<?php } ?>
</table>
<?php } ?>

<!-- APPROVED EVENTS -->
<h4 class="mt-5">Approved Events</h4>
<?php if(mysqli_num_rows($approved_events) == 0){ ?>
    <div class="alert alert-warning">No approved events yet.</div>
<?php } else { ?>
<table class="table table-bordered table-striped">
<tr>
    <th>Event</th>
    <th>Club</th>
    <th>Leader</th>
    <th>Date</th>
    <th>Status</th>
    <th>View</th>
</tr>
<?php while($e = mysqli_fetch_assoc($approved_events)){ ?>
<tr>
    <td><?= $e['title'] ?></td>
    <td><?= $e['club_name'] ?></td>
    <td><?= $e['leader_name'] ?></td>
    <td><?= $e['event_date'] ?></td>
    <td><span class="badge bg-success">Approved</span></td>
    <td>
        <a href="event_view.php?id=<?= $e['event_id'] ?>" class="btn btn-info btn-sm">View</a>
    </td>
</tr>
<?php } ?>
</table>
<?php } ?>

</div>

<?php include "../includes/footer.php"; ?>
