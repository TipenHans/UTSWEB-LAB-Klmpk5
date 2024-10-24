<?php
include 'dbLab.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task_id = (int)$_POST['task_id'];
    $is_completed = (int)$_POST['is_completed'];

    $stmt = $pdo->prepare("UPDATE tasks SET is_completed = :is_completed WHERE id = :task_id");
    $stmt->execute(['is_completed' => $is_completed, 'task_id' => $task_id]);

    $stmt = $pdo->prepare("SELECT todo_id FROM tasks WHERE id = :task_id");
    $stmt->execute(['task_id' => $task_id]);
    $todo_id = $stmt->fetchColumn();

    header('Location: view_todo.php?todo_id=' . $todo_id);
}
