<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth_check.php';

$transaksi_id = $_GET['id'] ?? 0;

try {
    $db = getDB();
    
    // Ambil data transaksi
    $stmt = $db->prepare("SELECT t.*, k.nama as kasir FROM transaksi t 
                         JOIN kasir k ON t.kasir_id = k.id 
                         WHERE t.id = :id");
    $stmt->execute([':id' => $transaksi_id]);
    $transaksi = $stmt->fetch(PDO::FETCH_ASSOC);
    
    // Ambil detail transaksi
    $stmt = $db->prepare("SELECT td.*, b.nama as barang FROM transaksi_detail td 
                         JOIN barang b ON td.barang_id = b.id 
                         WHERE td.transaksi_id = :id");
    $stmt->execute([':id' => $transaksi_id]);
    $details = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    if (!$transaksi) {
        die('Transaksi tidak ditemukan');
    }
} catch(PDOException $e) {
    die('Database error: ' . $e->getMessage());
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Struk #<?= $transaksi['no_transaksi'] ?></title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; }
        .struk { width: 300px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 10px; }
        .title { font-weight: bold; font-size: 14px; }
        .alamat { font-size: 10px; color: #555; }
        .divider { border-top: 1px dashed #000; margin: 5px 0; }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        table { width: 100%; border-collapse: collapse; }
        td { padding: 3px 0; }
        .footer { margin-top: 10px; font-size: 10px; text-align: center; }
    </style>
</head>
<body>
    <div class="struk">
        <div class="header">
            <div class="title">TOKO ABC</div>
            <div class="alamat">Jl. Contoh No. 123, Kota Contoh</div>
            <div class="alamat">Telp: 021-1234567</div>
        </div>
        
        <div class="divider"></div>
        
        <div>
            <table>
                <tr>
                    <td>No. Transaksi</td>
                    <td class="text-right"><?= $transaksi['no_transaksi'] ?></td>
                </tr>
                <tr>
                    <td>Tanggal</td>
                    <td class="text-right"><?= date('d/m/Y H:i', strtotime($transaksi['created_at'])) ?></td>
                </tr>
                <tr>
                    <td>Kasir</td>
                    <td class="text-right"><?= $transaksi['kasir'] ?></td>
                </tr>
                <tr>
                    <td>Pelanggan</td>
                    <td class="text-right"><?= $transaksi['pelanggan'] ?: '-' ?></td>
                </tr>
            </table>
        </div>
        
        <div class="divider"></div>
        
        <div>
            <table>
                <?php foreach ($details as $item): ?>
                <tr>
                    <td><?= $item['barang'] ?></td>
                    <td class="text-right"><?= $item['qty'] ?> x <?= number_format($item['harga'], 0, ',', '.') ?></td>
                    <td class="text-right"><?= number_format($item['subtotal'], 0, ',', '.') ?></td>
                </tr>
                <?php endforeach; ?>
            </table>
        </div>
        
        <div class="divider"></div>
        
        <div>
            <table>
                <tr>
                    <td>Subtotal</td>
                    <td class="text-right">Rp <?= number_format($transaksi['total'] + $transaksi['diskon'], 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td>Diskon</td>
                    <td class="text-right">Rp <?= number_format($transaksi['diskon'], 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td><strong>Total</strong></td>
                    <td class="text-right"><strong>Rp <?= number_format($transaksi['total_akhir'], 0, ',', '.') ?></strong></td>
                </tr>
                <tr>
                    <td>Tunai</td>
                    <td class="text-right">Rp <?= number_format($transaksi['tunai'], 0, ',', '.') ?></td>
                </tr>
                <tr>
                    <td>Kembalian</td>
                    <td class="text-right">Rp <?= number_format($transaksi['kembalian'], 0, ',', '.') ?></td>
                </tr>
            </table>
        </div>
        
        <div class="divider"></div>
        
        <div class="footer">
            Terima kasih telah berbelanja<br>
            Barang yang sudah dibeli tidak dapat ditukar atau dikembalikan
        </div>
    </div>
    
    <script>
        // Cetak otomatis
        window.onload = function() {
            setTimeout(function() {
                window.print();
                // window.close(); // Opsional: tutup window setelah cetak
            }, 500);
        };
    </script>
</body>
</html>