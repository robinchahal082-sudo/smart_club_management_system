<?php
session_start();
if ($_SESSION['role'] != 'student') {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

$student_id = $_SESSION['user_id'];

/* Clubs jisme student already member hai */
$joined = mysqli_query($conn,"
    SELECT club_id 
    FROM club_members 
    WHERE student_id='$student_id'
");

$joined_ids = [];
while ($j = mysqli_fetch_assoc($joined)) {
    $joined_ids[] = $j['club_id'];
}

/* Clubs jisme join request PENDING hai */
$requested = mysqli_query($conn,"
    SELECT club_id 
    FROM join_requests 
    WHERE student_id='$student_id'
    AND status='pending'
");

$requested_ids = [];
while ($r = mysqli_fetch_assoc($requested)) {
    $requested_ids[] = $r['club_id'];
}

/* All clubs */
$clubs = mysqli_query($conn,"
    SELECT c.club_id, c.club_name, u.name AS leader
    FROM clubs c
    JOIN users u ON c.leader_id = u.user_id
");
?>

<div class="container-fluid">
    <h2 class="mb-4">Join Clubs</h2>

    <table class="table table-bordered align-middle">
        <thead>
            <tr>
                <th>Club</th>
                <th>Leader</th>
                <th class="text-center">Status</th>
            </tr>
        </thead>

        <tbody>
        <?php while ($c = mysqli_fetch_assoc($clubs)) { ?>
            <tr>
                <td><?= htmlspecialchars($c['club_name']) ?></td>
                <td><?= htmlspecialchars($c['leader']) ?></td>
                <td class="text-center">

                    <?php if (in_array($c['club_id'], $joined_ids)) { ?>

                        <span class="badge bg-success px-3 py-2">
                            Already Member
                        </span>

                    <?php } elseif (in_array($c['club_id'], $requested_ids)) { ?>

                        <div class="d-flex justify-content-center align-items-center gap-2">
                            <span class="badge bg-warning text-dark px-3 py-2">
                                Request Sent
                            </span>

                            <a href="cancel_join_request.php?id=<?= $c['club_id'] ?>"
                               onclick="return confirm('Cancel join request?')"
                               class="btn btn-danger btn-sm">
                                Cancel
                            </a>
                        </div>

                    <?php } else { ?>

                        <a href="send_join_request.php?id=<?= $c['club_id'] ?>"
                           class="btn btn-primary btn-sm">
                            Join Club
                        </a>

                    <?php } ?>

                </td>
            </tr>
        <?php } ?>
        </tbody>
    </table>
</div>

<?php include "../includes/footer.php"; ?>
