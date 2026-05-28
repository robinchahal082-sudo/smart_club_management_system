<?php
session_start();
if ($_SESSION['role'] != 'faculty') {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$faculty_id = $_SESSION['user_id'];

$leaves = mysqli_query($conn,"
    SELECT u.name, c.club_name, clr.reason, clr.left_at
    FROM club_leave_requests clr
    JOIN users u ON clr.student_id=u.user_id
    JOIN clubs c ON clr.club_id=c.club_id
    WHERE c.faculty_id='$faculty_id'
    ORDER BY clr.left_at DESC
");
?>

<h2>Students Who Left Clubs</h2>

<table class="table table-bordered">
<tr>
    <th>Student</th>
    <th>Club</th>
    <th>Reason</th>
    <th>Date</th>
</tr>

<?php while($l=mysqli_fetch_assoc($leaves)){ ?>
<tr>
    <td><?= $l['name'] ?></td>
    <td><?= $l['club_name'] ?></td>
    <td><?= $l['reason'] ?></td>
    <td><?= $l['left_at'] ?></td>
</tr>
<?php } ?>
</table>

<?php include "../includes/footer.php"; ?>
