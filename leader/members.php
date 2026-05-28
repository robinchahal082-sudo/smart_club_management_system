<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role']!='leader'){
    header("Location: ../auth/login.php"); exit;
}

include "../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$leader_id = $_SESSION['user_id'];
$club = mysqli_fetch_assoc(mysqli_query($conn,"SELECT club_id FROM clubs WHERE leader_id='$leader_id'"));
$club_id = $club['club_id'];
$members = mysqli_query($conn,"
    SELECT u.user_id, u.name 
    FROM club_members cm 
    JOIN users u ON cm.student_id=u.user_id 
    WHERE cm.club_id='$club_id'
");
?>

<h2>Club Members</h2>

<table class="table table-bordered">
<tr><th>ID</th><th>Name</th><th>Action</th></tr>
<?php while($m=mysqli_fetch_assoc($members)){ ?>
<tr>
    <td><?= $m['user_id'] ?></td>
    <td><?= $m['name'] ?></td>
    <td>
        <a href="member_remove.php?id=<?= $m['user_id'] ?>" 
           onclick="return confirm('Remove this member?')" 
           class="btn btn-danger btn-sm">Remove</a>
    </td>
</tr>
<?php } ?>
</table>

<?php include "../includes/footer.php"; ?>
