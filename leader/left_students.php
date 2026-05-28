<?php
session_start();
if ($_SESSION['role'] != 'leader') {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$leader_id = $_SESSION['user_id'];

/* Leader ka club */
$club = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT club_id FROM clubs WHERE leader_id='$leader_id'
"));
$club_id = $club['club_id'];

$leaves = mysqli_query($conn,"
    SELECT u.name, clr.reason, clr.left_at
    FROM club_leave_requests clr
    JOIN users u ON clr.student_id=u.user_id
    WHERE clr.club_id='$club_id'
    ORDER BY clr.left_at DESC
");
?>

<h2>Students Who Left Club</h2>

<table class="table table-bordered">
<tr>
    <th>Student</th>
    <th>Reason</th>
    <th>Date</th>
</tr>

<?php while($l=mysqli_fetch_assoc($leaves)){ ?>
<tr>
    <td><?= $l['name'] ?></td>
    <td><?= $l['reason'] ?></td>
    <td><?= $l['left_at'] ?></td>
</tr>
<?php } ?>
</table>

<?php include "../includes/footer.php"; ?>
