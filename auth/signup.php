<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);
session_start();

include "../config/db.php";

$msg = "";

if (isset($_POST['signup'])) {

    $user_id  = mysqli_real_escape_string($conn, $_POST['user_id']);
    $name     = mysqli_real_escape_string($conn, $_POST['name']);
    $email    = mysqli_real_escape_string($conn, $_POST['email']);
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $role     = $_POST['role'];

    /* Only student & faculty allowed */
    if (!in_array($role, ['student', 'faculty'])) {
        $msg = "Invalid role selected!";
    } else {

        /* Check UID exists */
        $check_uid = mysqli_query(
            $conn,
            "SELECT user_id FROM users WHERE user_id='$user_id'"
        );

        if (mysqli_num_rows($check_uid) > 0) {
            $msg = "User ID already exists!";
        } else {

            /* Check email exists */
            $check_email = mysqli_query(
                $conn,
                "SELECT user_id FROM users WHERE email='$email'"
            );

            if (mysqli_num_rows($check_email) > 0) {
                $msg = "Email already registered!";
            } else {

                mysqli_query($conn, "
                    INSERT INTO users (user_id, name, email, password, role, status)
                    VALUES ('$user_id','$name','$email','$password','$role','active')
                ");

                $msg = "Account created successfully! Your User ID is: $user_id";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Signup | Club Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="d-flex justify-content-center align-items-center" style="height:100vh; background:#f5f5f5;">

<div class="card shadow p-4" style="width:380px;">
    <h4 class="text-center mb-3">Create Account</h4>

    <?php if($msg){ ?>
        <div class="alert alert-info"><?= $msg ?></div>
    <?php } ?>

    <form method="POST">

        <!-- USER ID -->
        <input type="text" name="user_id" class="form-control mb-2"
               placeholder="User ID" required>

        <input type="text" name="name" class="form-control mb-2"
               placeholder="Full Name" required>

        <input type="email" name="email" class="form-control mb-2"
               placeholder="Email" required>

        <input type="password" name="password" class="form-control mb-2"
               placeholder="Password" required>

        <!-- ROLE SELECTION -->
        <select name="role" class="form-control mb-3" required>
            <option value="">Select Role</option>
            <option value="student">Student</option>
            <option value="faculty">Faculty</option>
        </select>

        <button name="signup" class="btn btn-primary w-100">
            Sign Up
        </button>
    </form>

    <hr>
    <div class="text-center">
        Already have an account?
        <a href="login.php">Login</a>
    </div>
</div>

</body>
</html>
