<?php
/**
 * Potvrzení rezervace – změna stavu + volitelně odeslání potvrzujícího e-mailu (admin)
 * POST: id, send_email (0|1)
 */
ob_start();
session_start();
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

if (!isset($_SESSION['walance_admin'])) {
    ob_end_clean();
    http_response_code(403);
    echo json_encode(['success' => false, 'error' => 'Přihlášení vyžadováno']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    ob_end_clean();
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Metoda není povolena']);
    exit;
}

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/crud.php';

$id = (int)($_POST['id'] ?? 0);
$sendEmail = (int)($_POST['send_email'] ?? 1);

if (!$id) {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Chybí ID rezervace']);
    exit;
}

try {
    $row = findActive('bookings', $id);
    if (!$row) {
        ob_end_clean();
        echo json_encode(['success' => false, 'error' => 'Rezervace nenalezena']);
        exit;
    }

    softUpdate('bookings', $id, ['status' => 'confirmed']);

    if ($sendEmail) {
        $name = $row['name'];
        $email = $row['email'];
        $date = $row['booking_date'];
        $time = $row['booking_time'];
        $message = $row['message'] ?? '';

        $dateFormatted = date('d.m.Y', strtotime($date));
        $clientSubject = 'Rezervace termínu u WALANCE – váš termín byl potvrzen';
        $clientBody = "Dobrý den, $name,\n\n";
        $clientBody .= "rádi vás informujeme, že váš termín byl potvrzen.\n\n";
        $clientBody .= "Shrnutí rezervace:\n";
        $clientBody .= "• Datum: $dateFormatted\n";
        $clientBody .= "• Čas: $time\n";
        if ($message) $clientBody .= "• Vaše zpráva: $message\n\n";
        $clientBody .= "---\n\n";
        $clientBody .= "WALANCE – Anatomie udržitelného výkonu\n\n";
        $clientBody .= "Metoda WALANCE spojuje koučink, fyzioterapii a provozní manuál pro lídry a týmy. Pomáháme vám udržet výkon bez vyhoření – protože byznys není sprint, je to maraton v pohybu.\n\n";
        $clientBody .= "Těšíme se na setkání!\n\n";
        $clientBody .= "S pozdravem,\ntým WALANCE";

        $headers = "Content-Type: text/plain; charset=UTF-8\r\n";
        $headers .= "From: " . MAIL_FROM . "\r\n";
        $headers .= "Reply-To: " . MAIL_FROM . "\r\n";
        $headers .= "Bcc: " . CONTACT_EMAIL . "\r\n";
        @mail($email, '=?UTF-8?B?' . base64_encode($clientSubject) . '?=', $clientBody, $headers);

        $bookingsId = $row['bookings_id'] ?? $row['id'];
        $contactsId = $row['contacts_id'];
        softInsert('activities', [
            'contacts_id' => $contactsId,
            'bookings_id' => $bookingsId,
            'type' => 'booking_confirmation',
            'subject' => 'Potvrzení termínu – ' . $dateFormatted . ' ' . $time,
            'body' => 'Odeslán potvrzující e-mail klientovi ' . $name . ' (' . $email . ').',
            'direction' => 'out',
        ]);
    }

    ob_end_clean();
    echo json_encode(['success' => true, 'status' => 'confirmed']);
} catch (Exception $e) {
    ob_end_clean();
    echo json_encode(['success' => false, 'error' => 'Nepodařilo se potvrdit rezervaci']);
}
