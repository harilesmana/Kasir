<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth_check.php';

header('Content-Type: application/json');

// Pastikan hanya kasir yang bisa akses
if ($_SESSION['role'] !== 'kasir' && $_SESSION['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

try {
    $db = getDB();
    
    $data = json_decode(file_get_contents('php://input'), true);
    
    // Mulai transaksi
    $db->beginTransaction();
    
    // Generate nomor transaksi
    $no_transaksi = 'TRX-' . date('Ymd') . '-' . str_pad(mt_rand(1, 9999), 4, '0', STR_PAD_LEFT);
    
    // Simpan transaksi
    $stmt = $db->prepare("INSERT INTO transaksi (no_transaksi, kasir_id, pelanggan, total, diskon, total_akhir, tunai, kembalian) 
                         VALUES (:no_transaksi, :kasir_id, :pelanggan, :total, :diskon, :total_akhir, :tunai, :kembalian)");
    
    $stmt->execute([
        ':no_transaksi' => $no_transaksi,
        ':kasir_id' => $_SESSION['user_id'],
        ':pelanggan' => $data['pelanggan'],
        ':total' => $data['subtotal'],
        ':diskon' => $data['diskon'],
        ':total_akhir' => $data['total'],
        ':tunai' => $data['tunai'],
        ':kembalian' => $data['kembalian']
    ]);
    
    $transaksi_id = $db->lastInsertId();
    
    // Simpan detail transaksi
    $stmt = $db->prepare("INSERT INTO transaksi_detail (transaksi_id, barang_id, harga, qty, subtotal) 
                         VALUES (:transaksi_id, :barang_id, :harga, :qty, :subtotal)");
    
    foreach ($data['items'] as $item) {
        $stmt->execute([
            ':transaksi_id' => $transaksi_id,
            ':barang_id' => $item['id'],
            ':harga' => $item['harga'],
            ':qty' => $item['qty'],
            ':subtotal' => $item['subtotal']
        ]);
        
        // Update stok barang
        $db->exec("UPDATE barang SET stok = stok - {$item['qty']} WHERE id = {$item['id']}");
    }
    
    // Commit transaksi
    $db->commit();
    
    echo json_encode(['success' => true, 'transaksi_id' => $transaksi_id]);
} catch(PDOException $e) {
    $db->rollBack();
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Database error: ' . $e->getMessage()]);
}
?>