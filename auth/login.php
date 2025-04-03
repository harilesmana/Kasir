<?php
require_once __DIR__ . '/../config/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: ../index.php");
    exit();
}

$username = trim($_POST['username'] ?? '');
$password = trim($_POST['password'] ?? '');
$remember = isset($_POST['remember']);

// Validasi input
if (empty($username) || empty($password)) {
    header("Location: ../index.php?error=empty_fields");
    exit();
}

try {
    $db = getDB();
    
    // Cari user di database
    $stmt = $db->prepare("SELECT id, username, password, role FROM kasir WHERE username = :username");
    $stmt->bindParam(':username', $username);
    $stmt->execute();

    if ($stmt->rowCount() === 1) {
        $user = $stmt->fetch();
        
        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            
            // Remember me
            if ($remember) {
                $token = bin2hex(random_bytes(32));
                $expiry = time() + 60 * 60 * 24 * 30; // 30 hari
                
                setcookie('remember_token', $token, $expiry, '/');
                
                // Simpan token di database
                $stmt = $db->prepare("UPDATE kasir SET remember_token = :token WHERE id = :id");
                $stmt->bindParam(':token', $token);
                $stmt->bindParam(':id', $user['id']);
                $stmt->execute();
            }
            
            // Regenerate session ID untuk mencegah session fixation
            session_regenerate_id(true);
            
            // Redirect ke halaman yang diminta sebelumnya atau dashboard
            $redirect_url = $_SESSION['redirect_url'] ?? 'dashboard.php';
            unset($_SESSION['redirect_url']);
            header("Location: $redirect_url");
            exit();
        }
    }
    
    // Jika autentikasi gagal
    header("Location: ../index.php?error=invalid_credentials");
    exit();
    
} catch(PDOException $e) {
    error_log("Authentication error: " . $e->getMessage());
    header("Location: ../index.php?error=db_error");
    exit();
}
?>