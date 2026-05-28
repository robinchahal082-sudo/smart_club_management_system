<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'leader') {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$leader_id = $_SESSION['user_id'];

/* LEADER KA CLUB */
$clubRes = mysqli_query($conn,"
    SELECT club_id FROM clubs WHERE leader_id = '$leader_id'
");
$club = mysqli_fetch_assoc($clubRes);
$club_id = $club['club_id'];

/* EVENTS OF THIS CLUB */
$events = mysqli_query($conn,"
    SELECT event_id, title
    FROM events
    WHERE club_id = '$club_id'
    AND approval_status = 'approved'
    ORDER BY event_date DESC
");
?>

<div class="container-fluid">
<h2 class="mb-4">Event-wise Enrollments</h2>

<?php while($event = mysqli_fetch_assoc($events)) { ?>

<h4 class="mt-4"><?= $event['title'] ?></h4>

<?php
$participants = mysqli_query($conn,"
    SELECT 
        u.user_id,
        u.name AS student_name,
        c.club_name
    FROM event_participants ep
    JOIN users u ON ep.student_id = u.user_id
    LEFT JOIN club_members cm ON cm.student_id = u.user_id
    LEFT JOIN clubs c ON cm.club_id = c.club_id
    WHERE ep.event_id = '{$event['event_id']}'
    GROUP BY u.user_id
");
?>

<?php if(mysqli_num_rows($participants) == 0){ ?>
    <div class="alert alert-info">No students enrolled.</div>
<?php } else { ?>

<table class="table table-bordered table-hover">
    <tr>
        <th>ID</th>
        <th>Student Name</th>
        <th>Club</th>
    </tr>

<?php while($p = mysqli_fetch_assoc($participants)) { ?>
<tr>
    <td><?= $p['user_id'] ?></td>
    <td><?= $p['student_name'] ?></td>
    <td><?= $p['club_name'] ?? '—' ?></td>
</tr>
<?php } ?>

</table>
<?php } ?>

<?php } ?>

</div>

<?php include "../includes/footer.php"; ?>
