<?php
session_start();
if ($_SESSION['role']!='student'){ header("Location: ../auth/login.php"); exit; }

include "../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$student_id = $_SESSION['user_id'];

$clubs = mysqli_query($conn,"
    SELECT c.club_name,u.name AS leader,f.name AS faculty
    FROM club_members cm
    JOIN clubs c ON cm.club_id=c.club_id
    JOIN users u ON c.leader_id=u.user_id
    JOIN users f ON c.faculty_id=f.user_id
    WHERE cm.student_id='$student_id'
");
?>

<h2>My Clubs</h2>

<table class="table table-bordered">
<tr><th>Club</th><th>Leader</th><th>Faculty</th></tr>
<?php while($c=mysqli_fetch_assoc($clubs)){ ?>
<tr>
<td><?= $c['club_name'] ?></td>
<td><?= $c['leader'] ?></td>
<td><?= $c['faculty'] ?></td>
</tr>
<?php } ?>
</table>

<?php include "../includes/footer.php"; ?>
