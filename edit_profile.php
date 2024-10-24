<?php
session_start();
require 'dbLab.php';

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("SELECT * FROM users WHERE id = :user_id");
$stmt->execute(['user_id' => $user_id]);
$user = $stmt->fetch();
$error = '';
$success = '';

if (isset($_POST['update_profile'])) {
    $username = $_POST['username'];
    $email = $_POST['email'];

    $stmt = $pdo->prepare("SELECT * FROM users WHERE email = :email AND id != :user_id");
    $stmt->execute(['email' => $email, 'user_id' => $user_id]);
    $existing_user = $stmt->fetch();

    if ($existing_user) {
        $error = "Email is already taken. Please choose another.";
    } else {
        $stmt = $pdo->prepare("UPDATE users SET username = :username, email = :email WHERE id = :user_id");
        $stmt->execute(['username' => $username, 'email' => $email, 'user_id' => $user_id]);
        $success = true;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-QWTKZyjpPEjISv5WaRU9OFeRpok6YctnYmDr5pNlyT2bRjXh0JMhjY6hW+ALEwIH" crossorigin="anonymous">
</head>
<body>
    <div class="container">
        <div class="d-flex justify-content-center align-items-center vh-100">
            <div class="card">
                <div class="card-header">
                    <h1>Edit Profile</h1>
                </div>
                <div class="card-body">
                    <form method="POST" action="">
                        <div class="mb-3">
                            <label class="form-label"><strong>User ID (cannot be changed):</strong></label>
                            <p class="form-control-plaintext"><?php echo $user['id']; ?></p>
                        </div>
                        <div class="mb-3">
                            <label for="username" class="form-label"><strong>Username:</strong></label>
                            <input type="text" id="username" name="username" class="form-control" value="<?php echo htmlspecialchars($user['username']); ?>" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label"><strong>Email:</strong></label>
                            <input type="email" id="email" name="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required>
                        </div>
                        <div class="d-flex justify-content-between mt-4">
                            <button type="submit" name="update_profile" class="btn btn-success">Update Profile</button>
                            <button type="button" class="btn btn-danger" onclick="window.location.href='profile.php';">Cancel</button>
                        </div>
                    </form>  
                </div>
            </div>
        </div>            
    </div>

    <script>
    <?php if (!empty($success)): ?>
        Swal.fire({
            icon: 'success',
            title: 'Profile Updated',
            text: 'Your profile has been successfully updated.',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'profile.php';
            }
        });
    <?php elseif (!empty($error)): ?>
        Swal.fire({
            icon: 'error',
            title: 'Error',
            text: '<?php echo $error; ?>',
            confirmButtonText: 'OK'
        });
    <?php endif; ?>
    </script>
</body>
</html>
co
