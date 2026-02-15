<?php
session_start();
if (!isset($_SESSION['walance_admin'])) {
    header('Location: index.php');
    exit;
}
require_once __DIR__ . '/../api/config.php';
require_once __DIR__ . '/../api/db.php';
require_once __DIR__ . '/../api/crud.php';

$db = getDb();
$v = defined('APP_VERSION') ? APP_VERSION : '1.0.0';

$id = (int)($_GET['id'] ?? 0);
if (!$id) {
    header('Location: dashboard.php');
    exit;
}

$contact = findActive('contacts', $id);
if (!$contact) {
    header('Location: dashboard.php');
    exit;
}

$entityId = $contact['contacts_id'] ?? $contact['id'];

// Rezervace kontaktu
$bookings = [];
$stmt = $db->prepare("SELECT * FROM bookings WHERE contacts_id = ? AND valid_to IS NULL ORDER BY booking_date DESC, booking_time DESC");
$stmt->execute([$entityId]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Aktivity (timeline)
$activities = [];
$stmt = $db->prepare("SELECT * FROM activities WHERE contacts_id = ? AND valid_to IS NULL ORDER BY valid_from DESC");
$stmt->execute([$entityId]);
$activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Update notes (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_notes') {
    $notes = trim($_POST['notes'] ?? '');
    try {
        softUpdate('contacts', $id, ['notes' => $notes]);
        header('Location: contact.php?id=' . $id);
        exit;
    } catch (Exception $e) {}
}

// Přidání aktivity (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_activity') {
    $type = $_POST['type'] ?? 'note';
    $subject = trim($_POST['subject'] ?? '');
    $body = trim($_POST['body'] ?? '');
    $direction = $_POST['direction'] ?? '';
    if (in_array($type, ['call', 'email', 'meeting', 'note'])) {
        try {
            softInsert('activities', [
                'contacts_id' => $entityId,
                'type' => $type,
                'subject' => $subject,
                'body' => $body,
                'direction' => in_array($type, ['call', 'email']) ? $direction : null,
            ]);
            header('Location: contact.php?id=' . $id);
            exit;
        } catch (Exception $e) {}
    }
}

$typeLabels = ['call' => 'Telefonát', 'email' => 'E-mail', 'meeting' => 'Schůzka', 'note' => 'Poznámka', 'booking_confirmation' => 'Potvrzení termínu'];
$directionLabels = ['in' => 'Příchozí', 'out' => 'Odchozí'];
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>WALANCE CRM - <?= htmlspecialchars($contact['name']) ?></title>
    <script src="https://cdn.tailwindcss.com?v=<?= htmlspecialchars($v) ?>"></script>
    <script src="https://unpkg.com/lucide@latest?v=<?= htmlspecialchars($v) ?>"></script>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;600;700&display=swap?v=<?= htmlspecialchars($v) ?>" rel="stylesheet">
    <style>body { font-family: 'DM Sans', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen">
<?php $adminCurrentPage = 'dashboard'; include __DIR__ . '/includes/layout.php'; ?>
    <div class="p-6 max-w-6xl">
        <a href="dashboard.php" class="inline-flex items-center text-slate-600 hover:text-teal-600 text-sm mb-6">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Zpět na kontakty
        </a>

        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <h2 class="text-xl font-bold text-slate-800 mb-4"><?= htmlspecialchars($contact['name']) ?></h2>
            <div class="flex flex-wrap gap-6">
                <div>
                    <span class="text-slate-500 text-sm">E-mail</span>
                    <a href="mailto:<?= htmlspecialchars($contact['email']) ?>" class="block text-teal-600 hover:underline font-medium"><?= htmlspecialchars($contact['email']) ?></a>
                </div>
                <?php if (!empty($contact['phone'])): ?>
                <div>
                    <span class="text-slate-500 text-sm">Telefon</span>
                    <a href="tel:<?= htmlspecialchars($contact['phone']) ?>" class="block text-teal-600 hover:underline font-medium"><?= htmlspecialchars($contact['phone']) ?></a>
                </div>
                <?php endif; ?>
                <div>
                    <span class="text-slate-500 text-sm">Zdroj</span>
                    <span class="block font-medium"><?= $contact['source'] === 'booking' ? 'Rezervace' : 'Kontaktní formulář' ?></span>
                </div>
            </div>
            <?php if (!empty($contact['message'])): ?>
            <div class="mt-4 pt-4 border-t border-slate-100">
                <span class="text-slate-500 text-sm">Původní zpráva</span>
                <p class="text-slate-700 mt-1"><?= nl2br(htmlspecialchars($contact['message'])) ?></p>
            </div>
            <?php endif; ?>
            <div class="mt-4 pt-4 border-t border-slate-100">
                <span class="text-slate-500 text-sm">Poznámky</span>
                <form method="POST" class="mt-1">
                    <input type="hidden" name="action" value="update_notes">
                    <textarea name="notes" rows="3" class="w-full px-3 py-2 border border-slate-200 rounded-lg text-sm"><?= htmlspecialchars($contact['notes'] ?? '') ?></textarea>
                    <button type="submit" class="mt-2 px-4 py-1.5 bg-slate-100 hover:bg-slate-200 rounded-lg text-sm font-medium">Uložit poznámky</button>
                </form>
            </div>
        </div>

        <?php if (!empty($bookings)): ?>
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <h3 class="font-bold text-slate-800 mb-4">Rezervace</h3>
            <div class="space-y-2">
                <?php foreach ($bookings as $b): ?>
                <div class="flex items-center justify-between py-2 border-b border-slate-50 last:border-0">
                    <span><?= date('d.m.Y', strtotime($b['booking_date'])) ?> <?= htmlspecialchars($b['booking_time']) ?></span>
                    <span class="px-2 py-0.5 rounded text-xs font-medium <?= $b['status'] === 'confirmed' ? 'bg-green-100 text-green-800' : ($b['status'] === 'cancelled' ? 'bg-red-100 text-red-800' : 'bg-amber-100 text-amber-800') ?>">
                        <?= $b['status'] === 'confirmed' ? 'Potvrzeno' : ($b['status'] === 'cancelled' ? 'Zamítnuto' : 'Čeká') ?>
                    </span>
                </div>
                <?php endforeach; ?>
            </div>
            <a href="bookings.php" class="inline-block mt-4 text-teal-600 hover:underline text-sm">Správa rezervací →</a>
        </div>
        <?php endif; ?>

        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <h3 class="font-bold text-slate-800 mb-4">Aktivity</h3>

            <form method="POST" class="mb-8 p-4 bg-slate-50 rounded-lg">
                <input type="hidden" name="action" value="add_activity">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Typ</label>
                        <select name="type" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                            <?php foreach ($typeLabels as $k => $l): ?>
                            <option value="<?= $k ?>"><?= $l ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div id="direction-field" class="hidden">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Směr</label>
                        <select name="direction" class="w-full px-3 py-2 border border-slate-300 rounded-lg">
                            <option value="out">Odchozí</option>
                            <option value="in">Příchozí</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Předmět</label>
                        <input type="text" name="subject" class="w-full px-3 py-2 border border-slate-300 rounded-lg" placeholder="Např. Dohodnutí termínu">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Obsah / poznámka</label>
                        <textarea name="body" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-lg" placeholder="Popis aktivity..."></textarea>
                    </div>
                </div>
                <button type="submit" class="mt-4 px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 font-medium text-sm">
                    Přidat aktivitu
                </button>
            </form>

            <?php if (empty($activities)): ?>
            <p class="text-slate-500 text-sm">Zatím žádné aktivity.</p>
            <?php else: ?>
            <div class="space-y-4">
                <?php foreach ($activities as $a): ?>
                <div class="flex gap-4 p-4 border-l-4 <?= $a['type'] === 'call' ? 'border-blue-400' : ($a['type'] === 'email' ? 'border-purple-400' : ($a['type'] === 'meeting' ? 'border-green-400' : ($a['type'] === 'booking_confirmation' ? 'border-teal-400' : 'border-slate-300')) ?> bg-slate-50/50 rounded-r-lg">
                    <div class="flex-shrink-0">
                        <?php if ($a['type'] === 'call'): ?>
                        <i data-lucide="phone" class="w-5 h-5 text-blue-600"></i>
                        <?php elseif ($a['type'] === 'email'): ?>
                        <i data-lucide="mail" class="w-5 h-5 text-purple-600"></i>
                        <?php elseif ($a['type'] === 'meeting'): ?>
                        <i data-lucide="users" class="w-5 h-5 text-green-600"></i>
                        <?php elseif ($a['type'] === 'booking_confirmation'): ?>
                        <i data-lucide="calendar-check" class="w-5 h-5 text-teal-600"></i>
                        <?php else: ?>
                        <i data-lucide="file-text" class="w-5 h-5 text-slate-600"></i>
                        <?php endif; ?>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex flex-wrap items-center gap-2 text-sm">
                            <span class="font-medium text-slate-800"><?= $typeLabels[$a['type']] ?? $a['type'] ?></span>
                            <?php if (!empty($a['direction'])): ?>
                            <span class="text-slate-500">(<?= $directionLabels[$a['direction']] ?? $a['direction'] ?>)</span>
                            <?php endif; ?>
                            <span class="text-slate-400 text-xs"><?= date('d.m.Y H:i', strtotime($a['valid_from'])) ?></span>
                        </div>
                        <?php if (!empty($a['subject'])): ?>
                        <p class="font-medium text-slate-700 mt-1"><?= htmlspecialchars($a['subject']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($a['body'])): ?>
                        <p class="text-slate-600 text-sm mt-1"><?= nl2br(htmlspecialchars($a['body'])) ?></p>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <?php endif; ?>
        </div>
    </div>
<?php include __DIR__ . '/includes/layout-end.php'; ?>

    <script>
        lucide.createIcons();
        document.querySelector('select[name="type"]').addEventListener('change', function() {
            const dir = document.getElementById('direction-field');
            dir.classList.toggle('hidden', !['call','email'].includes(this.value));
        });
    </script>
</body>
</html>
