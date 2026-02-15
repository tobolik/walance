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

// Filtry
$source = $_GET['source'] ?? '';
$search = trim($_GET['search'] ?? '');

$sql = "SELECT c.*, 
    (SELECT COUNT(*) FROM bookings b WHERE b.contacts_id = COALESCE(c.contacts_id, c.id) AND b.valid_to IS NULL) as booking_count,
    (SELECT MAX(booking_date) FROM bookings b WHERE b.contacts_id = COALESCE(c.contacts_id, c.id) AND b.valid_to IS NULL) as last_booking
    FROM contacts c WHERE c.valid_to IS NULL";
$params = [];

if ($source) {
    $sql .= " AND c.source = ?";
    $params[] = $source;
}
if ($search) {
    $sql .= " AND (c.name LIKE ? OR c.email LIKE ? OR c.message LIKE ?)";
    $p = "%$search%";
    $params = array_merge($params, [$p, $p, $p]);
}
$sql .= " ORDER BY c.valid_from DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$contacts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Načíst rezervace pro kontakty (soft-update: contacts_id, valid_to)
$bookingsByContact = [];
foreach ($contacts as $c) {
    if ($c['source'] === 'booking') {
        $entityId = $c['contacts_id'] ?? $c['id'];
        $b = $db->prepare("SELECT booking_date, booking_time FROM bookings WHERE contacts_id = ? AND valid_to IS NULL ORDER BY booking_date DESC");
        $b->execute([$entityId]);
        $bookingsByContact[$c['id']] = $b->fetchAll(PDO::FETCH_ASSOC);
    }
}

// Update notes (AJAX) – softUpdate
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    if ($_POST['action'] === 'update_notes') {
        $id = (int)($_POST['id'] ?? 0);
        $notes = trim($_POST['notes'] ?? '');
        try {
            softUpdate('contacts', $id, ['notes' => $notes]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false]);
        }
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WALANCE CRM - Kontakty</title>
    <script src="https://cdn.tailwindcss.com?v=<?= htmlspecialchars($v) ?>"></script>
    <script src="https://unpkg.com/lucide@latest?v=<?= htmlspecialchars($v) ?>"></script>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;600;700&display=swap?v=<?= htmlspecialchars($v) ?>" rel="stylesheet">
    <style>body { font-family: 'DM Sans', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen">
    <header class="bg-white border-b border-slate-200 px-6 py-4">
        <div class="max-w-6xl mx-auto flex justify-between items-center">
            <h1 class="text-xl font-bold text-slate-800">WALANCE CRM</h1>
            <nav class="flex items-center gap-4">
                <a href="dashboard.php" class="text-teal-600 font-medium text-sm">Kontakty</a>
                <a href="bookings.php" class="text-slate-500 hover:text-teal-600 text-sm">Rezervace</a>
                <a href="calendar.php" class="text-slate-500 hover:text-teal-600 text-sm">Kalendář</a>
                <a href="availability.php" class="text-slate-500 hover:text-teal-600 text-sm">Dostupnost</a>
                <a href="../" class="text-slate-500 hover:text-teal-600 text-sm">Web</a>
                <span class="text-slate-500 text-sm"><?= htmlspecialchars($_SESSION['walance_admin_name'] ?? 'Admin') ?></span>
                <a href="logout.php" class="text-red-600 hover:text-red-700 text-sm font-medium">Odhlásit</a>
            </nav>
        </div>
    </header>

    <main class="max-w-6xl mx-auto p-6">
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-slate-800 mb-4">Kontakty z webu</h2>
            <form method="GET" class="flex flex-wrap gap-4">
                <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Hledat jméno, e-mail..."
                    class="px-4 py-2 border border-slate-300 rounded-lg flex-1 min-w-[200px]">
                <select name="source" class="px-4 py-2 border border-slate-300 rounded-lg">
                    <option value="">Všechny zdroje</option>
                    <option value="contact" <?= $source === 'contact' ? 'selected' : '' ?>>Kontaktní formulář</option>
                    <option value="booking" <?= $source === 'booking' ? 'selected' : '' ?>>Rezervace</option>
                </select>
                <button type="submit" class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 font-medium">
                    <i data-lucide="search" class="w-4 h-4 inline mr-1"></i> Hledat
                </button>
            </form>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">Datum</th>
                            <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">Jméno</th>
                            <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">E-mail</th>
                            <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">Zdroj</th>
                            <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">Zpráva</th>
                            <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">Poznámky</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($contacts as $c): ?>
                        <tr class="border-b border-slate-100 hover:bg-slate-50/50">
                            <td class="py-4 px-6 text-sm text-slate-600">
                                <?= date('d.m.Y H:i', strtotime($c['valid_from'] ?? $c['created_at'] ?? 'now')) ?>
                            </td>
                            <td class="py-4 px-6 font-medium text-slate-800">
                                <a href="contact.php?id=<?= (int)$c['id'] ?>" class="text-teal-600 hover:underline"><?= htmlspecialchars($c['name']) ?></a>
                            </td>
                            <td class="py-4 px-6">
                                <a href="mailto:<?= htmlspecialchars($c['email']) ?>" class="text-teal-600 hover:underline">
                                    <?= htmlspecialchars($c['email']) ?>
                                </a>
                            </td>
                            <td class="py-4 px-6">
                                <span class="px-2 py-1 rounded text-xs font-medium <?= $c['source'] === 'booking' ? 'bg-teal-100 text-teal-700' : 'bg-slate-100 text-slate-600' ?>">
                                    <?= $c['source'] === 'booking' ? 'Rezervace' : 'Kontakt' ?>
                                </span>
                                <?php if ($c['booking_count'] > 0): ?>
                                    <span class="text-xs text-slate-500">(<?= $c['booking_count'] ?>×)</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 px-6 text-sm text-slate-600 max-w-xs">
                                <?php if (!empty($bookingsByContact[$c['id']])): ?>
                                    <?php foreach ($bookingsByContact[$c['id']] as $b): ?>
                                        <span class="block"><?= date('d.m.', strtotime($b['booking_date'])) ?> <?= $b['booking_time'] ?></span>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                                <?= htmlspecialchars($c['message'] ?: ($c['phone'] && empty($bookingsByContact[$c['id']]) ? $c['phone'] : '')) ?>
                                <?php if (empty($bookingsByContact[$c['id']]) && !$c['message'] && !$c['phone']): ?>—<?php endif; ?>
                            </td>
                            <td class="py-4 px-6">
                                <textarea data-id="<?= $c['id'] ?>" class="notes-input w-full text-sm border border-slate-200 rounded px-2 py-1 focus:ring-2 focus:ring-teal-500" rows="2" placeholder="Poznámka..."><?= htmlspecialchars($c['notes'] ?? '') ?></textarea>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if (empty($contacts)): ?>
            <div class="p-12 text-center text-slate-500">Zatím žádné kontakty.</div>
            <?php endif; ?>
        </div>

        <div class="mt-6 p-4 bg-slate-50 rounded-lg text-sm text-slate-600">
            <strong>Rezervace:</strong> Pro zobrazení rezervací přejděte do detailu kontaktu (zdroj „Rezervace“). 
            Poznámky se ukládají automaticky při odchodu z pole.
        </div>
    </main>

    <footer class="max-w-6xl mx-auto px-6 py-4 text-center text-slate-400 text-xs">
        v<?= htmlspecialchars($v) ?>
    </footer>

    <script>
        lucide.createIcons();
        document.querySelectorAll('.notes-input').forEach(el => {
            el.addEventListener('blur', function() {
                const id = this.dataset.id;
                const notes = this.value;
                fetch('dashboard.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: 'action=update_notes&id=' + id + '&notes=' + encodeURIComponent(notes)
                });
            });
        });
    </script>
</body>
</html>
