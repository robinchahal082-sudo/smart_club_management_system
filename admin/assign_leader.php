<?php
session_start();
if (!isset($_SESSION['role']) || $_SESSION['role'] != 'admin') {
    header("Location: ../auth/login.php");
    exit;
}

include "../config/db.php";

$club_id    = $_GET['club_id'] ?? null;
$new_leader = $_GET['uid'] ?? null;

if (!$club_id || !$new_leader) {
    die("Invalid request");
}

/* 🔥 GET CURRENT LEADER DIRECTLY */
$res = mysqli_query($conn,"
    SELECT leader_id FROM clubs WHERE club_id='$club_id'
");

$row = mysqli_fetch_assoc($res);
$current_leader = $row['leader_id'];

/* 🔥 DEMOTE CURRENT LEADER (NO IF, NO CONFUSION) */
if ($current_leader) {
    mysqli_query($conn,"
        UPDATE users 
        SET role='student'
        WHERE user_id='$current_leader'
    ");

    // session sync
    if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $current_leader) {
        $_SESSION['role'] = 'student';
    }
}

/* 🔥 ASSIGN NEW LEADER */
mysqli_query($conn,"
    UPDATE clubs
    SET leader_id='$new_leader'
    WHERE club_id='$club_id'
");

/* 🔥 PROMOTE NEW LEADER */
mysqli_query($conn,"
    UPDATE users
    SET role='leader'
    WHERE user_id='$new_leader'
");

header("Location: club_members.php?club_id=$club_id");
exit;
