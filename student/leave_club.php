<?php
session_start();
if ($_SESSION['role'] != 'student') {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$student_id = $_SESSION['user_id'];

/* Student ka current club */
$club = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT c.club_id, c.club_name
    FROM club_members cm
    JOIN clubs c ON cm.club_id=c.club_id
    WHERE cm.student_id='$student_id'
"));
?>

<h2>Leave Club</h2>

<?php if(!$club){ ?>
    <div class="alert alert-warning">You are not part of any club.</div>
<?php } else { ?>

<div class="card p-3">
    <p><b>Club:</b> <?= $club['club_name'] ?></p>

    <form method="POST">
        <label>Reason for leaving</label>
        <textarea name="reason" class="form-control mb-2" required></textarea>

        <button name="leave" class="btn btn-danger">
            Leave Club
        </button>
    </form>
</div>

<?php } ?>

<?php
if(isset($_POST['leave'])){
    $reason = $_POST['reason'];
    $club_id = $club['club_id'];

    /* Log leave */
    mysqli_query($conn,"
        INSERT INTO club_leave_requests (student_id, club_id, reason)
        VALUES ('$student_id','$club_id','$reason')
    ");

    /* Remove student from club */
    mysqli_query($conn,"
        DELETE FROM club_members 
        WHERE student_id='$student_id' AND club_id='$club_id'
    ");

    echo "<script>alert('You have left the club');window.location='dashboard.php';</script>";
}
?>

<?php include "../includes/footer.php"; ?>
