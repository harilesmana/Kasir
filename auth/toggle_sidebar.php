<?php
session_start();

// Toggle status sidebar collapsed
$_SESSION['sidebar_collapsed'] = !isset($_SESSION['sidebar_collapsed']) || !$_SESSION['sidebar_collapsed'];

// Kembalikan response JSON
header('Content-Type: application/json');
echo json_encode(['status' => 'success', 'collapsed' => $_SESSION['sidebar_collapsed']]);
exit();
?>