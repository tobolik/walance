<?php
/**
 * Přepnutí blokovaného slotu (přidat/odebrat) – vyžaduje admin session
 * POST: date=YYYY-MM-DD, time=HH:MM
 */
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

if (!isset($_SESSION['walance_admin'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/availability.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$date = trim($_POST['date'] ?? '');
$time = trim($_POST['time'] ?? '');

if (!$date || !$time) {
    echo json_encode(['success' => false, 'error' => 'Missing date or time']);
    exit;
}

$blocked = toggleExcludedSlot($date, $time);
echo json_encode(['success' => true, 'blocked' => $blocked]);
