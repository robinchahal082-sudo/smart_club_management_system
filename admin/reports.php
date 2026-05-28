<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

/* =======================
   INSIGHT CARDS
======================= */
$most_active = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT c.club_name, COUNT(ep.student_id) AS enrollments
    FROM clubs c
    LEFT JOIN events e ON c.club_id=e.club_id
    LEFT JOIN event_participants ep ON e.event_id=ep.event_id
    GROUP BY c.club_id
    ORDER BY enrollments DESC LIMIT 1
"));

$highest_participation = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT c.club_name,
    ROUND((COUNT(DISTINCT ep.student_id)/COUNT(DISTINCT cm.student_id))*100) AS pct
    FROM clubs c
    LEFT JOIN club_members cm ON c.club_id=cm.club_id
    LEFT JOIN events e ON c.club_id=e.club_id
    LEFT JOIN event_participants ep ON e.event_id=ep.event_id
    GROUP BY c.club_id
    ORDER BY pct DESC LIMIT 1
"));

$most_events = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT c.club_name, COUNT(e.event_id) AS total
    FROM clubs c
    LEFT JOIN events e ON c.club_id=e.club_id
    GROUP BY c.club_id
    ORDER BY total DESC LIMIT 1
"));

$lowest_participation = mysqli_fetch_assoc(mysqli_query($conn,"
    SELECT c.club_name,
    ROUND((COUNT(DISTINCT ep.student_id)/COUNT(DISTINCT cm.student_id))*100) AS pct
    FROM clubs c
    LEFT JOIN club_members cm ON c.club_id=cm.club_id
    LEFT JOIN events e ON c.club_id=e.club_id
    LEFT JOIN event_participants ep ON e.event_id=ep.event_id
    GROUP BY c.club_id
    ORDER BY pct ASC LIMIT 1
"));

/* =======================
   OVERALL DATA
======================= */
$members_q = mysqli_query($conn,"
    SELECT c.club_name, COUNT(cm.student_id) members
    FROM clubs c LEFT JOIN club_members cm ON c.club_id=cm.club_id
    GROUP BY c.club_id
");

$events_q = mysqli_query($conn,"
    SELECT c.club_name, COUNT(e.event_id) events
    FROM clubs c LEFT JOIN events e ON c.club_id=e.club_id
    GROUP BY c.club_id
");

$enrollments_q = mysqli_query($conn,"
    SELECT c.club_name, COUNT(ep.student_id) enrollments
    FROM clubs c
    LEFT JOIN events e ON c.club_id=e.club_id
    LEFT JOIN event_participants ep ON e.event_id=ep.event_id
    GROUP BY c.club_id
");

$participants_q = mysqli_query($conn,"
    SELECT c.club_name, COUNT(DISTINCT ep.student_id) participants
    FROM clubs c
    LEFT JOIN events e ON c.club_id=e.club_id
    LEFT JOIN event_participants ep ON e.event_id=ep.event_id
    GROUP BY c.club_id
");

/* =======================
   LEADERBOARD
======================= */
$leaderboard = mysqli_query($conn,"
    SELECT c.club_name,
    COUNT(DISTINCT cm.student_id) members,
    COUNT(DISTINCT e.event_id) events,
    COUNT(DISTINCT ep.student_id) participants,
    ROUND((COUNT(DISTINCT ep.student_id)/COUNT(DISTINCT cm.student_id))*100) pct
    FROM clubs c
    LEFT JOIN club_members cm ON c.club_id=cm.club_id
    LEFT JOIN events e ON c.club_id=e.club_id
    LEFT JOIN event_participants ep ON e.event_id=ep.event_id
    GROUP BY c.club_id
    ORDER BY pct DESC
");

/* =======================
   MONTHLY DATA
======================= */
$monthly_events = mysqli_query($conn,"
    SELECT DATE_FORMAT(event_date,'%b') m, COUNT(*) t
    FROM events GROUP BY m
");

$monthly_enrollments = mysqli_query($conn,"
    SELECT DATE_FORMAT(e.event_date,'%b') m, COUNT(ep.student_id) t
    FROM events e
    LEFT JOIN event_participants ep ON e.event_id=ep.event_id
    GROUP BY m
");

/* =======================
   RESULT COVERAGE
======================= */
$published = mysqli_fetch_row(mysqli_query($conn,"
    SELECT COUNT(DISTINCT event_id) FROM event_results
"))[0];

$total_events = mysqli_fetch_row(mysqli_query($conn,"
    SELECT COUNT(*) FROM events
"))[0];

$pending = $total_events - $published;
?>

<div class="container-fluid">
<h2 class="mb-4">Reports & Analytics</h2>

<!-- INSIGHT CARDS -->
<div class="row mb-4">
<div class="col-md-3"><div class="card p-3 text-center"><b>Most Active</b><br><?= $most_active['club_name'] ?></div></div>
<div class="col-md-3"><div class="card p-3 text-center"><b>Highest %</b><br><?= $highest_participation['club_name'] ?> (<?= $highest_participation['pct'] ?>%)</div></div>
<div class="col-md-3"><div class="card p-3 text-center"><b>Most Events</b><br><?= $most_events['club_name'] ?></div></div>
<div class="col-md-3"><div class="card p-3 text-center"><b>Lowest %</b><br><?= $lowest_participation['club_name'] ?> (<?= $lowest_participation['pct'] ?>%)</div></div>
</div>

<!-- OVERALL GRAPHS -->
<div class="row">
<div class="col-md-3"><canvas id="membersChart" height="160"></canvas></div>
<div class="col-md-3"><canvas id="enrollmentsChart" height="160"></canvas></div>
<div class="col-md-3"><canvas id="eventsChart" height="160"></canvas></div>
<div class="col-md-3"><canvas id="participantsChart" height="160"></canvas></div>
</div>

<!-- MONTHLY -->
<div class="row mt-4">
<div class="col-md-6"><canvas id="monthlyEvents"></canvas></div>
<div class="col-md-6"><canvas id="monthlyEnrollments"></canvas></div>
</div>

<!-- RESULT COVERAGE -->
<div class="row mt-4">
<div class="col-md-4 offset-md-4"><canvas id="coverage"></canvas></div>
</div>

<!-- LEADERBOARD -->
<h4 class="mt-5">Top Clubs Leaderboard</h4>
<table class="table table-bordered table-striped">
<tr>
<th>#</th><th>Club</th><th>Members</th><th>Events</th><th>Participants</th><th>% Active</th>
</tr>
<?php $i=1; while($l=mysqli_fetch_assoc($leaderboard)){ ?>
<tr>
<td><?= $i++ ?></td>
<td><?= $l['club_name'] ?></td>
<td><?= $l['members'] ?></td>
<td><?= $l['events'] ?></td>
<td><?= $l['participants'] ?></td>
<td><?= $l['pct'] ?>%</td>
</tr>
<?php } ?>
</table>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
/* MEMBERS */
new Chart(membersChart,{
type:'bar',
data:{
labels:[<?php while($r=mysqli_fetch_assoc($members_q)){echo "'".$r['club_name']."',";}?>],
datasets:[{label:'Members',data:[<?php mysqli_data_seek($members_q,0); while($r=mysqli_fetch_assoc($members_q)){echo $r['members'].",";}?>],backgroundColor:'#4e73df'}]
}
});

/* ENROLLMENTS */
new Chart(enrollmentsChart,{
type:'line',
data:{
labels:[<?php while($r=mysqli_fetch_assoc($enrollments_q)){echo "'".$r['club_name']."',";}?>],
datasets:[{label:'Enrollments',data:[<?php mysqli_data_seek($enrollments_q,0); while($r=mysqli_fetch_assoc($enrollments_q)){echo $r['enrollments'].",";}?>],borderColor:'#1cc88a',tension:0.3}]
}
});

/* EVENTS */
new Chart(eventsChart,{
type:'bar',
data:{
labels:[<?php while($r=mysqli_fetch_assoc($events_q)){echo "'".$r['club_name']."',";}?>],
datasets:[{label:'Events',data:[<?php mysqli_data_seek($events_q,0); while($r=mysqli_fetch_assoc($events_q)){echo $r['events'].",";}?>],backgroundColor:'#f6c23e'}]
},
options:{indexAxis:'y'}
});

/* PARTICIPANTS */
new Chart(participantsChart,{
type:'doughnut',
data:{
labels:[<?php while($r=mysqli_fetch_assoc($participants_q)){echo "'".$r['club_name']."',";}?>],
datasets:[{label:'Participants',data:[<?php mysqli_data_seek($participants_q,0); while($r=mysqli_fetch_assoc($participants_q)){echo $r['participants'].",";}?>],backgroundColor:['#e74a3b','#1cc88a','#36b9cc','#f6c23e']}]
}
});

/* MONTHLY EVENTS */
new Chart(monthlyEvents,{
type:'line',
data:{
labels:[<?php while($m=mysqli_fetch_assoc($monthly_events)){echo "'".$m['m']."',";}?>],
datasets:[{label:'Events / Month',data:[<?php mysqli_data_seek($monthly_events,0); while($m=mysqli_fetch_assoc($monthly_events)){echo $m['t'].",";}?>],borderColor:'#4e73df',tension:0.4,fill:true}]
}
});

/* MONTHLY ENROLLMENTS */
new Chart(monthlyEnrollments,{
type:'line',
data:{
labels:[<?php while($m=mysqli_fetch_assoc($monthly_enrollments)){echo "'".$m['m']."',";}?>],
datasets:[{label:'Enrollments / Month',data:[<?php mysqli_data_seek($monthly_enrollments,0); while($m=mysqli_fetch_assoc($monthly_enrollments)){echo $m['t'].",";}?>],borderColor:'#1cc88a',tension:0.4,fill:true}]
}
});

/* RESULT COVERAGE */
new Chart(coverage,{
type:'pie',
data:{
labels:['Published','Pending'],
datasets:[{label:'Results',data:[<?= $published ?>,<?= $pending ?>],backgroundColor:['#1cc88a','#e74a3b']}]
}
});
</script>

<?php include "../includes/footer.php"; ?>
