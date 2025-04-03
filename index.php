<?php
session_start();

// Jika user sudah login, redirect ke dashboard
if (isset($_SESSION['user_id'])) {
    header("Location: dashboard.php");
    exit();
}

$pageTitle = "Login";
require_once __DIR__ . '/includes/header.php';
?>

<div class="login-container">
    <h1 class="login-title">Login</h1>
    
    <?php if (isset($_GET['error'])): ?>
        <div class="alert alert-danger alert-dismissible fade show">
            <?php
            $errors = [
                'empty_fields' => 'Username dan password harus diisi!',
                'invalid_credentials' => 'Username atau password salah!',
                'db_error' => 'Terjadi kesalahan sistem. Silakan coba lagi nanti.',
                'unauthorized' => 'Silakan login terlebih dahulu!'
            ];
            echo $errors[$_GET['error']] ?? 'Terjadi kesalahan saat login.';
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <?php if (isset($_GET['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show">
            <?php
            $successMessages = [
                'registered' => 'Registrasi berhasil! Silakan login.',
                'logout' => 'Anda telah logout.'
            ];
            echo $successMessages[$_GET['success']] ?? 'Operasi berhasil!';
            ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>
    
    <form action="auth/login.php" method="POST">
        <div class="form-group">
            <label for="username" class="form-label">Username</label>
            <input type="text" class="form-control" id="username" name="username" required>
        </div>
        <div class="form-group">
            <label for="password" class="form-label">Password</label>
            <input type="password" class="form-control" id="password" name="password" required>
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" id="remember" name="remember">
            <label class="form-check-label" for="remember">Remember me</label>
        </div>
        <button type="submit" class="btn btn-primary btn-login mt-3">Login</button>
    </form>
    
    <div class="text-center mt-3">
        <a href="register.php">Belum punya akun? Daftar disini</a>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>