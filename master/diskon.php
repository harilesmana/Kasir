<?php
$pageTitle = "Data Diskon";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth_check.php';

// Pastikan hanya admin yang bisa akses
if ($_SESSION['role'] !== 'admin') {
    header("Location: /index.php?error=unauthorized");
    exit();
}

// Ambil data diskon dari database
try {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM diskon ORDER BY berlaku_hingga DESC");
    $diskon = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-percent me-2"></i>Data Diskon</h5>
        <a href="tambah_diskon.php" class="btn btn-sm btn-primary">
            <i class="bi bi-plus-circle"></i> Tambah Diskon
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
                    'added' => 'Diskon berhasil ditambahkan!',
                    'updated' => 'Data diskon berhasil diperbarui!',
                    'deleted' => 'Diskon berhasil dihapus!'
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
                        <th>Kode</th>
                        <th>Nama</th>
                        <th>Jenis</th>
                        <th>Nilai</th>
                        <th>Berlaku</th>
                        <th>Status</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($diskon as $index => $d): 
                        $today = date('Y-m-d');
                        $status = ($today >= $d['berlaku_mulai'] && $today <= $d['berlaku_hingga']) ? 'Aktif' : 'Kadaluarsa';
                        $badge_class = $status === 'Aktif' ? 'bg-success' : 'bg-secondary';
                    ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($d['kode']) ?></td>
                        <td><?= htmlspecialchars($d['nama']) ?></td>
                        <td><?= ucfirst($d['jenis']) ?></td>
                        <td>
                            <?= $d['jenis'] === 'persen' ? 
                                $d['nilai'] . '%' : 'Rp ' . number_format($d['nilai'], 0, ',', '.') ?>
                        </td>
                        <td>
                            <?= date('d/m/Y', strtotime($d['berlaku_mulai'])) ?> - 
                            <?= date('d/m/Y', strtotime($d['berlaku_hingga'])) ?>
                        </td>
                        <td>
                            <span class="badge <?= $badge_class ?>"><?= $status ?></span>
                        </td>
                        <td>
                            <a href="edit_diskon.php?id=<?= $d['id'] ?>" class="btn btn-sm btn-warning">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $d['id'] ?>)">
                                <i class="bi bi-trash"></i>
                            </button>
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
    if (confirm('Apakah Anda yakin ingin menghapus diskon ini?')) {
        window.location.href = 'hapus_diskon.php?id=' + id;
    }
}
</script>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>