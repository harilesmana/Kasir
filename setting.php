<?php
$pageTitle = "Pengaturan Sistem";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/auth_check.php';

// Proses update profil
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    
    try {
        $db = getDB();
        $stmt = $db->prepare("UPDATE kasir SET username = :username, email = :email WHERE id = :id");
        $stmt->execute([
            ':username' => $username,
            ':email' => $email,
            ':id' => $_SESSION['user_id']
        ]);
        
        $_SESSION['username'] = $username;
        $success = "Profil berhasil diperbarui!";
    } catch(PDOException $e) {
        $error = "Gagal memperbarui profil: " . $e->getMessage();
    }
}

// Proses ganti password
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['change_password'])) {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    if ($new_password !== $confirm_password) {
        $error = "Password baru tidak cocok!";
    } else {
        try {
            $db = getDB();
            $stmt = $db->prepare("SELECT password FROM kasir WHERE id = :id");
            $stmt->execute([':id' => $_SESSION['user_id']]);
            $user = $stmt->fetch();
            
            if (password_verify($current_password, $user['password'])) {
                $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $db->prepare("UPDATE kasir SET password = :password WHERE id = :id");
                $stmt->execute([
                    ':password' => $hashed_password,
                    ':id' => $_SESSION['user_id']
                ]);
                
                $success = "Password berhasil diubah!";
            } else {
                $error = "Password saat ini salah!";
            }
        } catch(PDOException $e) {
            $error = "Gagal mengubah password: " . $e->getMessage();
        }
    }
}

// Ambil data user
try {
    $db = getDB();
    $stmt = $db->prepare("SELECT * FROM kasir WHERE id = :id");
    $stmt->execute([':id' => $_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<div class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-person-circle me-2"></i>Profil Pengguna</h5>
            </div>
            <div class="card-body text-center">
                <img src="https://via.placeholder.com/150" class="rounded-circle mb-3" alt="Profile">
                <h5><?= htmlspecialchars($user['username']) ?></h5>
                <p class="text-muted"><?= ucfirst($_SESSION['role']) ?></p>
                
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Username</label>
                        <input type="text" class="form-control" name="username" value="<?= htmlspecialchars($user['username']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" class="form-control" name="email" value="<?= htmlspecialchars($user['email'] ?? '') ?>">
                    </div>
                    <button type="submit" name="update_profile" class="btn btn-primary">Update Profil</button>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-md-8">
        <div class="card">
            <div class="card-header">
                <h5><i class="bi bi-shield-lock me-2"></i>Keamanan</h5>
            </div>
            <div class="card-body">
                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?= $success ?></div>
                <?php endif; ?>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="mb-3">
                        <label class="form-label">Password Saat Ini</label>
                        <input type="password" class="form-control" name="current_password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Password Baru</label>
                        <input type="password" class="form-control" name="new_password" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Konfirmasi Password Baru</label>
                        <input type="password" class="form-control" name="confirm_password" required>
                    </div>
                    <button type="submit" name="change_password" class="btn btn-primary">Ganti Password</button>
                </form>
            </div>
        </div>
        
        <?php if ($_SESSION['role'] === 'admin'): ?>
        <div class="card mt-4">
            <div class="card-header">
                <h5><i class="bi bi-gear me-2"></i>Pengaturan Sistem</h5>
            </div>
            <div class="card-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">Nama Aplikasi</label>
                        <input type="text" class="form-control" value="POS System">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Logo</label>
                        <input type="file" class="form-control">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Timezone</label>
                        <select class="form-select">
                            <option>Asia/Jakarta</option>
                            <option>Asia/Makassar</option>
                            <option>Asia/Jayapura</option>
                        </select>
                    </div>
                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="maintenanceMode">
                        <label class="form-check-label" for="maintenanceMode">Maintenance Mode</label>
                    </div>
                    <button type="submit" class="btn btn-primary">Simpan Pengaturan</button>
                </form>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<?php
require_once __DIR__ . '/includes/footer.php';
?>