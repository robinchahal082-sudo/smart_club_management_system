<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role']!='faculty'){
    header("Location: ../auth/login.php"); exit;
}

include "../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$faculty_id = $_SESSION['user_id'];

$results = mysqli_query($conn,"
    SELECT u.name AS student,e.title,er.position,er.score,c.club_name
    FROM event_results er
    JOIN users u ON er.student_id=u.user_id
    JOIN events e ON er.event_id=e.event_id
    JOIN clubs c ON e.club_id=c.club_id
    WHERE c.faculty_id='$faculty_id'
");
?>

<h2>Student Performance</h2>

<table class="table table-bordered">
<tr><th>Student</th><th>Club</th><th>Event</th><th>Position</th><th>Score</th></tr>
<?php while($r=mysqli_fetch_assoc($results)){ ?>
<tr>
<td><?= $r['student'] ?></td>
<td><?= $r['club_name'] ?></td>
<td><?= $r['title'] ?></td>
<td><?= $r['position'] ?></td>
<td><?= $r['score'] ?></td>
</tr>
<?php } ?>
</table>

<?php include "../includes/footer.php"; ?>
