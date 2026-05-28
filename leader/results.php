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

/* Approved events of this club */
$events = mysqli_query($conn, "
    SELECT event_id, title 
    FROM events 
    WHERE club_id='$club_id' AND approval_status='approved'
");

/* Already uploaded results */
$results = mysqli_query($conn, "
    SELECT r.*, u.name AS student_name, e.title AS event_title
    FROM event_results r
    JOIN users u ON r.student_id = u.user_id
    JOIN events e ON r.event_id = e.event_id
    WHERE e.club_id='$club_id'
    ORDER BY e.event_date DESC
");
?>

<h2>Leader – Event Results</h2>

<?php if(!$club_id){ ?>
    <div class="alert alert-warning">No club assigned.</div>
<?php } else { ?>

<h5 class="mb-3">Club: <?= $club_name ?></h5>

<!-- UPLOAD RESULT -->
<div class="card p-3 mb-4">
    <h5>Upload Result</h5>
    <form method="POST">
        <select name="event_id" class="form-control mb-2" required>
            <option value="">Select Event</option>
            <?php while($e = mysqli_fetch_assoc($events)){ ?>
                <option value="<?= $e['event_id'] ?>"><?= $e['title'] ?></option>
            <?php } ?>
        </select>

        <input type="number" name="student_id" class="form-control mb-2" placeholder="Student ID" required>
        <input type="text" name="position" class="form-control mb-2" placeholder="Position (1st / 2nd)">
        <input type="text" name="score" class="form-control mb-2" placeholder="Score">

        <button name="upload" class="btn btn-success">Upload Result</button>
    </form>
</div>

<!-- VIEW UPLOADED RESULTS -->
<div class="card p-3">
    <h5>Uploaded Results</h5>

    <?php if(mysqli_num_rows($results)==0){ ?>
        <p class="text-muted">No results uploaded yet.</p>
    <?php } else { ?>
    <table class="table table-bordered">
        <tr>
            <th>Event</th>
            <th>Student</th>
            <th>Position</th>
            <th>Score</th>
        </tr>
        <?php while($r = mysqli_fetch_assoc($results)){ ?>
        <tr>
            <td><?= $r['event_title'] ?></td>
            <td><?= $r['student_name'] ?></td>
            <td><?= $r['position'] ?></td>
            <td><?= $r['score'] ?></td>
        </tr>
        <?php } ?>
    </table>
    <?php } ?>
</div>

<?php } ?>

<?php
/* UPLOAD LOGIC */
if (isset($_POST['upload'])) {
    $event_id   = $_POST['event_id'];
    $student_id = $_POST['student_id'];
    $position   = $_POST['position'];
    $score      = $_POST['score'];

    mysqli_query($conn,
        "INSERT INTO event_results (event_id, student_id, position, score)
         VALUES ('$event_id','$student_id','$position','$score')"
    );

    echo "<script>alert('Result uploaded successfully');window.location='results.php';</script>";
}
?>

<?php include "../includes/footer.php"; ?>
