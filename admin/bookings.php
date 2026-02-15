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

// Akce: potvrdit / zamítnout (softUpdate)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $id = (int)($_POST['id'] ?? 0);
    $action = $_POST['action'];
    if ($id && in_array($action, ['confirm', 'cancel'])) {
        $status = $action === 'confirm' ? 'confirmed' : 'cancelled';
        try {
            softUpdate('bookings', $id, ['status' => $status]);
        } catch (Exception $e) { /* log */ }
    }
    header('Location: bookings.php' . (isset($_GET['status']) ? '?status=' . urlencode($_GET['status']) : ''));
    exit;
}

// AJAX pro rychlé akce
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ajax_action'])) {
    header('Content-Type: application/json');
    $id = (int)($_POST['id'] ?? 0);
    $action = $_POST['ajax_action'];
    if ($id && in_array($action, ['confirm', 'cancel'])) {
        $status = $action === 'confirm' ? 'confirmed' : 'cancelled';
        try {
            softUpdate('bookings', $id, ['status' => $status]);
            echo json_encode(['success' => true, 'status' => $status]);
        } catch (Exception $e) {
            echo json_encode(['success' => false]);
        }
    } else {
        echo json_encode(['success' => false]);
    }
    exit;
}

$statusFilter = $_GET['status'] ?? '';
$sql = "SELECT b.*, c.notes as contact_notes FROM bookings b 
        LEFT JOIN contacts c ON b.contacts_id = c.contacts_id AND c.valid_to IS NULL 
        WHERE b.valid_to IS NULL";
$params = [];
if ($statusFilter && in_array($statusFilter, ['pending', 'confirmed', 'cancelled'])) {
    $sql .= " AND b.status = ?";
    $params[] = $statusFilter;
}
$sql .= " ORDER BY b.booking_date ASC, b.booking_time ASC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

$statusLabels = [
    'pending' => ['Čeká', 'bg-amber-100 text-amber-800'],
    'confirmed' => ['Potvrzeno', 'bg-green-100 text-green-800'],
    'cancelled' => ['Zamítnuto', 'bg-red-100 text-red-800'],
];
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WALANCE CRM - Rezervace</title>
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
                <a href="dashboard.php" class="text-slate-500 hover:text-teal-600 text-sm">Kontakty</a>
                <a href="bookings.php" class="text-teal-600 font-medium text-sm">Rezervace</a>
                <a href="calendar.php" class="text-slate-500 hover:text-teal-600 text-sm">Kalendář</a>
                <a href="availability.php" class="text-slate-500 hover:text-teal-600 text-sm">Dostupnost</a>
                <a href="../" class="text-slate-500 hover:text-teal-600 text-sm">Web</a>
                <span class="text-slate-400 text-xs">v<?= htmlspecialchars($v) ?></span>
                <span class="text-slate-500 text-sm"><?= htmlspecialchars($_SESSION['walance_admin_name'] ?? 'Admin') ?></span>
                <a href="logout.php" class="text-red-600 hover:text-red-700 text-sm font-medium">Odhlásit</a>
            </nav>
        </div>
    </header>

    <main class="max-w-6xl mx-auto p-6">
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <h2 class="text-lg font-bold text-slate-800 mb-4">Správa rezervací</h2>
            <div class="flex flex-wrap gap-4 items-center">
                <span class="text-sm text-slate-600">Filtr:</span>
                <a href="bookings.php" class="px-3 py-1.5 rounded-lg text-sm <?= !$statusFilter ? 'bg-teal-100 text-teal-700 font-medium' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">Vše</a>
                <a href="bookings.php?status=pending" class="px-3 py-1.5 rounded-lg text-sm <?= $statusFilter === 'pending' ? 'bg-amber-100 text-amber-800 font-medium' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">Čekající</a>
                <a href="bookings.php?status=confirmed" class="px-3 py-1.5 rounded-lg text-sm <?= $statusFilter === 'confirmed' ? 'bg-green-100 text-green-800 font-medium' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">Potvrzené</a>
                <a href="bookings.php?status=cancelled" class="px-3 py-1.5 rounded-lg text-sm <?= $statusFilter === 'cancelled' ? 'bg-red-100 text-red-800 font-medium' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">Zamítnuté</a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">Datum & čas</th>
                            <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">Jméno</th>
                            <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">Kontakt</th>
                            <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">Stav</th>
                            <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">Akce</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($bookings as $b): ?>
                        <tr class="border-b border-slate-100 hover:bg-slate-50/50 booking-row" data-id="<?= $b['id'] ?>">
                            <td class="py-4 px-6">
                                <span class="font-medium text-slate-800"><?= date('d.m.Y', strtotime($b['booking_date'])) ?></span>
                                <span class="text-slate-600"><?= htmlspecialchars($b['booking_time']) ?></span>
                            </td>
                            <td class="py-4 px-6 font-medium text-slate-800"><?= htmlspecialchars($b['name']) ?></td>
                            <td class="py-4 px-6 text-sm">
                                <a href="mailto:<?= htmlspecialchars($b['email']) ?>" class="text-teal-600 hover:underline"><?= htmlspecialchars($b['email']) ?></a>
                                <?php if ($b['phone']): ?>
                                    <span class="block text-slate-500"><?= htmlspecialchars($b['phone']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 px-6">
                                <span class="status-badge px-2 py-1 rounded text-xs font-medium <?= $statusLabels[$b['status']][1] ?? 'bg-slate-100' ?>">
                                    <?= $statusLabels[$b['status']][0] ?? $b['status'] ?>
                                </span>
                            </td>
                            <td class="py-4 px-6">
                                <?php if ($b['status'] === 'pending'): ?>
                                <div class="flex gap-2">
                                    <button type="button" onclick="updateBooking(<?= $b['id'] ?>, 'confirm')" class="px-3 py-1.5 bg-green-600 hover:bg-green-700 text-white text-xs font-medium rounded-lg transition-colors">
                                        Potvrdit
                                    </button>
                                    <button type="button" onclick="updateBooking(<?= $b['id'] ?>, 'cancel')" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition-colors">
                                        Zamítnout
                                    </button>
                                </div>
                                <?php elseif ($b['status'] === 'confirmed'): ?>
                                <button type="button" onclick="updateBooking(<?= $b['id'] ?>, 'cancel')" class="px-3 py-1.5 bg-slate-500 hover:bg-slate-600 text-white text-xs font-medium rounded-lg transition-colors">
                                    Zamítnout
                                </button>
                                <?php else: ?>
                                <span class="text-slate-400 text-xs">—</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php if ($b['message']): ?>
                        <tr class="bg-slate-50/50">
                            <td colspan="5" class="py-2 px-6 text-sm text-slate-600">
                                <span class="text-slate-500">Poznámka:</span> <?= htmlspecialchars($b['message']) ?>
                            </td>
                        </tr>
                        <?php endif; ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if (empty($bookings)): ?>
            <div class="p-12 text-center text-slate-500">Žádné rezervace.</div>
            <?php endif; ?>
        </div>
    </main>

    <script>
        lucide.createIcons();
        function updateBooking(id, action) {
            const row = document.querySelector(`.booking-row[data-id="${id}"]`);
            if (!row) return;
            const btn = event.target;
            btn.disabled = true;
            fetch('bookings.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'ajax_action=' + action + '&id=' + id
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    location.reload();
                }
            })
            .catch(() => { btn.disabled = false; });
        }
    </script>
</body>
</html>
