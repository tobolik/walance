<?php
/**
 * Úprava data a času rezervace (admin)
 * POST: id, date (YYYY-MM-DD), time (HH:MM)
 */
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
header('Pragma: no-cache');

if (!isset($_SESSION['walance_admin'])) {
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Přihlášení vyžadováno']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Metoda není povolena']);
    exit;
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/crud.php';
require_once __DIR__ . '/availability.php';

$id = (int)($_POST['id'] ?? 0);
$date = trim($_POST['date'] ?? '');
$time = trim($_POST['time'] ?? '');

if (!$id || empty($date) || empty($time)) {
    echo json_encode(['success' => false, 'error' => 'Vyplňte datum a čas.']);
    exit;
}

$bookingDate = DateTime::createFromFormat('Y-m-d', $date);
if (!$bookingDate) {
    echo json_encode(['success' => false, 'error' => 'Neplatné datum (očekáváno YYYY-MM-DD).']);
    exit;
}

if (!preg_match('/^\d{2}:\d{2}$/', $time)) {
    echo json_encode(['success' => false, 'error' => 'Neplatný čas (očekáváno HH:MM).']);
    exit;
}

$settings = getAvailabilitySettings();
$allSlots = buildSlotsFromRanges($settings, (int)($settings['slot_interval'] ?? 30));
if (!in_array($time, $allSlots)) {
    echo json_encode(['success' => false, 'error' => 'Čas není v povolených slotech.']);
    exit;
}

try {
    $row = findActive('bookings', $id);
    if (!$row) {
        echo json_encode(['success' => false, 'error' => 'Rezervace nenalezena.']);
        exit;
    }

    $googleEventId = $row['google_event_id'] ?? null;
    if (GOOGLE_CALENDAR_ENABLED && $googleEventId && file_exists(__DIR__ . '/GoogleCalendar.php')) {
        require_once __DIR__ . '/GoogleCalendar.php';
        $calendarIds = getCalendarIds();
        $gcCalendarId = $calendarIds[0] ?? null;
        if ($gcCalendarId) {
            try {
                $gc = new GoogleCalendar($gcCalendarId);
                $gc->updateEvent($googleEventId, $date, $time);
            } catch (Exception $e) {
                // Pokračuj – DB se aktualizuje i při chybě GC
            }
        }
    }

    softUpdate('bookings', $id, [
        'booking_date' => $date,
        'booking_time' => $time,
    ]);

    echo json_encode(['success' => true, 'date' => $date, 'time' => $time]);
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Nepodařilo se uložit změny.']);
}
