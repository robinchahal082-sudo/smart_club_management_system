<?php
session_start();
include "../config/db.php";

if ($_SESSION['role'] != 'student') {
    header("Location: ../auth/login.php");
    exit;
}

$student_id = $_SESSION['user_id'];

$events = mysqli_query($conn,"
    SELECT e.event_id, e.title, e.event_date, c.club_name
    FROM event_participants ep
    JOIN events e ON ep.event_id = e.event_id
    JOIN clubs c ON e.club_id = c.club_id
    WHERE ep.student_id='$student_id'
");

include "../includes/header.php";
include "../includes/sidebar.php";
?>

<h3>My Enrolled Events</h3>

<table class="table table-bordered">
<tr>
    <th>Event</th>
    <th>Club</th>
    <th>Date</th>
    <th>Action</th>
</tr>

<?php while($e = mysqli_fetch_assoc($events)){ ?>
<tr>
    <td><?= $e['title'] ?></td>
    <td><?= $e['club_name'] ?></td>
    <td><?= $e['event_date'] ?></td>
    <td>
        <a href="unenroll.php?event_id=<?= $e['event_id'] ?>"
           class="btn btn-danger btn-sm"
           onclick="return confirm('Unenroll from event?')">
           Unenroll
        </a>
    </td>
</tr>
<?php } ?>
</table>

<?php include "../includes/footer.php"; ?>
