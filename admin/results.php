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

$results = mysqli_query($conn, "
    SELECT r.*, 
           u.name AS student_name,
           e.title AS event_title,
           c.club_name
    FROM event_results r
    JOIN users u ON r.student_id = u.user_id
    JOIN events e ON r.event_id = e.event_id
    JOIN clubs c ON e.club_id = c.club_id
    ORDER BY c.club_name, e.title
");
?>

<h2>All Event Results</h2>

<div class="card p-3">
<table class="table table-bordered">
    <tr>
        <th>Club</th>
        <th>Event</th>
        <th>Student</th>
        <th>Position</th>
        <th>Score</th>
    </tr>
    <?php while($r = mysqli_fetch_assoc($results)){ ?>
    <tr>
        <td><?= $r['club_name'] ?></td>
        <td><?= $r['event_title'] ?></td>
        <td><?= $r['student_name'] ?></td>
        <td><?= $r['position'] ?></td>
        <td><?= $r['score'] ?></td>
    </tr>
    <?php } ?>
</table>
</div>

<?php include "../includes/footer.php"; ?>
