<?php
require_once __DIR__ . '/../config/db.php';

session_start();

// Hapus remember token dari database jika ada
if (isset($_COOKIE['remember_token'])) {
    try {
        $db = getDB();
        $stmt = $db->prepare("UPDATE kasir SET remember_token = NULL WHERE remember_token = :token");
        $stmt->bindParam(':token', $_COOKIE['remember_token']);
        $stmt->execute();
    } catch(PDOException $e) {
        error_log("Logout error: " . $e->getMessage());
    }
    
    // Hapus cookie
    setcookie('remember_token', '', time() - 3600, '/');
}

// Hapus semua data session
$_SESSION = array();

// Jika ingin menghapus session cookie
if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(session_name(), '', time() - 42000,
        $params["path"], $params["domain"],
        $params["secure"], $params["httponly"]
    );
}

// Hancurkan session
session_destroy();

// Redirect ke halaman login dengan pesan logout
header("Location: /index.php?success=logout");
exit();
?>