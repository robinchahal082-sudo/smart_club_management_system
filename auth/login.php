<?php
session_start();
include "../config/db.php";

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $query = "SELECT * FROM users WHERE email='$email' AND status='active'";
    $result = mysqli_query($conn, $query);

    if (mysqli_num_rows($result) == 1) {
        $user = mysqli_fetch_assoc($result);

        if (password_verify($password, $user['password'])) {

            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['name'] = $user['name'];

            // Role-based redirect
            if ($user['role'] == 'admin') {
                header("Location: ../admin/dashboard.php");
            } elseif ($user['role'] == 'leader') {
                header("Location: ../leader/dashboard.php");
            } elseif ($user['role'] == 'student') {
                header("Location: ../student/dashboard.php");
            } elseif ($user['role'] == 'faculty') {
                header("Location: ../faculty/dashboard.php");
            }
            exit;
        } else {
            $error = "Invalid password!";
        }
    } else {
        $error = "User not found or account inactive!";
    }
}
?>

<!DOCTYPE html>
<html>

<head>
    <title>Login | Club Management</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="d-flex justify-content-center align-items-center" style="height:100vh;">
    
    <form method="POST" class="p-4 shadow bg-white rounded" style="width:350px;">
        <h4 class="text-center mb-3">Login</h4>

        <?php if (isset($error))
            echo "<div class='alert alert-danger'>$error</div>"; ?>

        <input type="email" name="email" class="form-control mb-3" placeholder="Email" required>
        <input type="password" name="password" class="form-control mb-3" placeholder="Password" required>

        <button name="login" class="btn btn-primary w-100">Login</button>

        <hr>
        <div class="text-center">
            New user?
            <a href="signup.php">Create account</a>
        </div>
        <div class="text-center mt-2">
            <a href="forgot_password.php">Forgot Password?</a>
        </div>

    </form>

</body>

</html>