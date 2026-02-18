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

// ── AJAX handlers (must come before any HTML output) ──
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json; charset=utf-8');
    header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

    $action = $_POST['action'];

    // ── Edit contact ──
    if ($action === 'edit_contact') {
        $name = trim($_POST['name'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $notes = trim($_POST['notes'] ?? '');
        if (empty($name) || empty($email)) {
            echo json_encode(['success' => false, 'error' => 'Jméno a e-mail jsou povinné.']);
            exit;
        }
        try {
            softUpdate('contacts', $id, [
                'name' => $name,
                'email' => $email,
                'phone' => $phone,
                'notes' => $notes,
            ]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    // ── Add activity ──
    if ($action === 'add_activity') {
        $contact = findActive('contacts', $id);
        if (!$contact) { echo json_encode(['success' => false, 'error' => 'Kontakt nenalezen.']); exit; }
        $entityId = $contact['contacts_id'] ?? $contact['id'];
        $type = $_POST['type'] ?? 'note';
        $subject = trim($_POST['subject'] ?? '');
        $body = trim($_POST['body'] ?? '');
        $direction = $_POST['direction'] ?? '';
        if (!in_array($type, ['call', 'email', 'meeting', 'note'])) {
            echo json_encode(['success' => false, 'error' => 'Neplatný typ.']);
            exit;
        }
        try {
            softInsert('activities', [
                'contacts_id' => $entityId,
                'type' => $type,
                'subject' => $subject,
                'body' => $body,
                'direction' => in_array($type, ['call', 'email']) ? $direction : null,
            ]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    // ── Edit activity ──
    if ($action === 'edit_activity') {
        $actId = (int)($_POST['activity_id'] ?? 0);
        if (!$actId) { echo json_encode(['success' => false, 'error' => 'Chybí ID aktivity.']); exit; }
        $type = $_POST['type'] ?? 'note';
        $subject = trim($_POST['subject'] ?? '');
        $body = trim($_POST['body'] ?? '');
        $direction = $_POST['direction'] ?? '';
        if (!in_array($type, ['call', 'email', 'meeting', 'note', 'booking_confirmation'])) {
            echo json_encode(['success' => false, 'error' => 'Neplatný typ.']);
            exit;
        }
        try {
            softUpdate('activities', $actId, [
                'type' => $type,
                'subject' => $subject,
                'body' => $body,
                'direction' => in_array($type, ['call', 'email']) ? $direction : null,
            ]);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    // ── Delete activity ──
    if ($action === 'delete_activity') {
        $actId = (int)($_POST['activity_id'] ?? 0);
        if (!$actId) { echo json_encode(['success' => false, 'error' => 'Chybí ID aktivity.']); exit; }
        try {
            softDelete('activities', $actId);
            echo json_encode(['success' => true]);
        } catch (Exception $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    // ── Search contacts for merge ──
    if ($action === 'search_contacts') {
        $q = trim($_POST['q'] ?? '');
        $contact = findActive('contacts', $id);
        $entityId = $contact ? ($contact['contacts_id'] ?? $contact['id']) : 0;
        $sql = "SELECT c.id, c.contacts_id, c.name, c.email, c.phone, c.source,
                    (SELECT COUNT(*) FROM bookings b WHERE b.contacts_id = COALESCE(c.contacts_id, c.id) AND b.valid_to IS NULL) as booking_count,
                    (SELECT COUNT(*) FROM activities a WHERE a.contacts_id = COALESCE(c.contacts_id, c.id) AND a.valid_to IS NULL) as activity_count
                FROM contacts c WHERE c.valid_to IS NULL AND c.id != ?";
        $params = [$id];
        if ($q !== '') {
            $sql .= " AND (c.name LIKE ? OR c.email LIKE ? OR c.phone LIKE ?)";
            $p = "%$q%";
            $params[] = $p; $params[] = $p; $params[] = $p;
        }
        $sql .= " ORDER BY c.name ASC LIMIT 20";
        $stmt = $db->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);
        echo json_encode(['success' => true, 'contacts' => $results]);
        exit;
    }

    // ── Merge contacts ──
    if ($action === 'merge_contacts') {
        $mergeId = (int)($_POST['merge_id'] ?? 0);
        if (!$mergeId) { echo json_encode(['success' => false, 'error' => 'Chybí ID kontaktu ke sloučení.']); exit; }

        $primary = findActive('contacts', $id);
        $secondary = findActive('contacts', $mergeId);
        if (!$primary || !$secondary) {
            echo json_encode(['success' => false, 'error' => 'Některý z kontaktů nenalezen.']);
            exit;
        }

        $primaryEntityId = $primary['contacts_id'] ?? $primary['id'];
        $secondaryEntityId = $secondary['contacts_id'] ?? $secondary['id'];

        try {
            $db->beginTransaction();

            // Move bookings from secondary to primary (via softUpdate, preserves history)
            $stmtB = $db->prepare("SELECT id FROM bookings WHERE contacts_id = ? AND valid_to IS NULL");
            $stmtB->execute([$secondaryEntityId]);
            foreach ($stmtB->fetchAll(PDO::FETCH_COLUMN) as $bId) {
                softUpdate('bookings', (int)$bId, ['contacts_id' => $primaryEntityId]);
            }

            // Move activities from secondary to primary (via softUpdate, preserves history)
            $stmtA = $db->prepare("SELECT id FROM activities WHERE contacts_id = ? AND valid_to IS NULL");
            $stmtA->execute([$secondaryEntityId]);
            foreach ($stmtA->fetchAll(PDO::FETCH_COLUMN) as $aId) {
                softUpdate('activities', (int)$aId, ['contacts_id' => $primaryEntityId]);
            }

            // Merge data: if primary is missing phone/message, take from secondary
            $updates = [];
            if (empty($primary['phone']) && !empty($secondary['phone'])) {
                $updates['phone'] = $secondary['phone'];
            }
            if (empty($primary['notes']) && !empty($secondary['notes'])) {
                $updates['notes'] = $secondary['notes'];
            } elseif (!empty($primary['notes']) && !empty($secondary['notes'])) {
                $updates['notes'] = $primary['notes'] . "\n---\n" . $secondary['notes'];
            }
            if (empty($primary['message']) && !empty($secondary['message'])) {
                $updates['message'] = $secondary['message'];
            }

            // Record merge trail on secondary, then soft-delete
            softUpdate('contacts', $mergeId, ['merged_into_contacts_id' => $primaryEntityId]);
            $updatedSecondary = findActiveByEntityId('contacts', $secondaryEntityId);
            if ($updatedSecondary) {
                softDelete('contacts', (int)$updatedSecondary['id']);
            }

            // Update primary if needed
            if (!empty($updates)) {
                softUpdate('contacts', $id, $updates);
            }

            $db->commit();
            echo json_encode(['success' => true, 'message' => 'Kontakty byly sloučeny.']);
        } catch (Exception $e) {
            $db->rollBack();
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
        exit;
    }

    echo json_encode(['success' => false, 'error' => 'Neznámá akce.']);
    exit;
}

// ── Load contact data ──
$contact = findActive('contacts', $id);
if (!$contact) {
    header('Location: dashboard.php');
    exit;
}

$entityId = $contact['contacts_id'] ?? $contact['id'];

// Bookings
$stmt = $db->prepare("SELECT * FROM bookings WHERE contacts_id = ? AND valid_to IS NULL ORDER BY booking_date DESC, booking_time DESC");
$stmt->execute([$entityId]);
$bookings = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Activities
$stmt = $db->prepare("SELECT * FROM activities WHERE contacts_id = ? AND valid_to IS NULL ORDER BY valid_from DESC");
$stmt->execute([$entityId]);
$activities = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Merged contacts (absorbed into this one)
$mergedContacts = [];
try {
    $stmt = $db->prepare("
        SELECT c1.* FROM contacts c1
        INNER JOIN (
            SELECT contacts_id, MAX(id) as max_id
            FROM contacts
            WHERE merged_into_contacts_id = ? AND valid_to IS NOT NULL
            GROUP BY contacts_id
        ) latest ON c1.id = latest.max_id
        ORDER BY c1.valid_to DESC
    ");
    $stmt->execute([$entityId]);
    $mergedContacts = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    // Column may not exist yet if migration hasn't been run
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
    <style>
        body { font-family: 'DM Sans', sans-serif; }
        .modal-backdrop { background: rgba(0,0,0,0.4); }
        .toast { animation: slideIn 0.3s ease-out, fadeOut 0.3s ease-in 2.7s forwards; }
        @keyframes slideIn { from { transform: translateY(-1rem); opacity: 0; } to { transform: translateY(0); opacity: 1; } }
        @keyframes fadeOut { from { opacity: 1; } to { opacity: 0; } }
    </style>
</head>
<body class="bg-slate-100 min-h-screen">
<?php $adminCurrentPage = 'dashboard'; include __DIR__ . '/includes/layout.php'; ?>
    <div class="p-6 max-w-6xl">
        <a href="dashboard.php" class="inline-flex items-center text-slate-600 hover:text-teal-600 text-sm mb-6">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Zpět na kontakty
        </a>

        <!-- ═══ Contact Card ═══ -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="flex items-start justify-between mb-4">
                <div id="contact-display">
                    <h2 class="text-xl font-bold text-slate-800" id="display-name"><?= htmlspecialchars($contact['name']) ?></h2>
                    <div class="flex flex-wrap gap-6 mt-3">
                        <div>
                            <span class="text-slate-500 text-sm">E-mail</span>
                            <a href="mailto:<?= htmlspecialchars($contact['email']) ?>" class="block text-teal-600 hover:underline font-medium" id="display-email"><?= htmlspecialchars($contact['email']) ?></a>
                        </div>
                        <div>
                            <span class="text-slate-500 text-sm">Telefon</span>
                            <span class="block font-medium" id="display-phone"><?= !empty($contact['phone']) ? htmlspecialchars($contact['phone']) : '—' ?></span>
                        </div>
                        <div>
                            <span class="text-slate-500 text-sm">Zdroj</span>
                            <span class="block font-medium"><?= $contact['source'] === 'booking' ? 'Rezervace' : 'Kontaktní formulář' ?></span>
                        </div>
                    </div>
                </div>
                <div class="flex gap-2 flex-shrink-0">
                    <button type="button" onclick="openEditContact()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-slate-700 bg-slate-100 hover:bg-slate-200 rounded-lg transition-colors" title="Upravit kontakt">
                        <i data-lucide="pencil" class="w-4 h-4"></i> Upravit
                    </button>
                    <button type="button" onclick="openMergeModal()" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-amber-700 bg-amber-50 hover:bg-amber-100 rounded-lg transition-colors" title="Sloučit s jiným kontaktem">
                        <i data-lucide="merge" class="w-4 h-4"></i> Sloučit
                    </button>
                </div>
            </div>

            <!-- Inline edit form (hidden by default) -->
            <div id="contact-edit-form" class="hidden border-t border-slate-200 pt-4 mt-4">
                <h3 class="text-sm font-semibold text-slate-600 mb-3">Upravit kontakt</h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Jméno *</label>
                        <input type="text" id="edit-name" value="<?= htmlspecialchars($contact['name']) ?>" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">E-mail *</label>
                        <input type="email" id="edit-email" value="<?= htmlspecialchars($contact['email']) ?>" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Telefon</label>
                        <input type="tel" id="edit-phone" value="<?= htmlspecialchars($contact['phone'] ?? '') ?>" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500">
                    </div>
                </div>
                <div class="mt-4">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Poznámky</label>
                    <textarea id="edit-notes" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500"><?= htmlspecialchars($contact['notes'] ?? '') ?></textarea>
                </div>
                <div class="flex gap-2 mt-4">
                    <button type="button" onclick="saveContact()" class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 text-sm font-medium transition-colors">
                        <i data-lucide="check" class="w-4 h-4 inline mr-1"></i> Uložit
                    </button>
                    <button type="button" onclick="cancelEditContact()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-lg text-sm font-medium transition-colors">
                        Zrušit
                    </button>
                </div>
            </div>

            <?php if (!empty($contact['message'])): ?>
            <div class="mt-4 pt-4 border-t border-slate-100">
                <span class="text-slate-500 text-sm">Původní zpráva</span>
                <p class="text-slate-700 mt-1"><?= nl2br(htmlspecialchars($contact['message'])) ?></p>
            </div>
            <?php endif; ?>

            <?php if (!empty($contact['notes'])): ?>
            <div id="notes-readonly" class="mt-4 pt-4 border-t border-slate-100">
                <span class="text-slate-500 text-sm">Poznámky</span>
                <p class="text-slate-700 mt-1"><?= nl2br(htmlspecialchars($contact['notes'])) ?></p>
            </div>
            <?php endif; ?>

            <?php if (!empty($mergedContacts)): ?>
            <div class="mt-4 pt-4 border-t border-slate-100">
                <button type="button" onclick="document.getElementById('merged-list').classList.toggle('hidden')" class="flex items-center gap-2 text-sm text-slate-600 hover:text-teal-600 transition-colors">
                    <i data-lucide="git-merge" class="w-4 h-4"></i>
                    <span>Sloučené kontakty</span>
                    <span class="px-1.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-700"><?= count($mergedContacts) ?></span>
                    <i data-lucide="chevron-down" class="w-3.5 h-3.5"></i>
                </button>
                <div id="merged-list" class="hidden mt-3 space-y-2">
                    <?php foreach ($mergedContacts as $mc): ?>
                    <div class="p-3 bg-slate-50 rounded-lg border border-slate-200">
                        <div class="flex items-center justify-between">
                            <div>
                                <span class="font-medium text-slate-800"><?= htmlspecialchars($mc['name']) ?></span>
                                <span class="text-slate-400 mx-1.5">&middot;</span>
                                <span class="text-sm text-slate-600"><?= htmlspecialchars($mc['email']) ?></span>
                                <?php if (!empty($mc['phone'])): ?>
                                    <span class="text-slate-400 mx-1.5">&middot;</span>
                                    <span class="text-sm text-slate-600"><?= htmlspecialchars($mc['phone']) ?></span>
                                <?php endif; ?>
                            </div>
                            <span class="text-xs text-slate-400">Sloučen <?= date('d.m.Y H:i', strtotime($mc['valid_to'])) ?></span>
                        </div>
                        <?php if (!empty($mc['message'])): ?>
                        <p class="text-sm text-slate-500 mt-1 truncate" title="<?= htmlspecialchars($mc['message']) ?>"><?= htmlspecialchars($mc['message']) ?></p>
                        <?php endif; ?>
                        <?php if (!empty($mc['notes'])): ?>
                        <p class="text-sm text-slate-500 mt-1"><em>Poznámky:</em> <?= htmlspecialchars($mc['notes']) ?></p>
                        <?php endif; ?>
                    </div>
                    <?php endforeach; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>

        <!-- ═══ Bookings ═══ -->
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

        <!-- ═══ Activities ═══ -->
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-bold text-slate-800">Aktivity</h3>
                <button type="button" onclick="toggleAddActivity()" id="btn-toggle-add" class="inline-flex items-center gap-1.5 px-3 py-1.5 text-sm font-medium text-teal-700 bg-teal-50 hover:bg-teal-100 rounded-lg transition-colors">
                    <i data-lucide="plus" class="w-4 h-4"></i> Přidat aktivitu
                </button>
            </div>

            <!-- Add activity form (hidden by default) -->
            <div id="add-activity-form" class="hidden mb-8 p-4 bg-slate-50 rounded-lg border border-slate-200">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Typ</label>
                        <select id="add-act-type" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                            <option value="call">Telefonát</option>
                            <option value="email">E-mail</option>
                            <option value="meeting">Schůzka</option>
                            <option value="note" selected>Poznámka</option>
                        </select>
                    </div>
                    <div id="add-direction-field" class="hidden">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Směr</label>
                        <select id="add-act-direction" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                            <option value="out">Odchozí</option>
                            <option value="in">Příchozí</option>
                        </select>
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Předmět</label>
                        <input type="text" id="add-act-subject" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm" placeholder="Např. Dohodnutí termínu">
                    </div>
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Obsah / poznámka</label>
                        <textarea id="add-act-body" rows="3" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm" placeholder="Popis aktivity..."></textarea>
                    </div>
                </div>
                <div class="flex gap-2 mt-4">
                    <button type="button" onclick="saveActivity()" class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 font-medium text-sm transition-colors">
                        <i data-lucide="check" class="w-4 h-4 inline mr-1"></i> Uložit aktivitu
                    </button>
                    <button type="button" onclick="toggleAddActivity()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-lg text-sm font-medium transition-colors">
                        Zrušit
                    </button>
                </div>
            </div>

            <!-- Activity list -->
            <div id="activity-list">
                <?php if (empty($activities)): ?>
                <p class="text-slate-500 text-sm" id="no-activities">Zatím žádné aktivity.</p>
                <?php else: ?>
                <div class="space-y-4">
                    <?php foreach ($activities as $a):
                        $borderMap = ['call' => 'border-blue-400', 'email' => 'border-purple-400', 'meeting' => 'border-green-400', 'booking_confirmation' => 'border-teal-400'];
                        $borderClass = isset($borderMap[$a['type']]) ? $borderMap[$a['type']] : 'border-slate-300';
                        $aSubject = isset($a['subject']) ? $a['subject'] : '';
                        $aBody = isset($a['body']) ? $a['body'] : '';
                        $aDir = isset($a['direction']) ? $a['direction'] : '';
                    ?>
                    <div class="activity-item flex gap-4 p-4 border-l-4 <?= $borderClass ?> bg-slate-50/50 rounded-r-lg group" data-id="<?= (int)$a['id'] ?>" data-type="<?= htmlspecialchars($a['type']) ?>" data-subject="<?= htmlspecialchars($aSubject) ?>" data-body="<?= htmlspecialchars($aBody) ?>" data-direction="<?= htmlspecialchars($aDir) ?>">
                        <div class="flex-shrink-0 pt-0.5">
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
                        <div class="flex-shrink-0 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button type="button" onclick="openEditActivity(<?= (int)$a['id'] ?>)" class="p-1.5 text-slate-400 hover:text-teal-600 hover:bg-teal-50 rounded-lg transition-colors" title="Upravit">
                                <i data-lucide="pencil" class="w-4 h-4"></i>
                            </button>
                            <button type="button" onclick="confirmDeleteActivity(<?= (int)$a['id'] ?>)" class="p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-lg transition-colors" title="Smazat">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- ═══ Edit Activity Modal ═══ -->
    <div id="edit-activity-modal" class="fixed inset-0 z-50 hidden">
        <div class="modal-backdrop absolute inset-0" onclick="closeEditActivity()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-lg p-6 pointer-events-auto relative">
                <button type="button" onclick="closeEditActivity()" class="absolute top-4 right-4 p-1 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
                <h3 class="text-lg font-bold text-slate-800 mb-4">Upravit aktivitu</h3>
                <input type="hidden" id="edit-act-id">
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Typ</label>
                        <select id="edit-act-type" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                            <option value="call">Telefonát</option>
                            <option value="email">E-mail</option>
                            <option value="meeting">Schůzka</option>
                            <option value="note">Poznámka</option>
                            <option value="booking_confirmation">Potvrzení termínu</option>
                        </select>
                    </div>
                    <div id="edit-direction-field" class="hidden">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Směr</label>
                        <select id="edit-act-direction" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                            <option value="out">Odchozí</option>
                            <option value="in">Příchozí</option>
                        </select>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Předmět</label>
                        <input type="text" id="edit-act-subject" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-700 mb-1">Obsah / poznámka</label>
                        <textarea id="edit-act-body" rows="4" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm"></textarea>
                    </div>
                </div>
                <div class="flex gap-2 mt-6">
                    <button type="button" onclick="saveEditActivity()" class="px-6 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 font-medium text-sm transition-colors">
                        Uložit změny
                    </button>
                    <button type="button" onclick="closeEditActivity()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-lg text-sm font-medium transition-colors">
                        Zrušit
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══ Delete Activity Confirm Modal ═══ -->
    <div id="delete-activity-modal" class="fixed inset-0 z-50 hidden">
        <div class="modal-backdrop absolute inset-0" onclick="closeDeleteModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-sm p-6 pointer-events-auto relative">
                <h3 class="text-lg font-bold text-slate-800 mb-2">Smazat aktivitu</h3>
                <p class="text-slate-600 text-sm mb-6">Opravdu chcete tuto aktivitu smazat? Tato akce je nevratná.</p>
                <input type="hidden" id="delete-act-id">
                <div class="flex gap-2 justify-end">
                    <button type="button" onclick="closeDeleteModal()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-lg text-sm font-medium transition-colors">
                        Zrušit
                    </button>
                    <button type="button" onclick="executeDeleteActivity()" class="px-4 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 text-sm font-medium transition-colors">
                        Smazat
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══ Merge Modal ═══ -->
    <div id="merge-modal" class="fixed inset-0 z-50 hidden">
        <div class="modal-backdrop absolute inset-0" onclick="closeMergeModal()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-2xl p-6 pointer-events-auto relative max-h-[90vh] flex flex-col">
                <button type="button" onclick="closeMergeModal()" class="absolute top-4 right-4 p-1 text-slate-400 hover:text-slate-600 rounded-lg hover:bg-slate-100">
                    <i data-lucide="x" class="w-5 h-5"></i>
                </button>
                <h3 class="text-lg font-bold text-slate-800 mb-2">Sloučit kontakty</h3>
                <p class="text-slate-600 text-sm mb-4">
                    Sloučíte vybraný kontakt do <strong><?= htmlspecialchars($contact['name']) ?></strong>.
                    Všechny rezervace a aktivity budou přesunuty, duplicitní kontakt bude smazán.
                </p>
                <div class="mb-4">
                    <input type="text" id="merge-search" placeholder="Hledat kontakt podle jména, e-mailu nebo telefonu..."
                        class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                        oninput="searchMergeContacts()">
                </div>
                <div id="merge-results" class="flex-1 overflow-y-auto space-y-2 min-h-[100px]">
                    <p class="text-slate-500 text-sm text-center py-8">Začněte psát pro vyhledání kontaktu...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══ Merge Confirm Modal ═══ -->
    <div id="merge-confirm-modal" class="fixed inset-0 z-[60] hidden">
        <div class="modal-backdrop absolute inset-0" onclick="closeMergeConfirm()"></div>
        <div class="absolute inset-0 flex items-center justify-center p-4 pointer-events-none">
            <div class="bg-white rounded-xl shadow-xl w-full max-w-md p-6 pointer-events-auto relative">
                <h3 class="text-lg font-bold text-slate-800 mb-2">Potvrdit sloučení</h3>
                <p class="text-slate-600 text-sm mb-4">
                    Opravdu chcete sloučit kontakt <strong id="merge-confirm-name"></strong> do <strong><?= htmlspecialchars($contact['name']) ?></strong>?
                </p>
                <p class="text-amber-700 text-sm bg-amber-50 px-3 py-2 rounded-lg mb-4">
                    <i data-lucide="alert-triangle" class="w-4 h-4 inline mr-1"></i>
                    Všechny rezervace a aktivity budou přesunuty. Sloučený kontakt bude smazán. Tuto akci nelze vrátit.
                </p>
                <input type="hidden" id="merge-confirm-id">
                <div class="flex gap-2 justify-end">
                    <button type="button" onclick="closeMergeConfirm()" class="px-4 py-2 bg-slate-100 hover:bg-slate-200 rounded-lg text-sm font-medium transition-colors">
                        Zrušit
                    </button>
                    <button type="button" onclick="executeMerge()" class="px-4 py-2 bg-amber-600 text-white rounded-lg hover:bg-amber-700 text-sm font-medium transition-colors">
                        Sloučit kontakty
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- ═══ Toast container ═══ -->
    <div id="toast-container" class="fixed top-4 right-4 z-[70] space-y-2"></div>

<?php include __DIR__ . '/includes/layout-end.php'; ?>

    <script>
        lucide.createIcons();
        const contactId = <?= (int)$id ?>;

        // ── Toast notification ──
        function showToast(msg, type) {
            const container = document.getElementById('toast-container');
            const el = document.createElement('div');
            const colors = type === 'success' ? 'bg-green-600' : (type === 'error' ? 'bg-red-600' : 'bg-slate-700');
            el.className = 'toast px-4 py-2.5 rounded-lg text-white text-sm font-medium shadow-lg ' + colors;
            el.textContent = msg;
            container.appendChild(el);
            setTimeout(() => el.remove(), 3200);
        }

        // ── POST helper ──
        function postAction(data) {
            return fetch('contact.php?id=' + contactId, {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams(data).toString(),
                cache: 'no-store'
            }).then(r => r.json());
        }

        // ═══ EDIT CONTACT ═══
        function openEditContact() {
            document.getElementById('contact-edit-form').classList.remove('hidden');
            const nr = document.getElementById('notes-readonly');
            if (nr) nr.classList.add('hidden');
            document.getElementById('edit-name').focus();
            document.getElementById('edit-name').select();
        }
        function cancelEditContact() {
            document.getElementById('contact-edit-form').classList.add('hidden');
            const nr = document.getElementById('notes-readonly');
            if (nr) nr.classList.remove('hidden');
        }
        function saveContact() {
            const name = document.getElementById('edit-name').value.trim();
            const email = document.getElementById('edit-email').value.trim();
            const phone = document.getElementById('edit-phone').value.trim();
            const notes = document.getElementById('edit-notes').value.trim();
            if (!name || !email) { showToast('Jméno a e-mail jsou povinné.', 'error'); return; }
            postAction({ action: 'edit_contact', name, email, phone, notes }).then(r => {
                if (r.success) {
                    showToast('Kontakt uložen.', 'success');
                    setTimeout(() => location.reload(), 500);
                } else {
                    showToast(r.error || 'Chyba při ukládání.', 'error');
                }
            }).catch(() => showToast('Chyba připojení.', 'error'));
        }

        // ═══ ADD ACTIVITY ═══
        function toggleAddActivity() {
            const form = document.getElementById('add-activity-form');
            form.classList.toggle('hidden');
            if (!form.classList.contains('hidden')) {
                document.getElementById('add-act-subject').focus();
            }
        }

        document.getElementById('add-act-type').addEventListener('change', function() {
            document.getElementById('add-direction-field').classList.toggle('hidden', !['call','email'].includes(this.value));
        });

        function saveActivity() {
            const type = document.getElementById('add-act-type').value;
            const subject = document.getElementById('add-act-subject').value.trim();
            const body = document.getElementById('add-act-body').value.trim();
            const direction = document.getElementById('add-act-direction').value;
            postAction({ action: 'add_activity', type, subject, body, direction }).then(r => {
                if (r.success) {
                    showToast('Aktivita přidána.', 'success');
                    setTimeout(() => location.reload(), 500);
                } else {
                    showToast(r.error || 'Chyba.', 'error');
                }
            }).catch(() => showToast('Chyba připojení.', 'error'));
        }

        // ═══ EDIT ACTIVITY ═══
        function openEditActivity(actId) {
            const item = document.querySelector('.activity-item[data-id="'+actId+'"]');
            if (!item) return;
            document.getElementById('edit-act-id').value = actId;
            document.getElementById('edit-act-type').value = item.dataset.type;
            document.getElementById('edit-act-subject').value = item.dataset.subject;
            document.getElementById('edit-act-body').value = item.dataset.body;
            document.getElementById('edit-act-direction').value = item.dataset.direction || 'out';
            toggleEditDirection();
            document.getElementById('edit-activity-modal').classList.remove('hidden');
        }

        document.getElementById('edit-act-type').addEventListener('change', toggleEditDirection);
        function toggleEditDirection() {
            const t = document.getElementById('edit-act-type').value;
            document.getElementById('edit-direction-field').classList.toggle('hidden', !['call','email'].includes(t));
        }

        function closeEditActivity() {
            document.getElementById('edit-activity-modal').classList.add('hidden');
        }

        function saveEditActivity() {
            const actId = document.getElementById('edit-act-id').value;
            const type = document.getElementById('edit-act-type').value;
            const subject = document.getElementById('edit-act-subject').value.trim();
            const body = document.getElementById('edit-act-body').value.trim();
            const direction = document.getElementById('edit-act-direction').value;
            postAction({ action: 'edit_activity', activity_id: actId, type, subject, body, direction }).then(r => {
                if (r.success) {
                    showToast('Aktivita upravena.', 'success');
                    closeEditActivity();
                    setTimeout(() => location.reload(), 500);
                } else {
                    showToast(r.error || 'Chyba.', 'error');
                }
            }).catch(() => showToast('Chyba připojení.', 'error'));
        }

        // ═══ DELETE ACTIVITY ═══
        function confirmDeleteActivity(actId) {
            document.getElementById('delete-act-id').value = actId;
            document.getElementById('delete-activity-modal').classList.remove('hidden');
        }
        function closeDeleteModal() {
            document.getElementById('delete-activity-modal').classList.add('hidden');
        }
        function executeDeleteActivity() {
            const actId = document.getElementById('delete-act-id').value;
            postAction({ action: 'delete_activity', activity_id: actId }).then(r => {
                if (r.success) {
                    showToast('Aktivita smazána.', 'success');
                    closeDeleteModal();
                    const item = document.querySelector('.activity-item[data-id="'+actId+'"]');
                    if (item) item.remove();
                    if (!document.querySelector('.activity-item')) {
                        document.getElementById('activity-list').innerHTML = '<p class="text-slate-500 text-sm">Zatím žádné aktivity.</p>';
                    }
                } else {
                    showToast(r.error || 'Chyba.', 'error');
                }
            }).catch(() => showToast('Chyba připojení.', 'error'));
        }

        // ═══ MERGE CONTACTS ═══
        let mergeSearchTimeout = null;
        function openMergeModal() {
            document.getElementById('merge-modal').classList.remove('hidden');
            document.getElementById('merge-search').value = '';
            document.getElementById('merge-results').innerHTML = '<p class="text-slate-500 text-sm text-center py-8">Začněte psát pro vyhledání kontaktu...</p>';
            setTimeout(() => document.getElementById('merge-search').focus(), 100);
        }
        function closeMergeModal() {
            document.getElementById('merge-modal').classList.add('hidden');
        }

        function searchMergeContacts() {
            clearTimeout(mergeSearchTimeout);
            const q = document.getElementById('merge-search').value.trim();
            if (q.length < 1) {
                document.getElementById('merge-results').innerHTML = '<p class="text-slate-500 text-sm text-center py-8">Začněte psát pro vyhledání kontaktu...</p>';
                return;
            }
            mergeSearchTimeout = setTimeout(() => {
                postAction({ action: 'search_contacts', q }).then(r => {
                    if (!r.success) return;
                    const results = document.getElementById('merge-results');
                    if (!r.contacts.length) {
                        results.innerHTML = '<p class="text-slate-500 text-sm text-center py-8">Žádné výsledky.</p>';
                        return;
                    }
                    results.innerHTML = r.contacts.map(c => {
                        const sourceLabel = c.source === 'booking' ? 'Rezervace' : 'Kontakt';
                        const sourceClass = c.source === 'booking' ? 'bg-teal-100 text-teal-700' : 'bg-slate-100 text-slate-600';
                        return '<div class="flex items-center justify-between p-3 border border-slate-200 rounded-lg hover:bg-slate-50 cursor-pointer" onclick="prepareMerge(' + c.id + ', \'' + escHtml(c.name) + '\')">' +
                            '<div><div class="font-medium text-slate-800">' + escHtml(c.name) + '</div>' +
                            '<div class="text-sm text-slate-500">' + escHtml(c.email) + (c.phone ? ' · ' + escHtml(c.phone) : '') + '</div>' +
                            '<div class="flex gap-2 mt-1"><span class="px-2 py-0.5 rounded text-xs font-medium ' + sourceClass + '">' + sourceLabel + '</span>' +
                            '<span class="text-xs text-slate-400">' + c.booking_count + ' rez., ' + c.activity_count + ' akt.</span></div></div>' +
                            '<span class="text-teal-600 text-sm font-medium flex-shrink-0 ml-4">Sloučit →</span></div>';
                    }).join('');
                });
            }, 300);
        }

        function escHtml(s) {
            const d = document.createElement('div');
            d.textContent = s || '';
            return d.innerHTML;
        }

        function prepareMerge(mergeId, name) {
            document.getElementById('merge-confirm-id').value = mergeId;
            document.getElementById('merge-confirm-name').textContent = name;
            document.getElementById('merge-confirm-modal').classList.remove('hidden');
        }
        function closeMergeConfirm() {
            document.getElementById('merge-confirm-modal').classList.add('hidden');
        }
        function executeMerge() {
            const mergeId = document.getElementById('merge-confirm-id').value;
            postAction({ action: 'merge_contacts', merge_id: mergeId }).then(r => {
                if (r.success) {
                    showToast('Kontakty sloučeny.', 'success');
                    closeMergeConfirm();
                    closeMergeModal();
                    setTimeout(() => location.reload(), 800);
                } else {
                    showToast(r.error || 'Chyba při slučování.', 'error');
                }
            }).catch(() => showToast('Chyba připojení.', 'error'));
        }
    </script>
</body>
</html>
