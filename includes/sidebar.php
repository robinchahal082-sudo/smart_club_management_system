<?php
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>

<div class="d-flex">

    <!-- SIDEBAR -->
    <div class="bg-dark text-white p-3 sidebar" style="width:240px; min-height:100vh;">
        <h4 class="mb-3">Club System</h4>
        <hr>

        <!-- COMMON DASHBOARD -->
        <a href="../<?php echo $_SESSION['role']; ?>/dashboard.php" class="d-block text-white mb-2">
            <i class="fa fa-home"></i> Dashboard
        </a>

        <!-- ================= ADMIN ================= -->
        <?php if ($_SESSION['role'] == 'admin') { ?>

            <a href="../admin/clubs_manage.php" class="d-block text-white mb-2">
                <i class="fa fa-building"></i> Manage Clubs
            </a>


            <a href="../admin/events.php" class="d-block text-white mb-2">
                <i class="fa fa-calendar-check"></i> Event Requests
            </a>

            <a href="../admin/results.php" class="d-block text-white mb-2">
                <i class="fa fa-trophy"></i> View Results
            </a>

            <a href="../admin/reports.php" class="d-block text-white mb-2">
                <i class="fa fa-chart-bar"></i> Reports
            </a>

        <?php } ?>


        <!-- ================= LEADER ================= -->
        <?php if ($_SESSION['role'] == 'leader') { ?>


            <a href="../leader/members.php" class="d-block text-white mb-2">
                <i class="fa fa-users"></i> Club Members
            </a>

            <a href="../leader/join_requests.php" class="d-block text-white mb-2">
                <i class="fa fa-user-plus"></i> Join Requests
            </a>



            <a href="../leader/events_manage.php" class="d-block text-white mb-2">
                <i class="fa fa-calendar"></i> Manage Events
            </a>

            <a href="../leader/event_enrollments.php" class="d-block text-white mb-2">
                <i class="fa fa-list"></i> Event Enrollments
            </a>

            <a href="../leader/results_manage.php" class="d-block text-white mb-2">
                <i class="fa fa-trophy"></i> Manage Results
            </a>

            <!-- NEW -->
            <a href="../leader/left_students.php" class="d-block text-white mb-2">
                <i class="fa fa-user-times"></i> Left Students
            </a>

        <?php } ?>

        <!-- ================= STUDENT ================= -->
        <?php if ($_SESSION['role'] == 'student') { ?>



            <a href="../student/my_clubs.php" class="d-block text-white mb-2">
                <i class="fa fa-building"></i> My Clubs
            </a>

            <!-- 🔥 ADD THIS -->
            <a href="../student/join_clubs.php" class="d-block text-white mb-2">
                <i class="fa fa-plus-circle"></i> Join New Club
            </a>

            <a href="../student/event_details.php" class="d-block text-white mb-2">
                <i class="fa fa-calendar-check"></i> Approved Events
            </a>


            <a href="../student/my_enrollments.php" class="d-block text-white mb-2">
                <i class="fa fa-calendar"></i> My Enrollments
            </a>

            <a href="../student/performance.php" class="d-block text-white mb-2">
                <i class="fa fa-chart-line"></i> My Performance
            </a>

            <a href="../student/results.php" class="d-block text-white mb-2">
                <i class="fa fa-trophy"></i> My Results
            </a>

            <a href="../student/leave_club.php" class="d-block text-white mb-2">
                <i class="fa fa-sign-out-alt"></i> Leave Club
            </a>

        <?php } ?>


        <!-- ================= FACULTY ================= -->
        <?php if ($_SESSION['role'] == 'faculty') { ?>


            <a href="../faculty/club_details.php" class="d-block text-white mb-2">
                <i class="fa fa-building"></i> Club Details
            </a>

            <a href="../faculty/members.php" class="d-block text-white mb-2">
                <i class="fa fa-users"></i> Club Members
            </a>

            <a href="../faculty/events.php" class="d-block text-white mb-2">
                <i class="fa fa-calendar"></i> Event Monitoring
            </a>

            <a href="../faculty/student_performance.php" class="d-block text-white mb-2">
                <i class="fa fa-chart-line"></i> Student Performance
            </a>

            <a href="../faculty/remarks.php" class="d-block text-white mb-2">
                <i class="fa fa-comment"></i> Remarks
            </a>

            <!-- NEW -->
            <a href="../faculty/left_students.php" class="d-block text-white mb-2">
                <i class="fa fa-user-times"></i> Left Students
            </a>

        <?php } ?>

        <hr>

        <!-- LOGOUT -->
        <a href="../auth/logout.php" class="d-block text-white">
            <i class="fa fa-sign-out-alt"></i> Logout
        </a>
    </div>

    <!-- PAGE CONTENT START -->
    <div class="p-4 w-100">