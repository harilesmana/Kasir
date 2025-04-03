<?php
$pageTitle = "Edit Kasir";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth_check.php';

// Pastikan hanya admin yang bisa akses
if ($_SESSION['role'] !== 'admin') {
    header("Location: /index.php?error=unauthorized");
    exit();
}

$kasir_id = $_GET['id'] ?? 0;

// Ambil data kasir dari database
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM kasir WHERE id = :id");
    $stmt->bindParam(':id', $kasir_id);
    $stmt->execute();
    $kasir = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$kasir) {
        header("Location: kasir.php");
        exit();
    }
} catch(PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}

// Proses form update kasir
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = trim($_POST['nama']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $role = $_POST['role'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    try {
        // Cek username sudah ada atau belum (kecuali untuk user ini)
        $stmt = $db->prepare("SELECT id FROM kasir WHERE username = :username AND id != :id");
        $stmt->bindParam(':username', $username);
        $stmt->bindParam(':id', $kasir_id);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $error = "Username sudah digunakan!";
        } else {
            // Jika password diisi, update password juga
            if (!empty($password)) {
                if ($password !== $confirm_password) {
                    $error = "Password tidak cocok!";
                } else {
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    
                    $stmt = $db->prepare("UPDATE kasir SET 
                                        nama = :nama, 
                                        username = :username, 
                                        email = :email, 
                                        role = :role,
                                        password = :password
                                        WHERE id = :id");
                    $stmt->bindParam(':password', $hashed_password);
                }
            } else {
                $stmt = $db->prepare("UPDATE kasir SET 
                                    nama = :nama, 
                                    username = :username, 
                                    email = :email, 
                                    role = :role
                                    WHERE id = :id");
            }
            
            $stmt->bindParam(':nama', $nama);
            $stmt->bindParam(':username', $username);
            $stmt->bindParam(':email', $email);
            $stmt->bindParam(':role', $role);
            $stmt->bindParam(':id', $kasir_id);
            $stmt->execute();
            
            header("Location: kasir.php?success=updated");
            exit();
        }
    } catch(PDOException $e) {
        $error = "Gagal memperbarui kasir: " . $e->getMessage();
    }
}
?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-pencil-square me-2"></i>Edit Kasir</h5>
    </div>
    <div class="card-body">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label for="nama" class="form-label">Nama Lengkap</label>
                <input type="text" class="form-control" id="nama" name="nama" 
                       value="<?= htmlspecialchars($kasir['nama']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="username" class="form-label">Username</label>
                <input type="text" class="form-control" id="username" name="username" 
                       value="<?= htmlspecialchars($kasir['username']) ?>" required>
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" 
                       value="<?= htmlspecialchars($kasir['email'] ?? '') ?>">
            </div>
            <div class="mb-3">
                <label for="role" class="form-label">Role</label>
                <select class="form-select" id="role" name="role" required>
                    <option value="kasir" <?= $kasir['role'] === 'kasir' ? 'selected' : '' ?>>Kasir</option>
                    <option value="admin" <?= $kasir['role'] === 'admin' ? 'selected' : '' ?>>Admin</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Password Baru (kosongkan jika tidak ingin mengubah)</label>
                <input type="password" class="form-control" id="password" name="password">
            </div>
            <div class="mb-3">
                <label for="confirm_password" class="form-label">Konfirmasi Password Baru</label>
                <input type="password" class="form-control" id="confirm_password" name="confirm_password">
            </div>
            <div class="d-flex justify-content-between">
                <a href="kasir.php" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </div>
        </form>
    </div>
</div>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>