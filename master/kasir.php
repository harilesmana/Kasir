<?php
$pageTitle = "Data Kasir";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth_check.php';

// Pastikan hanya admin yang bisa akses
if ($_SESSION['role'] !== 'admin') {
    header("Location: /index.php?error=unauthorized");
    exit();
}

// Ambil data kasir dari database
try {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM kasir ORDER BY nama ASC");
    $kasir = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-person-badge me-2"></i>Data Kasir</h5>
        <a href="tambah_kasir.php" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Kasir
        </a>
    </div>
    <div class="card-body">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <?php if (isset($_GET['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show">
                <?php
                $messages = [
                    'added' => 'Kasir berhasil ditambahkan!',
                    'updated' => 'Data kasir berhasil diperbarui!',
                    'deleted' => 'Kasir berhasil dihapus!'
                ];
                echo $messages[$_GET['success']] ?? 'Operasi berhasil!';
                ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Tanggal Dibuat</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($kasir as $index => $k): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($k['nama']) ?></td>
                        <td><?= htmlspecialchars($k['username']) ?></td>
                        <td><?= htmlspecialchars($k['email'] ?? '-') ?></td>
                        <td>
                            <span class="badge bg-<?= $k['role'] === 'admin' ? 'primary' : 'success' ?>">
                                <?= ucfirst($k['role']) ?>
                            </span>
                        </td>
                        <td><?= date('d/m/Y', strtotime($k['created_at'])) ?></td>
                        <td>
                            <a href="edit_kasir.php?id=<?= $k['id'] ?>" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <?php if ($k['id'] != $_SESSION['user_id']): ?>
                            <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $k['id'] ?>)">
                                <i class="bi bi-trash"></i>
                            </button>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script>
function confirmDelete(id) {
    if (confirm('Apakah Anda yakin ingin menghapus kasir ini?')) {
        window.location.href = 'hapus_kasir.php?id=' + id;
    }
}
</script>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>