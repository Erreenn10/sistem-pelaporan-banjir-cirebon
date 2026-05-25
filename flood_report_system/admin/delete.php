<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header("Location: login.php");
    exit();
}

include '../config/database.php';

$id = $_GET['id'];
$stmt = $pdo->prepare("DELETE FROM reports WHERE id = ?");
$stmt->execute([$id]);

header("Location: index.php");
exit();
?>