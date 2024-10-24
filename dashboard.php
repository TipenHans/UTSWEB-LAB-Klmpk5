<?php
include 'dbLab.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

$user_id = $_SESSION['user_id'];

$stmt = $pdo->prepare("
    SELECT 
        todos.id, todos.title,
        (SELECT COUNT(*) FROM tasks WHERE tasks.todo_id = todos.id) AS total_tasks,
        (SELECT COUNT(*) FROM tasks WHERE tasks.todo_id = todos.id AND tasks.is_completed = 1) AS completed_tasks
    FROM todos
    WHERE todos.user_id = :user_id
");
$stmt->execute(['user_id' => $user_id]);
$todos = $stmt->fetchAll();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Your Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="styles.css">
</head>
<body>

<div class="container mt-5">
    <div class="running-text">
        <div class="text">
            Semangat Belajar! Kamu pasti bisa mencapai tujuanmu!
        </div>
    </div>
    <div class="card p-4 shadow-sm mt-5">
        <h3 class="text-center mb-4">Create a New To-Do List</h3>
        <form method="post" action="create_todo.php" class="row g-3">
            <div class="col-12">
                <input type="text" name="title" class="form-control" placeholder="List Title" required>
            </div>
            <div class="col-12 d-flex justify-content-between">
                <button type="submit" class="btn btn-success">Create New To-Do List</button>
                <a href="logout.php" class="btn btn-danger">Logout</a>
            </div>
        </form>
    </div>

    <h1 class="mt-4 text-center">To-Do Lists</h1>

    <div class="row mt-4">
        <?php if (empty($todos)): ?>
            <div class="col-12 text-center">
                <p class="text-muted">You don't have any to-do lists yet. Create one below!</p>
            </div>
        <?php else: ?>
            <?php foreach ($todos as $todo): 
                $totalTasks = $todo['total_tasks'];
                $completedTasks = $todo['completed_tasks'];
                $progress = $totalTasks > 0 ? ($completedTasks / $totalTasks) * 100 : 0;
            ?>
                <div class="col-md-6 col-lg-4 mb-3">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="progress mb-3">
                                <div class="progress-bar <?php echo ($progress == 100) ? 'bg-success' : ''; ?>" role="progressbar" 
                                     style="width: <?php echo $progress; ?>%" 
                                     aria-valuenow="<?php echo round($progress); ?>" 
                                     aria-valuemin="0" aria-valuemax="100">
                                </div>
                            </div>
                            <h5 class="card-title text-primary"><?php echo htmlspecialchars($todo['title']); ?></h5>
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="view_todo.php?todo_id=<?php echo $todo['id']; ?>" class="btn btn-outline-info btn-sm">View Tasks</a>
                                <a href="delete_todo.php?todo_id=<?php echo $todo['id']; ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Are you sure?')">Delete</a>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
