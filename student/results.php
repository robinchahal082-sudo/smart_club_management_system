<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$student_id = $_SESSION['user_id'];

$results = mysqli_query($conn, "
    SELECT r.*, 
           e.title AS event_title,
           c.club_name
    FROM event_results r
    JOIN events e ON r.event_id = e.event_id
    JOIN clubs c ON e.club_id = c.club_id
    WHERE r.student_id='$student_id'
    ORDER BY e.event_date DESC
");
?>

<h2>My Results</h2>

<div class="card p-3">
<?php if(mysqli_num_rows($results)==0){ ?>
    <p class="text-muted">No results published yet.</p>
<?php } else { ?>
<table class="table table-bordered">
    <tr>
        <th>Club</th>
        <th>Event</th>
        <th>Position</th>
        <th>Score</th>
    </tr>
    <?php while($r = mysqli_fetch_assoc($results)){ ?>
    <tr>
        <td><?= $r['club_name'] ?></td>
        <td><?= $r['event_title'] ?></td>
        <td><?= $r['position'] ?></td>
        <td><?= $r['score'] ?></td>
    </tr>
    <?php } ?>
</table>
<?php } ?>
</div>

<?php include "../includes/footer.php"; ?>
