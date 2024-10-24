<?php
include 'dbLab.php';
session_start();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $title = htmlspecialchars($_POST['title']);
    $user_id = $_SESSION['user_id'];

    $stmt = $pdo->prepare("INSERT INTO todos (user_id, title) VALUES (:user_id, :title)");
    $stmt->execute(['user_id' => $user_id, 'title' => $title]);

    header('Location: dashboard.php');
}
