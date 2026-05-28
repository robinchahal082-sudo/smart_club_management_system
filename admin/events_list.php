<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$type = $_GET['type'] ?? 'pending';

/* PAGE TITLE + QUERY */
if ($type == 'pending') {
    $title = "Pending Events";
    $query = "
        SELECT e.*, c.club_name, u.name AS leader_name
        FROM events e
        JOIN clubs c ON e.club_id = c.club_id
        JOIN users u ON e.created_by = u.user_id
        WHERE e.approval_status = 'pending'
        ORDER BY e.event_date ASC
    ";
}
elseif ($type == 'upcoming') {
    $title = "Upcoming Events";
    $query = "
        SELECT e.*, c.club_name, u.name AS leader_name
        FROM events e
        JOIN clubs c ON e.club_id = c.club_id
        JOIN users u ON e.created_by = u.user_id
        WHERE e.approval_status = 'approved'
        AND e.event_date >= CURDATE()
        ORDER BY e.event_date ASC
    ";
}
elseif ($type == 'completed') {
    $title = "Completed Events";
    $query = "
        SELECT e.*, c.club_name, u.name AS leader_name
        FROM events e
        JOIN clubs c ON e.club_id = c.club_id
        JOIN users u ON e.created_by = u.user_id
        WHERE e.approval_status = 'approved'
        AND e.event_date < CURDATE()
        ORDER BY e.event_date DESC
    ";
}
else {
    header("Location: dashboard.php");
    exit;
}

$events = mysqli_query($conn, $query);
?>

<div class="container-fluid">
<h2 class="mb-4"><?= $title ?></h2>

<?php if(mysqli_num_rows($events) == 0){ ?>
    <div class="alert alert-info">No events found.</div>
<?php } else { ?>

<table class="table table-bordered table-hover">
<tr>
    <th>Event</th>
    <th>Club</th>
    <th>Leader</th>
    <th>Date</th>
    <th>Status</th>
    <th>View</th>
</tr>

<?php while($e = mysqli_fetch_assoc($events)){ ?>
<tr>
    <td><?= $e['title'] ?></td>
    <td><?= $e['club_name'] ?></td>
    <td><?= $e['leader_name'] ?></td>
    <td><?= $e['event_date'] ?></td>
    <td>
        <?php if($type == 'pending'){ ?>
            <span class="badge bg-warning">Pending</span>
        <?php } elseif($type == 'upcoming'){ ?>
            <span class="badge bg-primary">Upcoming</span>
        <?php } else { ?>
            <span class="badge bg-success">Completed</span>
        <?php } ?>
    </td>
    <td>
        <a href="event_view.php?id=<?= $e['event_id'] ?>" class="btn btn-info btn-sm">
            View
        </a>
    </td>
</tr>
<?php } ?>

</table>
<?php } ?>

<a href="dashboard.php" class="btn btn-secondary mt-3">← Back to Dashboard</a>

</div>

<?php include "../includes/footer.php"; ?>
