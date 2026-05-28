<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'faculty') {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$faculty_id = $_SESSION['user_id'];

/* Faculty ke clubs */
$clubs = mysqli_query($conn, "
    SELECT club_id, club_name 
    FROM clubs 
    WHERE faculty_id='$faculty_id'
");
?>

<h2>Club Members</h2>

<?php if (mysqli_num_rows($clubs) == 0) { ?>
    <div class="alert alert-warning">No club assigned.</div>
<?php } ?>

<?php while ($c = mysqli_fetch_assoc($clubs)) { ?>
    <div class="card p-3 mb-4">
        <h5>Club: <?= $c['club_name'] ?></h5>

        <?php
        $members = mysqli_query($conn, "
            SELECT u.user_id, u.name 
            FROM club_members cm
            JOIN users u ON cm.student_id = u.user_id
            WHERE cm.club_id='{$c['club_id']}'
        ");

        if (mysqli_num_rows($members) == 0) {
            echo "<p class='text-muted'>No members in this club.</p>";
        } else {
        ?>
            <table class="table table-bordered">
                <tr>
                    <th>Student ID</th>
                    <th>Name</th>
                </tr>
                <?php while ($m = mysqli_fetch_assoc($members)) { ?>
                    <tr>
                        <td><?= $m['user_id'] ?></td>
                        <td><?= $m['name'] ?></td>
                    </tr>
                <?php } ?>
            </table>
        <?php } ?>
    </div>
<?php } ?>

<?php include "../includes/footer.php"; ?>
