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

/* ================= GET LEADER CLUB ================= */
$club_q = mysqli_query($conn, "
    SELECT club_id, club_name 
    FROM clubs 
    WHERE leader_id='$leader_id'
");

$club = mysqli_fetch_assoc($club_q);

if (!$club) {
    echo "
        <div class='alert alert-warning'>
            <strong>No club assigned!</strong><br>
            Please ask admin to assign you as club leader.
        </div>
    ";
    include "../includes/footer.php";
    exit;
}

$club_id   = $club['club_id'];
$club_name = $club['club_name'];

/* ================= FETCH JOIN REQUESTS ================= */
$requests = mysqli_query($conn, "
    SELECT 
        jr.request_id,
        jr.student_id,
        u.name,
        u.email
    FROM join_requests jr
    JOIN users u ON jr.student_id = u.user_id
    WHERE jr.club_id='$club_id'
      AND jr.status='pending'
");
?>

<h2 class="mb-3">Join Requests — <?= htmlspecialchars($club_name) ?></h2>

<?php if (mysqli_num_rows($requests) == 0) { ?>

    <div class="alert alert-info">
        No pending join requests.
    </div>

<?php } else { ?>

<table class="table table-bordered">
    <thead>
        <tr>
            <th>Student Name</th>
            <th>Email</th>
            <th style="width:180px;">Action</th>
        </tr>
    </thead>
    <tbody>
        <?php while ($r = mysqli_fetch_assoc($requests)) { ?>
        <tr>
            <td><?= htmlspecialchars($r['name']) ?></td>
            <td><?= htmlspecialchars($r['email']) ?></td>
            <td>
                <a href="approve_join.php?request_id=<?= $r['request_id'] ?>"
                   class="btn btn-success btn-sm">
                   Approve
                </a>

                <a href="reject_join.php?request_id=<?= $r['request_id'] ?>"
                   class="btn btn-danger btn-sm"
                   onclick="return confirm('Reject this join request?')">
                   Reject
                </a>
            </td>
        </tr>
        <?php } ?>
    </tbody>
</table>

<?php } ?>

<?php include "../includes/footer.php"; ?>
