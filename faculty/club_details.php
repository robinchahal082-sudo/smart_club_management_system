<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role']!='faculty'){
    header("Location: ../auth/login.php"); exit;
}

include "../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$faculty_id = $_SESSION['user_id'];

$clubs = mysqli_query($conn,"
    SELECT c.club_id,c.club_name,u.name AS leader
    FROM clubs c 
    JOIN users u ON c.leader_id=u.user_id
    WHERE c.faculty_id='$faculty_id'
");
?>

<h2>Assigned Club Details</h2>

<?php while($c=mysqli_fetch_assoc($clubs)){ ?>
<div class="card p-3 mb-4">
    <h5><?= $c['club_name'] ?></h5>
    <p><b>Leader:</b> <?= $c['leader'] ?></p>

    <p><b>Total Members:</b>
    <?= mysqli_fetch_row(mysqli_query($conn,"
        SELECT COUNT(*) FROM club_members WHERE club_id='{$c['club_id']}'
    "))[0]; ?>
    </p>
</div>
<?php } ?>

<?php include "../includes/footer.php"; ?>
