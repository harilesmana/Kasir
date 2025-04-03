<?php
$pageTitle = "Tambah Kasir";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth_check.php';

// Pastikan hanya admin yang bisa akses
if ($_SESSION['role'] !== 'admin') {
    header("Location: /index.php?error=unauthorized");
    exit();
}

// Proses form tambah kasir
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    
    // Validasi
    if ($password !== $confirm_password) {
        $error = "Password tidak cocok!";
    } else {
        try {
            $db = getDB();
            
            // Cek username sudah ada atau belum
            $stmt = $db->prepare("SELECT id FROM kasir WHERE username = :username");
            $stmt->bindParam(':username', $username);
            $stmt->execute();
            
            if ($stmt->rowCount() > 0) {
                $error = "Username sudah digunakan!";
            } else {
                // Hash password
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                
                // Simpan ke database
                $stmt = $db->prepare("INSERT INTO kasir (nama, username, password, email, role) 
                                     VALUES (:nama, :username, :password, :email, :role)");
                $stmt->execute([
                    ':nama' => $nama,
                    ':username' => $username,
                    ':password' => $hashed_password,
                    ':email' => $email,
                    ':role' => $role
                ]);
                
                header("Location: kasir.php?success=added");
                exit();
            }
        } catch(PDOException $e) {
            $error = "Gagal menambahkan kasir: " . $e->getMessage();
        }
    }
}
?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-person-plus me-2"></i>Tambah Kasir</h5>
    </div>
    <div class="card-body">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label for="nama" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="nama" name="nama" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email">
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-select" id="role" name="role" required>
                    <option value="kasir">Kasir</option>
                    <option value="admin">Admin</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password</label>
                <input type="password" class="form-control" id="password" name="password" required>
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Konfirmasi Password</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password" required>
            </div>
            <div class="d-flex justify-content-between">
                <a href="kasir.php" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>