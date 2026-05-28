<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";

/* ==============================
   🔴 ADDED LOGIC ONLY
   CREATE CLUB (THIS WAS MISSING)
============================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['club_name'])) {

    $club_name  = $_POST['club_name'];
    $desc       = $_POST['description'] ?? '';
    $leader_id  = $_POST['leader_id'];
    $faculty_id = $_POST['faculty_id'];

    // create club
    mysqli_query($conn,"
        INSERT INTO clubs (club_name, description, leader_id, faculty_id)
        VALUES ('$club_name','$desc','$leader_id','$faculty_id')
    ");

    // promote leader
    mysqli_query($conn,"
        UPDATE users SET role='leader'
        WHERE user_id='$leader_id'
    ");

    header("Location: dashboard.php");
    exit;
}

include "../includes/header.php";
include "../includes/sidebar.php";

/* COUNTS */
$total_clubs = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM clubs"))[0];
$total_students = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM users WHERE role='student'"))[0];
$total_events = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM events WHERE approval_status='approved'"))[0];
$pending_events = mysqli_fetch_row(mysqli_query($conn, "SELECT COUNT(*) FROM events WHERE approval_status='pending'"))[0];
$upcoming_events = mysqli_fetch_row(mysqli_query($conn,"
    SELECT COUNT(*) FROM events 
    WHERE approval_status='approved' AND event_date >= CURDATE()
"))[0];
$completed_events = mysqli_fetch_row(mysqli_query($conn,"
    SELECT COUNT(*) FROM events 
    WHERE approval_status='approved' AND event_date < CURDATE()
"))[0];

/* ALL CLUBS */
$clubs = mysqli_query($conn,"
    SELECT c.club_id, c.club_name, 
           u1.name AS leader_name, 
           u2.name AS faculty_name
    FROM clubs c
    LEFT JOIN users u1 ON c.leader_id = u1.user_id
    LEFT JOIN users u2 ON c.faculty_id = u2.user_id
");
?>

<div class="container-fluid">
<h2 class="mb-4">Admin Dashboard</h2>

<!-- DASHBOARD CARDS -->
<div class="row mb-4">
    <div class="col-md-4">
        <div class="card p-3 shadow-sm dashboard-card">
            <h6>Total Clubs</h6>
            <h3><?= $total_clubs ?></h3>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-3 shadow-sm dashboard-card">
            <h6>Total Students</h6>
            <h3><?= $total_students ?></h3>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card p-3 shadow-sm dashboard-card">
            <h6>Total Events</h6>
            <h3><?= $total_events ?></h3>
        </div>
    </div>
</div>

<div class="row mb-4">
    <div class="col-md-4">
        <a href="events_list.php?type=pending" class="text-decoration-none">
            <div class="card p-3 shadow-sm dashboard-card border-warning">
                <h6>Pending Events</h6>
                <h3 class="text-warning"><?= $pending_events ?></h3>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="events_list.php?type=upcoming" class="text-decoration-none">
            <div class="card p-3 shadow-sm dashboard-card border-primary">
                <h6>Upcoming Events</h6>
                <h3 class="text-primary"><?= $upcoming_events ?></h3>
            </div>
        </a>
    </div>

    <div class="col-md-4">
        <a href="events_list.php?type=completed" class="text-decoration-none">
            <div class="card p-3 shadow-sm dashboard-card border-success">
                <h6>Completed Events</h6>
                <h3 class="text-success"><?= $completed_events ?></h3>
            </div>
        </a>
    </div>
</div>

<!-- CREATE CLUB + CLUB LIST -->
<div class="row">
    <div class="col-md-5">
        <div class="card p-4 shadow-sm">
            <h5>Create Club</h5>

            <!-- 🔴 SAME FORM, NO CHANGE -->
            <form method="post">
                <input type="text" name="club_name" class="form-control mb-2" placeholder="Club Name" required>
                <textarea name="description" class="form-control mb-2" placeholder="Description"></textarea>

                <select name="leader_id" class="form-control mb-2" required>
                    <option value="">Select Leader (Student)</option>
                    <?php
                    $leaders = mysqli_query($conn,"SELECT user_id,name FROM users WHERE role='student'");
                    while($l=mysqli_fetch_assoc($leaders)){
                        echo "<option value='{$l['user_id']}'>{$l['name']}</option>";
                    }
                    ?>
                </select>

                <select name="faculty_id" class="form-control mb-3" required>
                    <option value="">Select Faculty</option>
                    <?php
                    $faculty = mysqli_query($conn,"SELECT user_id,name FROM users WHERE role='faculty'");
                    while($f=mysqli_fetch_assoc($faculty)){
                        echo "<option value='{$f['user_id']}'>{$f['name']}</option>";
                    }
                    ?>
                </select>

                <button class="btn btn-primary w-100">Create Club</button>
            </form>
        </div>
    </div>

    <div class="col-md-7">
        <div class="card p-4 shadow-sm">
            <h5>All Clubs</h5>
            <table class="table table-bordered mt-3">
                <tr>
                    <th>Club</th>
                    <th>Leader</th>
                    <th>Faculty</th>
                    <th>Action</th>
                </tr>
                <?php while($c=mysqli_fetch_assoc($clubs)){ ?>
                <tr>
                    <td><?= $c['club_name'] ?></td>
                    <td><?= $c['leader_name'] ?></td>
                    <td><?= $c['faculty_name'] ?></td>
                    <td>
                        <a href="club_edit.php?id=<?= $c['club_id'] ?>" class="btn btn-warning btn-sm">
                            Edit
                        </a>
                    </td>
                </tr>
                <?php } ?>
            </table>
        </div>
    </div>
</div>
</div>

<?php include "../includes/footer.php"; ?>
