<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'student') {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$student_id = $_SESSION['user_id'];

/* PERFORMANCE DATA */
$results = mysqli_query($conn,"
    SELECT 
        e.title AS event_title,
        c.club_name,
        er.position,
        er.score
    FROM event_results er
    JOIN events e ON er.event_id = e.event_id
    JOIN clubs c ON e.club_id = c.club_id
    WHERE er.student_id = '$student_id'
    ORDER BY e.event_date ASC
");

/* STATS */
$stats = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT 
        COUNT(*) AS total_events,
        MAX(score) AS best_score,
        ROUND(AVG(score),2) AS avg_score
    FROM event_results
    WHERE student_id = '$student_id'
"));
?>

<div class="container-fluid">
<h2 class="mb-4">My Performance</h2>

<!-- PERFORMANCE CARDS -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card p-3 text-center">
            <b>Total Events</b><br>
            <?= $stats['total_events'] ?? 0 ?>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 text-center">
            <b>Best Score</b><br>
            <?= $stats['best_score'] ?? '-' ?>
        </div>
    </div>
    <div class="col-md-4">
        <div class="card p-3 text-center">
            <b>Average Score</b><br>
            <?= $stats['avg_score'] ?? '-' ?>
        </div>
    </div>
</div>

<!-- PERFORMANCE TABLE -->
<table class="table table-bordered table-striped">
<thead>
<tr>
    <th>Event</th>
    <th>Club</th>
    <th>Position</th>
    <th>Score</th>
</tr>
</thead>
<tbody>
<?php if(mysqli_num_rows($results) > 0){ ?>
<?php while($r = mysqli_fetch_assoc($results)){ ?>
<tr>
    <td><?= $r['event_title'] ?></td>
    <td><?= $r['club_name'] ?></td>
    <td><?= $r['position'] ?></td>
    <td><?= $r['score'] ?></td>
</tr>
<?php } ?>
<?php } else { ?>
<tr>
    <td colspan="4" class="text-center">No performance data available</td>
</tr>
<?php } ?>
</tbody>
</table>

<!-- SCORE TREND GRAPH -->
<h4 class="mt-5">Score Trend</h4>
<canvas id="scoreChart" height="120"></canvas>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
new Chart(document.getElementById('scoreChart'), {
    type: 'line',
    data: {
        labels: [
            <?php 
            mysqli_data_seek($results,0);
            while($r = mysqli_fetch_assoc($results)){
                echo "'".$r['event_title']."',";
            }
            ?>
        ],
        datasets: [{
            label: 'Score',
            data: [
                <?php 
                mysqli_data_seek($results,0);
                while($r = mysqli_fetch_assoc($results)){
                    echo $r['score'].",";
                }
                ?>
            ],
            borderColor: '#4e73df',
            backgroundColor: 'rgba(78,115,223,0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        scales: {
            y: {
                beginAtZero: true,
                max: 100
            }
        }
    }
});
</script>

<?php include "../includes/footer.php"; ?>
