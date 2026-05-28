<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'leader') {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$leader_id = $_SESSION['user_id'];

/* Leader ka club */
$clubQ = mysqli_query($conn, "SELECT club_id, club_name FROM clubs WHERE leader_id='$leader_id'");
$club  = mysqli_fetch_assoc($clubQ);
$club_id   = $club['club_id'] ?? 0;
$club_name = $club['club_name'] ?? '';
?>

<h2>Event Enrollments</h2>

<?php if(!$club_id){ ?>
    <div class="alert alert-warning">No club assigned.</div>
<?php } else { ?>

<h5>Club: <?= $club_name ?></h5>

<?php
$events = mysqli_query($conn, "
    SELECT event_id, title 
    FROM events 
    WHERE club_id='$club_id' AND approval_status='approved'
");

if(mysqli_num_rows($events)==0){
    echo "<p class='text-muted'>No approved events.</p>";
}

while($e = mysqli_fetch_assoc($events)){
    $event_id = $e['event_id'];
    ?>
    <div class="card p-3 mb-4">
        <h6>Event: <?= $e['title'] ?></h6>

        <?php
        $students = mysqli_query($conn, "
            SELECT u.user_id, u.name
            FROM event_participants ep
            JOIN users u ON ep.student_id = u.user_id
            WHERE ep.event_id='$event_id'
        ");

        if(mysqli_num_rows($students)==0){
            echo "<p class='text-muted'>No students enrolled yet.</p>";
        } else {
            echo "<table class='table table-bordered'>
                    <tr>
                        <th>Student ID</th>
                        <th>Name</th>
                    </tr>";
            while($s = mysqli_fetch_assoc($students)){
                echo "<tr>
                        <td>{$s['user_id']}</td>
                        <td>{$s['name']}</td>
                      </tr>";
            }
            echo "</table>";
        }
        ?>
    </div>
<?php } } ?>

<?php include "../includes/footer.php"; ?>
