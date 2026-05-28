<?php
include "../config/db.php";

if (!isset($_GET['token'])) {
    die("Invalid request");
}

$token = mysqli_real_escape_string($conn, $_GET['token']);

$user = mysqli_query($conn, "
    SELECT * FROM users WHERE reset_token='$token'
");

if (mysqli_num_rows($user) != 1) {
    die("Invalid reset link");
}

$data = mysqli_fetch_assoc($user);

/* ✅ PHP-side expiry check (timezone safe) */
if (strtotime($data['reset_token_expiry']) < time()) {
    die("Reset link expired");
}

if (isset($_POST['reset'])) {
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);

    mysqli_query($conn, "
        UPDATE users 
        SET password='$password',
            reset_token=NULL,
            reset_token_expiry=NULL
        WHERE user_id='{$data['user_id']}'
    ");

    header("Location: login.php?reset=1");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Reset Password</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="d-flex justify-content-center align-items-center" style="height:100vh; background:#f5f5f5;">

<form method="POST" class="p-4 shadow bg-white rounded" style="width:350px;">
    <h4 class="text-center mb-3">Reset Password</h4>

    <input type="password" name="password" class="form-control mb-3"
           placeholder="Enter new password" required>

    <button name="reset" class="btn btn-success w-100">
        Reset Password
    </button>
</form>

</body>
</html>
