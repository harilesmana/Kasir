<?php
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/auth_check.php';

header('Content-Type: application/json');

try {
    $db = getDB();
    
    $kode = $_POST['kode'] ?? '';
    $today = date('Y-m-d');
    
    // Cek diskon di database
    $stmt = $db->prepare("SELECT * FROM diskon WHERE kode = :kode 
                         AND berlaku_mulai <= :today 
                         AND berlaku_hingga >= :today");
    $stmt->execute([
        ':kode' => $kode,
        ':today' => $today
    ]);
    
    if ($stmt->rowCount() === 1) {
        $diskon = $stmt->fetch(PDO::FETCH_ASSOC);
        echo json_encode([
            'valid' => true,
            'jenis' => $diskon['jenis'],
            'nilai' => (float)$diskon['nilai']
        ]);
    } else {
        echo json_encode(['valid' => false]);
    }
} catch(PDOException $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Database error: ' . $e->getMessage()]);
}
?>