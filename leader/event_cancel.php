<?php
session_start();
include "../config/db.php";
if ($_SESSION['role']!='leader'){ header("Location: ../auth/login.php"); exit; }

$event_id = $_GET['id'];
mysqli_query($conn,"DELETE FROM events WHERE event_id='$event_id'");
header("Location: events_manage.php");
