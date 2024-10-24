<?php
include 'dbLab.php';
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit();
}

if (isset($_GET['todo_id'])) {
    $todo_id = (int)$_GET['todo_id'];

    // Handle filtering status
    $filter = isset($_GET['filter_status']) ? $_GET['filter_status'] : 'all';

    if ($filter == 'completed') {
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE todo_id = :todo_id AND is_completed = 1");
    } elseif ($filter == 'incomplete') {
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE todo_id = :todo_id AND is_completed = 0");
    } else {
        $stmt = $pdo->prepare("SELECT * FROM tasks WHERE todo_id = :todo_id");
    }
    $stmt->execute(['todo_id' => $todo_id]);
    $tasks = $stmt->fetchAll();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View To-Do List</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <script>

    function startCountdown(deadlineElements) {
        deadlineElements.forEach(element => {
            const deadline = new Date(element.dataset.deadline).getTime();
            const countdownElement = element.querySelector('.countdown');
            const overdueElement = element.querySelector('.overdue');
            const earlyCompletionElement = element.querySelector('.early-completion');

            function updateCountdown() {
                const now = new Date().getTime();
                const distance = deadline - now;

                if (distance > 0) {
                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    countdownElement.textContent = `${days}d ${hours}h ${minutes}m ${seconds}s`;
                    overdueElement.textContent = '';
                } else {
                    countdownElement.textContent = '';
                    const overdueDistance = now - deadline;
                    const overdueDays = Math.floor(overdueDistance / (1000 * 60 * 60 * 24));
                    const overdueHours = Math.floor((overdueDistance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const overdueMinutes = Math.floor((overdueDistance % (1000 * 60 * 60)) / (1000 * 60));
                    const overdueSeconds = Math.floor((overdueDistance % (1000 * 60)) / 1000);

                    overdueElement.textContent = `Overdue by ${overdueDays}d ${overdueHours}h ${overdueMinutes}m ${overdueSeconds}s`;
                    earlyCompletionElement.textContent = '';
                }
        }

        updateCountdown();
        const intervalId = setInterval(updateCountdown, 1000);

        if (element.querySelector('.badge').textContent.trim() === 'Completed') {
            clearInterval(intervalId);
            countdownElement.textContent = '';
            overdueElement.textContent = '';
            
            const completedTime = new Date().getTime();
            const earlyDistance = deadline - completedTime;
            if (earlyDistance > 0) {
                const earlyDays = Math.floor(earlyDistance / (1000 * 60 * 60 * 24));
                const earlyHours = Math.floor((earlyDistance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const earlyMinutes = Math.floor((earlyDistance % (1000 * 60 * 60)) / (1000 * 60));
                const earlySeconds = Math.floor((earlyDistance % (1000 * 60)) / 1000);

                earlyCompletionElement.textContent = `Completed ${earlyDays}d ${earlyHours}h ${earlyMinutes}m ${earlySeconds}s early.`;
            } else {
                earlyCompletionElement.textContent = 'Completed after the deadline.';
            }
        }

        const updateForm = element.querySelector('form');
        updateForm.addEventListener('submit', (event) => {
            const completionStatus = updateForm.querySelector('select[name="is_completed"]').value;
            if (completionStatus == 1) {
                clearInterval(intervalId);
                countdownElement.textContent = '';
                overdueElement.textContent = '';
                
                const completedTime = new Date().getTime();
                const earlyDistance = deadline - completedTime;
                if (earlyDistance > 0) {
                    const earlyDays = Math.floor(earlyDistance / (1000 * 60 * 60 * 24));
                    const earlyHours = Math.floor((earlyDistance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const earlyMinutes = Math.floor((earlyDistance % (1000 * 60 * 60)) / (1000 * 60));
                    const earlySeconds = Math.floor((earlyDistance % (1000 * 60)) / 1000);

                    earlyCompletionElement.textContent = `Completed ${earlyDays}d ${earlyHours}h ${earlyMinutes}m ${earlySeconds}s early.`;
                } else {
                    earlyCompletionElement.textContent = 'Completed after the deadline.';
                }
            } else {
                if (now > deadline) {
                    const overdueDistance = now - deadline;
                    const overdueDays = Math.floor(overdueDistance / (1000 * 60 * 60 * 24));
                    const overdueHours = Math.floor((overdueDistance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const overdueMinutes = Math.floor((overdueDistance % (1000 * 60 * 60)) / (1000 * 60));
                    const overdueSeconds = Math.floor((overdueDistance % (1000 * 60)) / 1000);

                    overdueElement.textContent = `Overdue by ${overdueDays}d ${overdueHours}h ${overdueMinutes}m ${overdueSeconds}s`;
                }
            }
        });
    });
}




        window.addEventListener('DOMContentLoaded', () => {
            const deadlineElements = document.querySelectorAll('.task-deadline');
            startCountdown(deadlineElements);
        });
    </script>
</head>
<body>
    <div class="container mt-5">
        <h1 class="mb-4">Tasks for To-Do List</h1>

        <form method="get" action="view_todo.php" class="mb-3 row g-3">
            <input type="hidden" name="todo_id" value="<?php echo $todo_id; ?>">
            <div class="col-auto">
                <select name="filter_status" class="form-select">
                    <option value="all" <?php echo $filter == 'all' ? 'selected' : ''; ?>>All Tasks</option>
                    <option value="completed" <?php echo $filter == 'completed' ? 'selected' : ''; ?>>Completed Tasks</option>
                    <option value="incomplete" <?php echo $filter == 'incomplete' ? 'selected' : ''; ?>>Incomplete Tasks</option>
                </select>
            </div>
            <div class="col-auto">
                <button type="submit" class="btn btn-primary">Filter</button>
            </div>
        </form>

        <ul class="list-group mb-4">
            <?php foreach ($tasks as $task): ?>
                <li class="list-group-item d-flex justify-content-between align-items-center task-deadline" data-deadline="<?php echo $task['deadline']; ?>">
        <div>
            <strong><?php echo htmlspecialchars($task['task_name']); ?></strong> - 
            <span class="badge <?php echo $task['is_completed'] ? 'bg-success' : 'bg-warning'; ?>">
                <?php echo $task['is_completed'] ? 'Completed' : 'Incomplete'; ?>
            </span><br>
            <span class="text-muted">Deadline: <?php echo htmlspecialchars($task['deadline']); ?></span><br>
            <span class="text-dark countdown"></span>
            <span class="text-danger overdue"></span>
            <span class="text-success early-completion"></span> 
        </div>
        <div>
            <form method="post" action="update_task_status.php" class="d-inline">
                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                <select name="is_completed" class="form-select d-inline w-auto">
                    <option value="1" <?php echo $task['is_completed'] ? 'selected' : ''; ?>>Completed</option>
                    <option value="0" <?php echo !$task['is_completed'] ? 'selected' : ''; ?>>Incomplete</option>
                </select>
                <a href="add_task_description.php?task_id=<?php echo $task['id']; ?>" class="btn btn-sm btn-info">Add Description</a>
                <button type="submit" class="btn btn-sm btn-outline-secondary">Update</button>
            </form>
            <form method="post" action="delete_task.php" class="d-inline" onsubmit="return confirm('Are you sure you want to delete this task?');">
                <input type="hidden" name="task_id" value="<?php echo $task['id']; ?>">
                <button type="submit" class="btn btn-sm btn-danger">Delete</button>
            </form>
        </div>
    </li>

            <?php endforeach; ?>
        </ul>

        <form method="post" action="add_task.php" class="row g-3">
            <input type="hidden" name="todo_id" value="<?php echo $todo_id; ?>">
            <div class="col-md-6">
                <input type="text" name="task_name" class="form-control" placeholder="New Task" required>
            </div>
            <div class="col-md-2">
                <input type="datetime-local" name="deadline" class="form-control" required>
            </div>
            <div class="col-md-4">
                <button type="submit" class="btn btn-success w-100">Add Task</button>
            </div>
        </form>

        <div class="mt-4">
            <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
