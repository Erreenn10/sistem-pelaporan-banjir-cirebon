<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit();
}

include '../config/database.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
    $status = isset($_POST['status']) ? $_POST['status'] : '';
    
    // Validasi status
    $allowed_status = ['Diterima', 'Ditindaklanjuti', 'Dikerjakan', 'Selesai'];
    if (!in_array($status, $allowed_status)) {
        echo json_encode(['success' => false, 'message' => 'Status tidak valid']);
        exit();
    }
    
    try {
        $stmt = $pdo->prepare("UPDATE reports SET status = ? WHERE id = ?");
        if ($stmt->execute([$status, $id])) {
            echo json_encode(['success' => true, 'message' => 'Status berhasil diupdate']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Gagal update status']);
        }
    } catch (PDOException $e) {
        echo json_encode(['success' => false, 'message' => 'Database error']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method']);
}
?>