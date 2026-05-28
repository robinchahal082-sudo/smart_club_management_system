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

/* GET LEADER CLUB */
$club_q = mysqli_query($conn, "SELECT club_id FROM clubs WHERE leader_id='$leader_id'");
$club = mysqli_fetch_assoc($club_q);
$club_id = $club['club_id'];

/* =========================
   AUTO RANK CALCULATION
========================= */
$event_q = mysqli_query($conn, "
    SELECT DISTINCT er.event_id 
    FROM event_results er
    JOIN events e ON er.event_id = e.event_id
    WHERE e.club_id = '$club_id'
");

while ($ev = mysqli_fetch_assoc($event_q)) {
    $event_id = $ev['event_id'];
    $rank = 1;

    $score_q = mysqli_query($conn, "
        SELECT result_id 
        FROM event_results 
        WHERE event_id = '$event_id'
        ORDER BY score DESC
    ");

    while ($row = mysqli_fetch_assoc($score_q)) {
        mysqli_query($conn, "
            UPDATE event_results 
            SET position = '$rank'
            WHERE result_id = '{$row['result_id']}'
        ");
        $rank++;
    }
}

/* =========================
   FETCH RESULTS
========================= */
$results = mysqli_query($conn, "
    SELECT 
        e.title AS event_title,
        u.name AS student_name,
        er.position,
        er.score,
        er.result_id
    FROM event_results er
    JOIN events e ON er.event_id = e.event_id
    JOIN users u ON er.student_id = u.user_id
    WHERE e.club_id = '$club_id'
    ORDER BY e.title, er.position
");
?>

<div class="container-fluid">

<h2 class="mb-3">Manage Results</h2>

<a href="upload_result.php" class="btn btn-success mb-3">
    + Upload Result
</a>

<table class="table table-bordered table-striped">
    <thead>
        <tr>
            <th>Event</th>
            <th>Student</th>
            <th>Position</th>
            <th>Score</th>
            <th>Action</th>
        </tr>
    </thead>
    <tbody>
    <?php if(mysqli_num_rows($results) > 0){ ?>
        <?php while($r = mysqli_fetch_assoc($results)){ ?>
        <tr>
            <td><?= $r['event_title'] ?></td>
            <td><?= $r['student_name'] ?></td>
            <td><?= $r['position'] ?></td>
            <td><?= $r['score'] ?></td>
            <td>
                <a href="result_delete.php?id=<?= $r['result_id'] ?>"
                   class="btn btn-danger btn-sm"
                   onclick="return confirm('Delete result?')">
                   Delete
                </a>
            </td>
        </tr>
        <?php } ?>
    <?php } else { ?>
        <tr>
            <td colspan="5" class="text-center">No results available</td>
        </tr>
    <?php } ?>
    </tbody>
</table>

</div>

<?php include "../includes/footer.php"; ?>
