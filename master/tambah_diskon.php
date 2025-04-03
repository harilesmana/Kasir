<?php
$pageTitle = "Tambah Diskon";
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/auth_check.php';

// Pastikan hanya admin yang bisa akses
if ($_SESSION['role'] !== 'admin') {
    header("Location: /index.php?error=unauthorized");
    exit();
}

// Proses form tambah diskon
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $kode = trim($_POST['kode']);
    $nama = trim($_POST['nama']);
    $jenis = $_POST['jenis'];
    $nilai = (float) str_replace('.', '', $_POST['nilai']);
    $berlaku_mulai = $_POST['berlaku_mulai'];
    $berlaku_hingga = $_POST['berlaku_hingga'];
    
    try {
        $db = getDB();
        
        // Cek kode diskon sudah ada atau belum
        $stmt = $db->prepare("SELECT id FROM diskon WHERE kode = :kode");
        $stmt->bindParam(':kode', $kode);
        $stmt->execute();
        
        if ($stmt->rowCount() > 0) {
            $error = "Kode diskon sudah digunakan!";
        } else {
            // Simpan ke database
            $stmt = $db->prepare("INSERT INTO diskon (kode, nama, jenis, nilai, berlaku_mulai, berlaku_hingga) 
                                 VALUES (:kode, :nama, :jenis, :nilai, :berlaku_mulai, :berlaku_hingga)");
            $stmt->execute([
                ':kode' => $kode,
                ':nama' => $nama,
                ':jenis' => $jenis,
                ':nilai' => $nilai,
                ':berlaku_mulai' => $berlaku_mulai,
                ':berlaku_hingga' => $berlaku_hingga
            ]);
            
            header("Location: diskon.php?success=added");
            exit();
        }
    } catch(PDOException $e) {
        $error = "Gagal menambahkan diskon: " . $e->getMessage();
    }
}
?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0"><i class="bi bi-percent me-2"></i>Tambah Diskon</h5>
    </div>
    <div class="card-body">
        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= $error ?></div>
        <?php endif; ?>
        
        <form method="POST">
            <div class="mb-3">
                <label for="kode" class="form-label">Kode Diskon</label>
                <input type="text" class="form-control" id="kode" name="kode" required>
                <small class="text-muted">Kode unik untuk diskon (misal: DISKON10)</small>
            </div>
            <div class="mb-3">
                <label for="nama" class="form-label">Nama Diskon</label>
                <input type="text" class="form-control" id="nama" name="nama" required>
                <small class="text-muted">Deskripsi singkat diskon (misal: Diskon Hari Kemerdekaan)</small>
            </div>
            <div class="mb-3">
                <label for="jenis" class="form-label">Jenis Diskon</label>
                <select class="form-select" id="jenis" name="jenis" required>
                    <option value="persen">Persentase (%)</option>
                    <option value="nominal">Nominal (Rp)</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="nilai" class="form-label">Nilai Diskon</label>
                <div class="input-group">
                    <span class="input-group-text" id="nilai-addon">
                        <span id="jenis-label">%</span>
                    </span>
                    <input type="text" class="form-control" id="nilai" name="nilai" required>
                </div>
            </div>
            <div class="mb-3">
                <label for="berlaku_mulai" class="form-label">Berlaku Mulai</label>
                <input type="date" class="form-control" id="berlaku_mulai" name="berlaku_mulai" required>
            </div>
            <div class="mb-3">
                <label for="berlaku_hingga" class="form-label">Berlaku Hingga</label>
                <input type="date" class="form-control" id="berlaku_hingga" name="berlaku_hingga" required>
            </div>
            <div class="d-flex justify-content-between">
                <a href="diskon.php" class="btn btn-secondary">Kembali</a>
                <button type="submit" class="btn btn-primary">Simpan</button>
            </div>
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    // Update label jenis diskon
    $('#jenis').change(function() {
        const jenis = $(this).val();
        $('#jenis-label').text(jenis === 'persen' ? '%' : 'Rp');
        
        // Format input nilai
        if (jenis === 'nominal') {
            $('#nilai').on('keyup', function() {
                let value = $(this).val().replace(/\./g, '');
                if (!isNaN(value)) {
                    $(this).val(value.replace(/\B(?=(\d{3})+(?!\d))/g, "."));
                }
            });
        } else {
            $('#nilai').off('keyup');
        }
    });
    
    // Set default tanggal
    const today = new Date().toISOString().split('T')[0];
    $('#berlaku_mulai').val(today);
    
    // Set tanggal akhir default (7 hari dari sekarang)
    const nextWeek = new Date();
    nextWeek.setDate(nextWeek.getDate() + 7);
    $('#berlaku_hingga').val(nextWeek.toISOString().split('T')[0]);
});
</script>

<?php
require_once __DIR__ . '/../includes/footer.php';
?>