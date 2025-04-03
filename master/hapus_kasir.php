<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth_check.php';

// Pastikan hanya admin yang bisa akses
if ($_SESSION['role'] !== 'admin') {
    header("Location: /index.php?error=unauthorized");
    exit();
}

$kasir_id = $_GET['id'] ?? 0;

// Pastikan tidak bisa menghapus diri sendiri
if ($kasir_id == $_SESSION['user_id']) {
    header("Location: kasir.php?error=cannot_delete_yourself");
    exit();
}

try {
    $db = getDB();
    
    // Hapus kasir dari database
    $stmt = $db->prepare("DELETE FROM kasir WHERE id = :id");
    $stmt->bindParam(':id', $kasir_id);
    $stmt->execute();
    
    header("Location: kasir.php?success=deleted");
    exit();
} catch(PDOException $e) {
    header("Location: kasir.php?error=delete_failed");
    exit();
}
?>