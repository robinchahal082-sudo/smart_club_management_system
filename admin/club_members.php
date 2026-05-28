<?php
session_start();
if ($_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$club_id = $_GET['club_id'];

$club = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT club_name FROM clubs WHERE club_id='$club_id'
"));

$members = mysqli_query($conn,"
    SELECT u.user_id, u.name, u.role
    FROM club_members cm
    JOIN users u ON cm.student_id = u.user_id
    WHERE cm.club_id='$club_id'
");
?>

<h2>Club Members — <?= $club['club_name'] ?></h2>

<table class="table table-bordered">
<tr>
    <th>Name</th>
    <th>Current Role</th>
    <th>Action</th>
</tr>

<?php while($m = mysqli_fetch_assoc($members)) { ?>
<tr>
    <td><?= $m['name'] ?></td>
    <td><?= ucfirst($m['role']) ?></td>
    <td>
        <?php if($m['role'] == 'student'){ ?>
            <a href="assign_leader.php?uid=<?= $m['user_id'] ?>&club_id=<?= $club_id ?>"
               class="btn btn-success btn-sm"
               onclick="return confirm('Assign as club leader?')">
               Assign Leader
            </a>
        <?php } else { ?>
            <span class="badge bg-info">Already Leader</span>
        <?php } ?>
    </td>
</tr>
<?php } ?>
</table>

<?php include "../includes/footer.php"; ?>
