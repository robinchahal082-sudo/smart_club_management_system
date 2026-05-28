<?php
include "../config/db.php";

if (isset($_POST['send'])) {
    $email = mysqli_real_escape_string($conn, $_POST['email']);

    $check = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");

    if (mysqli_num_rows($check) == 1) {

        $token  = bin2hex(random_bytes(32));
        $expiry = date("Y-m-d H:i:s", time() + 900); // 15 min

        mysqli_query($conn, "
            UPDATE users 
            SET reset_token='$token',
                reset_token_expiry='$expiry'
            WHERE email='$email'
        ");

        $link = "http://localhost/club_management/auth/reset_password.php?token=$token";

    } else {
        $error = "Email not found!";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Forgot Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .token-box {
            word-break: break-all;
            background: #e9f7ef;
            border: 1px solid #b7e4c7;
            padding: 10px;
            border-radius: 6px;
            font-size: 14px;
        }
    </style>
</head>

<body class="d-flex justify-content-center align-items-center"
      style="height:100vh; background:#f5f5f5;">

<form method="POST" class="p-4 shadow bg-white rounded" style="width:420px;">
    <h4 class="text-center mb-3">Forgot Password</h4>

    <?php if (isset($error)) { ?>
        <div class="alert alert-danger"><?= $error ?></div>
    <?php } ?>

    <?php if (isset($link)) { ?>
        <div class="alert alert-success">
            <strong>Reset link generated:</strong>
            <div class="token-box mt-2">
                <a href="<?= $link ?>" target="_blank"><?= $link ?></a>
            </div>
        </div>
    <?php } ?>

    <input type="email" name="email" class="form-control mb-3"
           placeholder="Enter your email" required>

    <button name="send" class="btn btn-primary w-100">
        Send Reset Link
    </button>

    <div class="text-center mt-3">
        <a href="login.php">Back to Login</a>
    </div>
</form>

</body>
</html>
