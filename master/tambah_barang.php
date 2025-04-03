<?php
$pageTitle = "Tambah Barang";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth_check.php';

// Pastikan hanya admin yang bisa akses
if ($_SESSION['role'] !== 'admin') {
    header("Location: /index.php?error=unauthorized");
    exit();
}

// Proses form tambah barang
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode = trim($_POST['kode']);
    $nama = trim($_POST['nama']);
    $kategori = trim($_POST['kategori']);
    $harga = (int) str_replace('.', '', $_POST['harga']);
    $stok = (int) $_POST['stok'];
    
    try {
        $db = getDB();
        $stmt = $db->prepare("INSERT INTO barang (kode, nama, kategori, harga, stok) 
                             VALUES (:kode, :nama, :kategori, :harga, :stok)");
        $stmt->execute([
            ':kode' => $kode,
            ':nama' => $nama,
            ':kategori' => $kategori,
            ':harga' => $harga,
            ':stok' => $stok
        ]);
        
        $_SESSION['success_message'] = 'Barang berhasil ditambahkan!';
        header("Location: barang.php");
        exit();
    } catch(PDOException $e) {
        $error = "Gagal menambahkan barang: " . $e->getMessage();
    }
}
?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-plus-circle me-2"></i>Tambah Barang</h5>
    </div>
    <div class="card-body">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label for="kode" class="form-label">Kode Barang</label>
                <input type="text" class="form-control" id="kode" name="kode" required>
            </div>
            <div class="mb-3">
                <label for="nama" class="form-label">Nama Barang</label>
                <input type="text" class="form-control" id="nama" name="nama" required>
            </div>
            <div class="mb-3">
                <label for="kategori" class="form-label">Kategori</label>
                <select class="form-select" id="kategori" name="kategori" required>
                    <option value="">Pilih Kategori</option>
                    <option value="Elektronik">Elektronik</option>
                    <option value="Peralatan">Peralatan</option>
                    <option value="Bahan">Bahan</option>
                    <option value="Lainnya">Lainnya</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="harga" class="form-label">Harga</label>
                <div class="input-group">
                    <span class="input-group-text">Rp</span>
                    <input type="text" class="form-control" id="harga" name="harga" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="stok" class="form-label">Stok</label>
                <input type="number" class="form-control" id="stok" name="stok" required min="0">
            </div>
            <div class="d-flex justify-content-between">
                <a href="barang.php" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    // Format input harga
    $('#harga').on('keyup', function() {
        let value = $(this).val().replace(/\./g, '');
        if (!isNaN(value)) {
            $(this).val(value.replace(/\B(?=(\d{3})+(?!\d))/g, "."));
        }
    });
});
</script>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>