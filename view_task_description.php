<?php
include 'dbLab.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['task_id'])) {
    $task_id = (int)$_GET['task_id'];

    $stmt = $pdo->prepare("SELECT * FROM tasks WHERE id = :task_id");
    $stmt->execute(['task_id' => $task_id]);
    $task = $stmt->fetch();

    if (!$task) {
        echo "Task not found!";
        exit();
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $description = $_POST['description'];

        $stmt = $pdo->prepare("UPDATE tasks SET description = :description WHERE id = :task_id");
        $stmt->execute([
            'description' => $description,
            'task_id' => $task_id
        ]);

        header("Location: view_todo.php?todo_id=" . $task['todo_id']);
        exit();
    }
} else {
    echo "Invalid request!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Task Description</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-5">
    <h1>Edit Description for Task: <?php echo htmlspecialchars($task['task_name']); ?></h1>

    <form method="post" class="mt-4">
        <div class="mb-3">
            <label for="description" class="form-label">Description</label>
            <textarea name="description" id="description" rows="5" class="form-control"><?php echo htmlspecialchars($task['description']); ?></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Save Description</button>
        <a href="view_todo.php?todo_id=<?php echo $task['todo_id']; ?>" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

