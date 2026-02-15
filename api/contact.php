<?php
/**
 * Kontaktní formulář - odeslání e-mailu a uložení do CRM
 * Očekává JSON POST: { name, email, subject?, message }
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
require_once __DIR__ . '/db.php';

$input = json_decode(file_get_contents('php://input'), true) ?? [];
$name = trim($input['name'] ?? '');
$email = trim($input['email'] ?? '');
$subject = trim($input['subject'] ?? 'Nová zpráva z webu WALANCE');
$message = trim($input['message'] ?? '');

if (empty($name) || empty($email) || empty($message)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Vyplňte prosím jméno, e-mail a zprávu.']);
    exit;
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => 'Neplatná e-mailová adresa.']);
    exit;
}

// Uložení do CRM
try {
    $db = getDb();
    $stmt = $db->prepare("INSERT INTO contacts (name, email, message, source) VALUES (?, ?, ?, 'contact')");
    $stmt->execute([$name, $email, $message]);
} catch (Exception $e) {
    // Log, ale nepřerušuj
}

// Odeslání e-mailu
$to = CONTACT_EMAIL;
$headers = [
    'From: ' . $email,
    'Reply-To: ' . $email,
    'X-Mailer: PHP/' . phpversion(),
    'Content-Type: text/plain; charset=UTF-8'
];

$body = "Jméno: $name\n";
$body .= "E-mail: $email\n\n";
$body .= "Zpráva:\n$message";

$sent = @mail($to, '=?UTF-8?B?' . base64_encode($subject) . '?=', $body, implode("\r\n", $headers));

if ($sent) {
    echo json_encode(['success' => true, 'message' => 'Zpráva byla odeslána. Brzy vás budeme kontaktovat.']);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Nepodařilo se odeslat zprávu. Zkuste to prosím později nebo napište na ' . CONTACT_EMAIL]);
}
