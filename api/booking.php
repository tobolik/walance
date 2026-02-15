<?php
/**
 * Rezervace termínu - uložení do CRM, Google Calendar, e-mail
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Metoda není povolena']);
    exit;
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/crud.php';

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$name = trim($input['name'] ?? '');
$email = trim($input['email'] ?? '');
$phone = trim($input['phone'] ?? '');
$date = trim($input['date'] ?? '');
$time = trim($input['time'] ?? '');
$message = trim($input['message'] ?? '');

if (empty($name) || empty($email) || empty($date) || empty($time)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Vyplňte prosím všechna povinná pole.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Neplatná e-mailová adresa.']);
    exit;
}

// Validace data
$bookingDate = DateTime::createFromFormat('Y-m-d', $date);
if (!$bookingDate || $bookingDate < new DateTime('today')) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Neplatné datum.']);
    exit;
}

$googleEventId = null;
if (GOOGLE_CALENDAR_ENABLED && file_exists(__DIR__ . '/GoogleCalendar.php')) {
    require_once __DIR__ . '/GoogleCalendar.php';
    try {
        $gc = new GoogleCalendar();
        $googleEventId = $gc->createEvent($date, $time, $name, $email, $message);
    } catch (Exception $e) {
        // Pokračuj i bez Google Calendar
    }
}

try {
    // Kontakt (softInsert)
    $contactId = softInsert('contacts', [
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'message' => $message,
        'source' => 'booking',
    ]);

    // Rezervace (softInsert) – contacts_id = entity_id kontaktu
    softInsert('bookings', [
        'contacts_id' => $contactId,
        'name' => $name,
        'email' => $email,
        'phone' => $phone,
        'booking_date' => $date,
        'booking_time' => $time,
        'message' => $message,
        'status' => 'pending',
        'google_event_id' => $googleEventId,
    ]);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Nepodařilo se uložit rezervaci.']);
    exit;
}

// E-mail notifikace
$to = CONTACT_EMAIL;
$subject = 'Nová rezervace: ' . $name . ' - ' . $date . ' ' . $time;
$body = "Nová rezervace z webu WALANCE\n\n";
$body .= "Jméno: $name\nE-mail: $email\nTelefon: $phone\n";
$body .= "Datum: $date\nČas: $time\n\n";
if ($message) $body .= "Zpráva: $message\n";
@mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, 'Content-Type: text/plain; charset=UTF-8');

echo json_encode([
    'success' => true,
    'message' => 'Rezervace byla odeslána. Brzy vás budeme kontaktovat s potvrzením.'
]);
