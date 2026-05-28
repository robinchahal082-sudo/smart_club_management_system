<?php
session_start();
include "../config/db.php";

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: ../auth/login.php");
    exit;
}

$student_id = $_SESSION['user_id'];

/* ALL APPROVED EVENTS */
$events = mysqli_query($conn, "
    SELECT e.event_id, e.title, e.event_date, c.club_name
    FROM events e
    JOIN clubs c ON e.club_id = c.club_id
    WHERE e.approval_status = 'approved'
    ORDER BY e.event_date DESC
");

include "../includes/header.php";
include "../includes/sidebar.php";
?>

<h3>Approved Events</h3>

<?php if (mysqli_num_rows($events) == 0) { ?>
    <div class="alert alert-info">No approved events available.</div>
<?php } else { ?>

<table class="table table-bordered table-hover">
    <tr>
        <th>Event</th>
        <th>Club</th>
        <th>Date</th>
        <th>Action</th>
    </tr>

<?php while ($e = mysqli_fetch_assoc($events)) { ?>

<?php
/* CHECK ENROLL STATUS */
$check = mysqli_query($conn, "
    SELECT 1 FROM event_participants
    WHERE event_id = '{$e['event_id']}'
    AND student_id = '$student_id'
");

/* DATE CHECK */
$event_date = strtotime($e['event_date']);
$today = strtotime(date('Y-m-d'));
?>

<tr>
    <td><?= $e['title'] ?></td>
    <td><?= $e['club_name'] ?></td>
    <td><?= date("d M Y", strtotime($e['event_date'])) ?></td>
    <td>
        <a href="event_details.php?id=<?= $e['event_id'] ?>" 
           class="btn btn-info btn-sm">
           View
        </a>

        <?php if (mysqli_num_rows($check) > 0) { ?>
            <span class="badge bg-secondary">Enrolled</span>

        <?php } elseif ($event_date >= $today) { ?>
            <a href="enroll.php?event_id=<?= $e['event_id'] ?>" 
               class="btn btn-success btn-sm">
               Enroll
            </a>

        <?php } else { ?>
            <span class="badge bg-danger">Event Closed</span>
        <?php } ?>
    </td>
</tr>

<?php } ?>
</table>

<?php } ?>

<?php include "../includes/footer.php"; ?>
