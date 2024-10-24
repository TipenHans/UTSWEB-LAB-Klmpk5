<?php
include 'dbLab.php';
session_start();

if (isset($_GET['todo_id'])) {
    $todo_id = (int) $_GET['todo_id'];

    // Delete all tasks associated with the todo list
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE todo_id = :todo_id");
    $stmt->execute(['todo_id' => $todo_id]);

    // Now delete the todo list itself
    $stmt = $pdo->prepare("DELETE FROM todos WHERE id = :todo_id AND user_id = :user_id");
    $stmt->execute(['todo_id' => $todo_id, 'user_id' => $_SESSION['user_id']]);

    header('Location: dashboard.php');
}
