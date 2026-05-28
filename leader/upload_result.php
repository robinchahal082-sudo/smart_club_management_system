<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'leader') {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$leader_id = $_SESSION['user_id'];

/* LEADER CLUB */
$club = mysqli_fetch_assoc(mysqli_query(
    $conn, "SELECT club_id FROM clubs WHERE leader_id='$leader_id'"
));
$club_id = $club['club_id'];

/* EVENTS */
$events = mysqli_query($conn, "
    SELECT event_id, title 
    FROM events 
    WHERE club_id='$club_id' AND approval_status='approved'
");

/* SELECTED EVENT */
$selected_event = isset($_GET['event_id']) ? $_GET['event_id'] : '';

/* STUDENTS ENROLLED IN EVENT */
$students = [];
if($selected_event){
    $students = mysqli_query($conn, "
        SELECT u.user_id, u.name
        FROM event_participants ep
        JOIN users u ON ep.student_id = u.user_id
        WHERE ep.event_id = '$selected_event'
    ");
}

/* SUBMIT RESULT */
if(isset($_POST['submit'])){
    $event_id   = $_POST['event_id'];
    $student_id = $_POST['student_id'];
    $score      = $_POST['score'];

    mysqli_query($conn, "
        INSERT INTO event_results (event_id, student_id, score)
        VALUES ('$event_id', '$student_id', '$score')
    ");

    header("Location: results_manage.php");
    exit;
}
?>

<div class="container-fluid">
<h2>Upload Result</h2>

<form method="get">
    <div class="mb-3">
        <label>Event</label>
        <select name="event_id" class="form-control" onchange="this.form.submit()" required>
            <option value="">Select Event</option>
            <?php while($e=mysqli_fetch_assoc($events)){ ?>
                <option value="<?= $e['event_id'] ?>" 
                    <?= ($selected_event == $e['event_id']) ? 'selected' : '' ?>>
                    <?= $e['title'] ?>
                </option>
            <?php } ?>
        </select>
    </div>
</form>

<form method="post">
    <input type="hidden" name="event_id" value="<?= $selected_event ?>">

    <div class="mb-3">
        <label>Student</label>
        <select name="student_id" class="form-control" required>
            <option value="">Select Student</option>
            <?php if($students && mysqli_num_rows($students) > 0){ ?>
                <?php while($s=mysqli_fetch_assoc($students)){ ?>
                    <option value="<?= $s['user_id'] ?>">
                        <?= $s['name'] ?>
                    </option>
                <?php } ?>
            <?php } ?>
        </select>
    </div>

    <div class="mb-3">
        <label>Score</label>
        <input type="number" name="score" class="form-control" required>
    </div>

    <button type="submit" name="submit" class="btn btn-primary">
        Upload Result
    </button>
</form>
</div>

<?php include "../includes/footer.php"; ?>
