<?php
// Pastikan file ini dipanggil setelah session_start()
if (!isset($_SESSION['user_id'])) {
    // Cek remember me cookie
    if (isset($_COOKIE['remember_token'])) {
        require_once __DIR__ . '/../config/db.php';
        
        try {
            $db = getDB();
            $stmt = $db->prepare("SELECT id, username, role FROM kasir WHERE remember_token = :token");
            $stmt->bindParam(':token', $_COOKIE['remember_token']);
            $stmt->execute();
            
            if ($stmt->rowCount() === 1) {
                $user = $stmt->fetch();
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['role'] = $user['role'];
                session_regenerate_id(true);
                return; // Lanjutkan eksekusi
            }
        } catch(PDOException $e) {
            error_log("Remember me error: " . $e->getMessage());
        }
    }
    
    $_SESSION['redirect_url'] = $_SERVER['REQUEST_URI'];
    header("Location: /index.php?error=unauthorized");
    exit();
}
?>