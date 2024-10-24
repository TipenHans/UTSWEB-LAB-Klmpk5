<?php
include 'dbLab.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task_name = htmlspecialchars($_POST['task_name']);
    $todo_id = (int)$_POST['todo_id'];
    $deadline = $_POST['deadline'];  // Capture the deadline input

    // Debugging: Tampilkan nilai-nilai yang relevan
    echo 'todo_id: ' . $todo_id . '<br>';
    echo 'user_id: ' . $_SESSION['user_id'] . '<br>';

    // Periksa apakah todo_id valid (ada dalam tabel todos)
    $stmt = $pdo->prepare("SELECT COUNT(*) FROM todos WHERE id = :todo_id AND user_id = :user_id");
    $stmt->execute(['todo_id' => $todo_id, 'user_id' => $_SESSION['user_id']]);
    $todo_exists = $stmt->fetchColumn();

    if ($todo_exists) {
        // Jika todo_id valid, tambahkan task ke database dengan deadline
        $stmt = $pdo->prepare("INSERT INTO tasks (todo_id, task_name, deadline) VALUES (:todo_id, :task_name, :deadline)");
        $stmt->execute([
            'todo_id' => $todo_id, 
            'task_name' => $task_name, 
            'deadline' => $deadline // Store deadline into database
        ]);

        // Redirect ke halaman todo list
        header('Location: view_todo.php?todo_id=' . $todo_id);
        exit();
    } else {
        // Jika todo_id tidak valid, tampilkan pesan "No task added"
        echo "No task added. Invalid to-do list.";
    }
}