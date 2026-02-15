<?php
/**
 * Kontrola, zda byl pro rezervaci již odeslán potvrzující e-mail (admin)
 * GET ?id=123
 */
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

if (!isset($_SESSION['walance_admin'])) {
    http_response_code(403);
    echo json_encode(['error' => 'Unauthorized']);
    exit;
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    echo json_encode(['already_sent' => false]);
    exit;
}

try {
    $db = getDb();
    // Načíst bookings_id, email a contacts_id rezervace
    $stmt = $db->prepare("SELECT bookings_id, contacts_id, email FROM bookings WHERE id = ? AND valid_to IS NULL");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$row) {
        echo json_encode(['already_sent' => false]);
        exit;
    }
    $bookingsId = $row['bookings_id'] ?? $id;

    // Hledat aktivitu typu booking_confirmation pro tuto rezervaci (bookings_id = entity id)
    $stmt = $db->prepare("
        SELECT valid_from FROM activities
        WHERE type = 'booking_confirmation' AND bookings_id = ? AND valid_to IS NULL
        ORDER BY valid_from DESC LIMIT 1
    ");
    $stmt->execute([$bookingsId]);
    $activity = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($activity) {
        echo json_encode([
            'already_sent' => true,
            'sent_at' => $activity['valid_from'],
            'email' => $row['email'],
        ]);
    } else {
        echo json_encode(['already_sent' => false]);
    }
} catch (Exception $e) {
    echo json_encode(['already_sent' => false]);
}
