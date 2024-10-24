<?php
include 'dbLab.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = filter_var(trim($_POST['username']), FILTER_SANITIZE_STRING);
    $email = filter_var(trim($_POST['email']), FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $notification = "
            <script>
                Swal.fire({
                    icon: 'error',
                    title: 'Invalid Email!',
                    text: 'Please enter a valid email address.',
                    position: 'center'
                });
            </script>
        ";
    } else {
        if ($password !== $confirm_password) {
            $notification = "
                <script>
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Passwords do not match!'
                    });
                </script>
            ";
        } else {
            $hashed_password = password_hash($password, PASSWORD_BCRYPT);

            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email");
            $stmt->execute(['email' => $email]);

            if ($stmt->rowCount() > 0) {
                $notification = "
                    <script>
                        Swal.fire({
                            icon: 'warning',
                            title: 'Email Already Registered!',
                            text: 'Please login instead.',
                            confirmButtonText: 'Login',
                            position: 'center'
                        }).then(function() {
                            window.location.href = 'login.php';
                        });
                    </script>
                ";
            } else {
                $stmt = $pdo->prepare("INSERT INTO users (username, email, password) 
                                       VALUES (:full_name, :email, :password)");
                $stmt->execute([
                    'full_name' => $username,
                    'email' => $email, 
                    'password' => $hashed_password
                ]);

                $notification = "
                        Swal.fire({
                            icon: 'success',
                            title: 'Registration Successful!',
                            text: 'Please login to continue.',
                            confirmButtonText: 'Login',
                            position: 'center'
                        }).then(function() {
                            window.location.href = 'login.php';
                        });
                ";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <title>Register</title>
</head>
<body>
<div class="container mt-5">
    <div class="row justify-content-center">
        <div class="col-md-4">
            <div class="card">
                <div class="card-header text-center">
                    <h4>Register</h4>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label for="username" class="form-label">Username</label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Password</label>
                            <input type="password" class="form-control" id="password" name="password" required>
                        </div>
                        <div class="mb-3">
                            <label for="confirm_password" class="form-label">Confirm Password</label>
                            <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Register</button>
                    </form>
                </div>
                <div class="card-footer text-center">
                    <p>Already have an account? <a href="login.php">Login here</a>.</p>
                </div>
            </div>
        </div>
    </div>
</div>
<script>
    <?php if (!empty($notification)) echo $notification; ?>
</script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
