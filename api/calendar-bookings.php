<?php
/**
 * Rezervace pro kalendář – vrací booking ID a údaje pro sloty (admin)
 * GET ?month=YYYY-MM
 */
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

if (!isset($_SESSION['walance_admin'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

$month = $_GET['month'] ?? '';
if (!preg_match('/^\d{4}-\d{2}$/', $month)) {
    echo json_encode([]);
    exit;
}

try {
    $db = getDb();
    $stmt = $db->prepare("
        SELECT id, booking_date, booking_time, name, email, status
        FROM bookings
        WHERE valid_to IS NULL
        AND booking_date >= ? AND booking_date <= ?
        AND status IN ('pending', 'confirmed', 'cancelled')
        ORDER BY booking_date, booking_time
    ");
    $first = $month . '-01';
    $last = date('Y-m-t', strtotime($first));
    $stmt->execute([$first, $last]);
    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    echo json_encode([]);
    exit;
}

$byDate = [];
foreach ($rows as $r) {
    $d = $r['booking_date'];
    $t = $r['booking_time'];
    if (!isset($byDate[$d])) $byDate[$d] = [];
    $item = [
        'id' => (int) $r['id'],
        'name' => $r['name'],
        'email' => $r['email'],
        'status' => $r['status'],
    ];
    if (!isset($byDate[$d][$t])) {
        $byDate[$d][$t] = [];
    }
    $byDate[$d][$t][] = $item;
}

echo json_encode($byDate);
