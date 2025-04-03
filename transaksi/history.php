<?php
$pageTitle = "History Transaksi";
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/auth_check.php';

// Ambil data transaksi dari database
try {
    $db = getDB();
    
    // Filter tanggal jika ada
    $filter = [];
    $params = [];
    
    if (isset($_GET['start_date']) && !empty($_GET['start_date'])) {
        $filter[] = "t.created_at >= :start_date";
        $params[':start_date'] = $_GET['start_date'] . ' 00:00:00';
    }
    
    if (isset($_GET['end_date']) && !empty($_GET['end_date'])) {
        $filter[] = "t.created_at <= :end_date";
        $params[':end_date'] = $_GET['end_date'] . ' 23:59:59';
    }
    
    // Jika bukan admin, hanya tampilkan transaksi kasir tersebut
    if ($_SESSION['role'] !== 'admin') {
        $filter[] = "t.kasir_id = :kasir_id";
        $params[':kasir_id'] = $_SESSION['user_id'];
    }
    
    $whereClause = $filter ? "WHERE " . implode(" AND ", $filter) : "";
    
    $query = "SELECT t.*, k.nama as kasir FROM transaksi t 
              JOIN kasir k ON t.kasir_id = k.id 
              $whereClause
              ORDER BY t.created_at DESC";
    
    $stmt = $db->prepare($query);
    $stmt->execute($params);
    $transaksi = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-clock-history me-2"></i>History Transaksi</h5>
        <div>
            <button class="btn btn-sm btn-outline-secondary me-2" id="btnPrint">
                <i class="bi bi-printer"></i> Cetak
            </button>
            <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#filterModal">
                <i class="bi bi-funnel"></i> Filter
            </button>
        </div>
    </div>
    <div class="card-body">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <!-- Modal Filter -->
        <div class="modal fade" id="filterModal" tabindex="-1" aria-labelledby="filterModalLabel" aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title" id="filterModalLabel">Filter Transaksi</h5>
                        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                    </div>
                    <form method="GET">
                        <div class="modal-body">
                            <div class="mb-3">
                                <label class="form-label">Tanggal Mulai</label>
                                <input type="date" class="form-control" name="start_date" value="<?= $_GET['start_date'] ?? '' ?>">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Tanggal Akhir</label>
                                <input type="date" class="form-control" name="end_date" value="<?= $_GET['end_date'] ?? '' ?>">
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                            <button type="submit" class="btn btn-primary">Terapkan Filter</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>No. Transaksi</th>
                        <th>Tanggal</th>
                        <th>Pelanggan</th>
                        <th>Kasir</th>
                        <th>Total</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($transaksi as $index => $trx): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= $trx['no_transaksi'] ?></td>
                        <td><?= date('d/m/Y H:i', strtotime($trx['created_at'])) ?></td>
                        <td><?= $trx['pelanggan'] ?: '-' ?></td>
                        <td><?= $trx['kasir'] ?></td>
                        <td>Rp <?= number_format($trx['total_akhir'], 0, ',', '.') ?></td>
                        <td>
                            <a href="cetak_struk.php?id=<?= $trx['id'] ?>" class="btn btn-sm btn-info" target="_blank">
                                <i class="bi bi-eye"></i> Lihat
                            </a>
                            <?php if ($_SESSION['role'] === 'admin'): ?>
                            <button class="btn btn-sm btn-danger" onclick="confirmDelete(<?= $trx['id'] ?>)">
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
    if (confirm('Apakah Anda yakin ingin menghapus transaksi ini?')) {
        window.location.href = 'hapus_transaksi.php?id=' + id;
    }
}

$(document).ready(function() {
    // Cetak data
    $('#btnPrint').click(function() {
        window.print();
    });
});
</script>

<?php
require_once __DIR__ . '/../../includes/footer.php';
?>