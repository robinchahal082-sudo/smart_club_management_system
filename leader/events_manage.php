<?php
session_start();
include "../config/db.php";

if ($_SESSION['role'] != 'leader') {
    header("Location: ../auth/login.php");
    exit;
}

$leader_id = $_SESSION['user_id'];

/* Leader ka club fetch */
$clubRes = mysqli_query($conn,
    "SELECT club_id, club_name FROM clubs WHERE leader_id='$leader_id'"
);
$club = mysqli_fetch_assoc($clubRes);

/* EVENT CREATE / REQUEST */
if (isset($_POST['create_event'])) {
    $title = mysqli_real_escape_string($conn, $_POST['title']);
    $desc  = mysqli_real_escape_string($conn, $_POST['description']);
    $date  = $_POST['event_date'];

    mysqli_query($conn, "
        INSERT INTO events (club_id, title, description, event_date, approval_status, created_by)
        VALUES (
            '{$club['club_id']}',
            '$title',
            '$desc',
            '$date',
            'pending',
            '$leader_id'
        )
    ");

    $success = "Event request sent to admin for approval!";
}

/* Leader ke events */
$events = mysqli_query($conn, "
    SELECT * FROM events 
    WHERE club_id='{$club['club_id']}'
    ORDER BY created_at DESC
");

include "../includes/header.php";
include "../includes/sidebar.php";
?>

<h3>Manage Events (<?= $club['club_name'] ?>)</h3>

<?php if (isset($success)) { ?>
    <div class="alert alert-success"><?= $success ?></div>
<?php } ?>

<!-- ✅ EVENT REQUEST FORM -->
<div class="card p-3 mb-4">
    <h5>Create / Request New Event</h5>

    <form method="POST">
        <input type="text" name="title" class="form-control mb-2"
               placeholder="Event Title" required>

        <textarea name="description" class="form-control mb-2"
                  placeholder="Event Description"></textarea>

        <input type="date" name="event_date" class="form-control mb-2" required>

        <button name="create_event" class="btn btn-primary">
            Send Event Request
        </button>
    </form>
</div>

<!-- 📋 EVENT LIST -->
<div class="card p-3">
    <h5>Your Events</h5>

    <table class="table table-bordered">
        <tr>
            <th>Event</th>
            <th>Date</th>
            <th>Status</th>
        </tr>

        <?php while ($e = mysqli_fetch_assoc($events)) { ?>
            <tr>
                <td><?= $e['title'] ?></td>
                <td><?= $e['event_date'] ?></td>
                <td>
                    <?php
                        if ($e['approval_status'] == 'pending')
                            echo "<span class='badge bg-warning'>Pending</span>";
                        elseif ($e['approval_status'] == 'approved')
                            echo "<span class='badge bg-success'>Approved</span>";
                        else
                            echo "<span class='badge bg-danger'>Rejected</span>";
                    ?>
                </td>
            </tr>
        <?php } ?>
    </table>
</div>

<?php include "../includes/footer.php"; ?>
