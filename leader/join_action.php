<?php
session_start();
include "../config/db.php";


if (!isset($_SESSION['role']) || $_SESSION['role'] != 'leader') {
    header("Location: ../auth/login.php");
    exit;
}

$action = $_GET['action'] ?? '';

/* ============================
   APPROVE / REJECT JOIN REQUEST
============================ */
if (($action == 'approve' || $action == 'reject') && isset($_GET['id'])) {

    $id = $_GET['id'];

    $rq = mysqli_fetch_assoc(
        mysqli_query($conn, "SELECT * FROM join_requests WHERE request_id='$id'")
    );

    if ($rq) {

        if ($action == 'approve') {

            mysqli_query($conn,
                "INSERT INTO club_members (club_id, student_id)
                 VALUES ('{$rq['club_id']}','{$rq['student_id']}')"
            );

            mysqli_query($conn,
                "UPDATE join_requests SET status='approved' WHERE request_id='$id'"
            );
        }

        if ($action == 'reject') {
            mysqli_query($conn,
                "UPDATE join_requests SET status='rejected' WHERE request_id='$id'"
            );
        }
    }

    header("Location: join_requests.php");
    exit;
}

/* ============================
   REMOVE STUDENT FROM CLUB
============================ */
if ($action == 'remove' && isset($_GET['student_id'], $_GET['club_id'])) {

    $student_id = $_GET['student_id'];
    $club_id    = $_GET['club_id'];

    // Remove from club members
    mysqli_query($conn,
        "DELETE FROM club_members 
         WHERE student_id='$student_id' AND club_id='$club_id'"
    );

    // Log in left students
    mysqli_query($conn,
        "INSERT INTO club_leave_requests (club_id, student_id, reason, left_at)
         VALUES ('$club_id','$student_id','Removed by leader',NOW())"
    );

    header("Location: members.php");
    exit;
}

/* fallback */
header("Location: dashboard.php");
exit;
