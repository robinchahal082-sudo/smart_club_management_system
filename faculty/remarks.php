<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role']!='faculty'){
    header("Location: ../auth/login.php"); exit;
}

include "../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$faculty_id = $_SESSION['user_id'];

$events = mysqli_query($conn,"
    SELECT e.event_id,e.title
    FROM events e
    JOIN clubs c ON e.club_id=c.club_id
    WHERE c.faculty_id='$faculty_id'
");
?>

<h2>Faculty Remarks</h2>

<form method="POST" class="card p-3 mb-4">
<select name="event_id" class="form-control mb-2" required>
    <option value="">Select Event</option>
    <?php while($e=mysqli_fetch_assoc($events)){ ?>
        <option value="<?= $e['event_id'] ?>"><?= $e['title'] ?></option>
    <?php } ?>
</select>

<textarea name="remark" class="form-control mb-2" placeholder="Write remark..." required></textarea>
<button name="save" class="btn btn-primary">Save Remark</button>
</form>

<?php
if(isset($_POST['save'])){
    mysqli_query($conn,"
        INSERT INTO faculty_remarks (faculty_id,event_id,remark)
        VALUES ('$faculty_id','{$_POST['event_id']}','{$_POST['remark']}')
    ");
    echo "<script>alert('Remark saved');</script>";
}
?>

<?php include "../includes/footer.php"; ?>
