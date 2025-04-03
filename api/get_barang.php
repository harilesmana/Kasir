<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth_check.php';

header('Content-Type: application/json');

try {
    $db = getDB();
    
    $search = $_GET['search'] ?? '';
    
    $query = "SELECT id, kode, nama, harga, stok FROM barang WHERE stok > 0";
    
    if (!empty($search)) {
        $query .= " AND (kode LIKE :search OR nama LIKE :search)";
        $searchTerm = "%$search%";
    }
    
    $stmt = $db->prepare($query);
    
    if (!empty($search)) {
        $stmt->bindParam(':search', $searchTerm);
    }
    
    $stmt->execute();
    $barang = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    echo json_encode($barang);
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>