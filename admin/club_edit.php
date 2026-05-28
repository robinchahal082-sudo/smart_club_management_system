<?php
session_start();
if($_SESSION['role']!='admin'){ header("Location: ../auth/login.php"); exit; }

include "../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$club_id = $_GET['id'];

$club = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT * FROM clubs WHERE club_id='$club_id'
"));

$students = mysqli_query($conn,"SELECT user_id,name FROM users WHERE role IN ('student','leader')");
$faculty  = mysqli_query($conn,"SELECT user_id,name FROM users WHERE role='faculty'");
?>

<h3>Change Leader / Faculty</h3>

<form method="POST">
    <label>Leader (Student)</label>
    <select name="leader_id" class="form-control mb-2">
        <?php while($s=mysqli_fetch_assoc($students)){ ?>
            <option value="<?= $s['user_id'] ?>"
                <?= $s['user_id']==$club['leader_id']?'selected':'' ?>>
                <?= $s['name'] ?>
            </option>
        <?php } ?>
    </select>

    <label>Faculty</label>
    <select name="faculty_id" class="form-control mb-2">
        <?php while($f=mysqli_fetch_assoc($faculty)){ ?>
            <option value="<?= $f['user_id'] ?>"
                <?= $f['user_id']==$club['faculty_id']?'selected':'' ?>>
                <?= $f['name'] ?>
            </option>
        <?php } ?>
    </select>

    <button name="update" class="btn btn-success">Update</button>
</form>

<?php
if(isset($_POST['update'])){

    $new_leader  = $_POST['leader_id'];
    $new_faculty = $_POST['faculty_id'];

    /* 🔴 GET OLD LEADER (THIS WAS MISSING) */
    $old = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT leader_id FROM clubs WHERE club_id='$club_id'
    "));
    $old_leader = $old['leader_id'];

    /* Update club */
    mysqli_query($conn,"
        UPDATE clubs
        SET leader_id='$new_leader', faculty_id='$new_faculty'
        WHERE club_id='$club_id'
    ");

    /* Promote new leader */
    mysqli_query($conn,"
        UPDATE users 
        SET role='leader'
        WHERE user_id='$new_leader'
    ");

    /* 🔥 DEMOTE OLD LEADER → STUDENT (DIRECT, NO CONDITION) */
    if($old_leader){
        mysqli_query($conn,"
            UPDATE users 
            SET role='student'
            WHERE user_id='$old_leader'
        ");
    }

    header("Location: clubs_manage.php");
    exit;
}
?>

<?php include "../includes/footer.php"; ?>
