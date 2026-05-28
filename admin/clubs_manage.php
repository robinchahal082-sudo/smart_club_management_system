<?php
session_start();

if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";
include "../includes/header.php";
include "../includes/sidebar.php";

/* ==============================
   ASSIGN / CHANGE CLUB LEADER
============================== */

if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['leader_id'], $_POST['club_id'])) {

    $club_id   = $_POST['club_id'];
    $leader_id = $_POST['leader_id'];

    // Old leader fetch
    $old = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT leader_id 
        FROM clubs 
        WHERE club_id='$club_id'
    "));

    $old_leader = $old['leader_id'];

    // New leader assign
    mysqli_query($conn,"
        UPDATE clubs 
        SET leader_id='$leader_id' 
        WHERE club_id='$club_id'
    ");

    // New leader role = leader
    mysqli_query($conn,"
        UPDATE users 
        SET role='leader' 
        WHERE user_id='$leader_id'
    ");

    // Old leader role = student
    if ($old_leader && $old_leader != $leader_id) {

        mysqli_query($conn,"
            UPDATE users 
            SET role='student' 
            WHERE user_id='$old_leader'
        ");
    }

    header("Location: clubs_manage.php");
    exit;
}

/* ==============================
   DELETE CLUB
============================== */

if (isset($_GET['delete'])) {

    $club_id = $_GET['delete'];

    // Club leader fetch
    $club = mysqli_fetch_assoc(mysqli_query($conn,"
        SELECT leader_id 
        FROM clubs 
        WHERE club_id='$club_id'
    "));

    // Leader role back to student
    if ($club && $club['leader_id']) {

        $leader_id = $club['leader_id'];

        mysqli_query($conn,"
            UPDATE users 
            SET role='student' 
            WHERE user_id='$leader_id'
        ");
    }

    // Delete club
    mysqli_query($conn,"
        DELETE FROM clubs 
        WHERE club_id='$club_id'
    ");

    header("Location: clubs_manage.php");
    exit;
}

/* ==============================
   FETCH CLUBS
============================== */

$clubs = mysqli_query($conn, "
    SELECT c.club_id, c.club_name,
           l.name AS leader_name,
           f.name AS faculty_name
    FROM clubs c
    LEFT JOIN users l ON c.leader_id = l.user_id
    LEFT JOIN users f ON c.faculty_id = f.user_id
");

?>

<h2>Manage Clubs</h2>

<table class="table table-bordered mt-3 align-middle">

    <thead>
        <tr>
            <th>Club Name</th>
            <th>Leader</th>
            <th>Faculty</th>
            <th class="text-center">Actions</th>
        </tr>
    </thead>

    <tbody>

    <?php while ($c = mysqli_fetch_assoc($clubs)) { ?>

        <tr>

            <td>
                <?= htmlspecialchars($c['club_name']) ?>
            </td>

            <td>
                <?= $c['leader_name'] 
                    ? htmlspecialchars($c['leader_name']) 
                    : '<span class="text-muted">Not Assigned</span>' ?>
            </td>

            <td>
                <?= $c['faculty_name'] 
                    ? htmlspecialchars($c['faculty_name']) 
                    : '<span class="text-muted">Not Assigned</span>' ?>
            </td>

            <td class="text-center">

                <a href="club_details.php?id=<?= $c['club_id'] ?>"
                   class="btn btn-info btn-sm mb-1">
                    View Details
                </a>

                <a href="club_members.php?club_id=<?= $c['club_id'] ?>"
                   class="btn btn-secondary btn-sm mb-1">
                    View Members
                </a>

                <a href="clubs_manage.php?delete=<?= $c['club_id'] ?>"
                   onclick="return confirm('Are you sure you want to delete this club?')"
                   class="btn btn-danger btn-sm mb-1">
                    Delete
                </a>

            </td>

        </tr>

    <?php } ?>

    </tbody>

</table>

<?php include "../includes/footer.php"; ?>