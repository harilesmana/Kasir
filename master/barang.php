<?php
$pageTitle = "Master Data Barang";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth_check.php';

// Pastikan hanya admin yang bisa akses
if ($_SESSION['role'] !== 'admin') {
    header("Location: /index.php?error=unauthorized");
    exit();
}

// Ambil data barang dari database
try {
    $db = getDB();
    $stmt = $db->query("SELECT * FROM barang ORDER BY nama ASC");
    $barang = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch(PDOException $e) {
    $error = "Database error: " . $e->getMessage();
}
?>

<div class="card">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0"><i class="bi bi-box me-2"></i>Data Barang</h5>
        <div>
            <button class="btn btn-sm btn-outline-secondary me-2" id="btnPrint">
                <i class="bi bi-printer"></i> Cetak
            </button>
            <a href="tambah_barang.php" class="btn btn-sm btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Barang
            </a>
        </div>
    </div>
    <div class="card-body">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <div class="mb-3">
            <div class="row">
                <div class="col-md-6">
                    <div class="input-group">
                        <input type="text" class="form-control" id="searchInput" placeholder="Cari barang...">
                        <button class="btn btn-outline-secondary" type="button" id="btnSearch">
                            <i class="bi bi-search"></i>
                        </button>
                    </div>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="categoryFilter">
                        <option value="">Semua Kategori</option>
                        <option>Elektronik</option>
                        <option>Peralatan</option>
                        <option>Bahan</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select class="form-select" id="sortBy">
                        <option value="nama_asc">Nama A-Z</option>
                        <option value="nama_desc">Nama Z-A</option>
                        <option value="stok_asc">Stok Terkecil</option>
                        <option value="stok_desc">Stok Terbanyak</option>
                        <option value="harga_asc">Harga Terendah</option>
                        <option value="harga_desc">Harga Tertinggi</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="table-responsive">
            <table class="table table-striped table-hover table-bordered" id="tabelBarang">
                <thead class="table-dark">
                    <tr>
                        <th width="50">#</th>
                        <th>Kode</th>
                        <th>Nama Barang</th>
                        <th>Kategori</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th width="120">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($barang as $index => $item): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= htmlspecialchars($item['kode']) ?></td>
                        <td><?= htmlspecialchars($item['nama']) ?></td>
                        <td><?= htmlspecialchars($item['kategori']) ?></td>
                        <td>Rp <?= number_format($item['harga'], 0, ',', '.') ?></td>
                        <td><?= $item['stok'] ?></td>
                        <td class="text-center">
                            <a href="edit_barang.php?id=<?= $item['id'] ?>" class="btn btn-sm btn-warning" title="Edit">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <button class="btn btn-sm btn-danger" title="Hapus" onclick="confirmDelete(<?= $item['id'] ?>)">
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
    if (confirm('Apakah Anda yakin ingin menghapus barang ini?')) {
        window.location.href = 'hapus_barang.php?id=' + id;
    }
}

$(document).ready(function() {
    // Fungsi filter dan pencarian
    function filterBarang() {
        const search = $('#searchInput').val().toLowerCase();
        const category = $('#categoryFilter').val();
        const sortBy = $('#sortBy').val();
        
        // Implementasi filter dan sorting
        // ... (bisa ditambahkan dengan AJAX atau client-side filtering)
    }
    
    $('#searchInput, #categoryFilter, #sortBy').on('change keyup', filterBarang);
    
    // Cetak data
    $('#btnPrint').click(function() {
        window.print();
    });
});
</script>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>