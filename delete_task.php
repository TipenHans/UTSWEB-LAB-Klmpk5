<?php
include 'dbLab.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $task_id = (int)$_POST['task_id'];

    // Hapus task dari database berdasarkan task_id saja
    $stmt = $pdo->prepare("DELETE FROM tasks WHERE id = :task_id");
    $stmt->execute(['task_id' => $task_id]);

    // Redirect ke halaman sebelumnya (view_todo.php) setelah task dihapus
    header('Location: ' . $_SERVER['HTTP_REFERER']);
}