<?php
session_start();
if (!isset($_SESSION['walance_admin'])) {
    header('Location: index.php');
    exit;
}
require_once __DIR__ . '/../api/config.php';
require_once __DIR__ . '/../api/availability.php';

$v = defined('APP_VERSION') ? APP_VERSION : '1.0.0';
$settings = getAvailabilitySettings();
$saved = false;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $slotStart = (int)($_POST['slot_start'] ?? 9);
    $slotEnd = (int)($_POST['slot_end'] ?? 17);
    $slotInterval = (int)($_POST['slot_interval'] ?? 30);
    $workDays = isset($_POST['work_days']) ? array_map('intval', (array)$_POST['work_days']) : [1,2,3,4,5];
    $excludedDates = trim($_POST['excluded_dates'] ?? '');
    
    if ($slotStart < 0 || $slotStart > 23) $slotStart = 9;
    if ($slotEnd < 1 || $slotEnd > 24) $slotEnd = 17;
    if ($slotEnd <= $slotStart) $slotEnd = $slotStart + 1;
    if (!in_array($slotInterval, [15, 30, 60])) $slotInterval = 30;
    
    $data = [
        'slot_start' => $slotStart,
        'slot_end' => $slotEnd,
        'slot_interval' => $slotInterval,
        'work_days' => $workDays,
        'excluded_dates' => array_filter(array_map('trim', explode("\n", $excludedDates))),
    ];
    if (saveAvailabilitySettings($data)) {
        $saved = true;
        $settings = getAvailabilitySettings();
    } else {
        $error = 'Nepodařilo se uložit. Zkontrolujte oprávnění složky data/.';
    }
}

$dayNames = ['Ne', 'Po', 'Út', 'St', 'Čt', 'Pá', 'So'];
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WALANCE CRM - Dostupnost</title>
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
                <a href="bookings.php" class="text-slate-500 hover:text-teal-600 text-sm">Rezervace</a>
                <a href="calendar.php" class="text-slate-500 hover:text-teal-600 text-sm">Kalendář</a>
                <a href="availability.php" class="text-teal-600 font-medium text-sm">Dostupnost</a>
                <a href="../" class="text-slate-500 hover:text-teal-600 text-sm">Web</a>
                <span class="text-slate-500 text-sm"><?= htmlspecialchars($_SESSION['walance_admin_name'] ?? 'Admin') ?></span>
                <a href="logout.php" class="text-red-600 hover:text-red-700 text-sm font-medium">Odhlásit</a>
            </nav>
        </div>
    </header>

    <main class="max-w-2xl mx-auto p-6">
        <a href="dashboard.php" class="inline-flex items-center text-slate-600 hover:text-teal-600 text-sm mb-6">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Zpět
        </a>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-bold text-slate-800 mb-6">Nastavení dostupnosti</h2>
            <p class="text-slate-600 text-sm mb-6">Určuje, kdy jsou zobrazeny sloty pro rezervaci na webu. Google Calendar blokuje další časy.</p>
            
            <?php if ($saved): ?>
            <div class="mb-4 p-3 bg-green-50 text-green-800 rounded-lg text-sm">Nastavení uloženo.</div>
            <?php endif; ?>
            <?php if ($error): ?>
            <div class="mb-4 p-3 bg-red-50 text-red-800 rounded-lg text-sm"><?= htmlspecialchars($error) ?></div>
            <?php endif; ?>

            <form method="POST" class="space-y-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Pracovní doba</label>
                    <div class="flex items-center gap-4">
                        <div class="flex items-center gap-2">
                            <span class="text-slate-500 text-sm">Od</span>
                            <input type="number" name="slot_start" min="0" max="23" value="<?= (int)$settings['slot_start'] ?>" class="w-16 px-2 py-1.5 border border-slate-300 rounded-lg">
                            <span class="text-slate-500 text-sm">hod</span>
                        </div>
                        <div class="flex items-center gap-2">
                            <span class="text-slate-500 text-sm">Do</span>
                            <input type="number" name="slot_end" min="1" max="24" value="<?= (int)$settings['slot_end'] ?>" class="w-16 px-2 py-1.5 border border-slate-300 rounded-lg">
                            <span class="text-slate-500 text-sm">hod</span>
                        </div>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Interval slotů</label>
                    <select name="slot_interval" class="px-4 py-2 border border-slate-300 rounded-lg">
                        <option value="15" <?= ($settings['slot_interval'] ?? 30) == 15 ? 'selected' : '' ?>>15 minut</option>
                        <option value="30" <?= ($settings['slot_interval'] ?? 30) == 30 ? 'selected' : '' ?>>30 minut</option>
                        <option value="60" <?= ($settings['slot_interval'] ?? 30) == 60 ? 'selected' : '' ?>>60 minut</option>
                    </select>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Pracovní dny</label>
                    <div class="flex flex-wrap gap-4">
                        <?php for ($d = 0; $d <= 6; $d++): ?>
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="work_days[]" value="<?= $d ?>" <?= in_array($d, $settings['work_days'] ?? [1,2,3,4,5]) ? 'checked' : '' ?> class="rounded border-slate-300">
                            <span class="text-sm"><?= $dayNames[$d] ?></span>
                        </label>
                        <?php endfor; ?>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Výjimky – blokované dny (YYYY-MM-DD, jeden na řádek)</label>
                    <textarea name="excluded_dates" rows="4" class="w-full px-3 py-2 border border-slate-300 rounded-lg text-sm font-mono" placeholder="2026-12-24&#10;2026-12-31"><?= htmlspecialchars(implode("\n", $settings['excluded_dates'] ?? [])) ?></textarea>
                </div>

                <button type="submit" class="px-6 py-2.5 bg-teal-600 text-white rounded-lg hover:bg-teal-700 font-medium">
                    Uložit nastavení
                </button>
            </form>
        </div>

        <?php if (GOOGLE_CALENDAR_ENABLED): ?>
        <div class="mt-6 p-4 bg-teal-50 rounded-lg text-sm text-teal-800">
            <strong>Google Calendar</strong> je napojen – události z kalendáře automaticky blokují sloty.
        </div>
        <?php else: ?>
        <div class="mt-6 p-4 bg-amber-50 rounded-lg text-sm text-amber-800">
            Google Calendar není nakonfigurován. Pro blokování časů z kalendáře nahrajte <code class="bg-amber-100 px-1 rounded">api/credentials/google-calendar.json</code>.
        </div>
        <?php endif; ?>
    </main>

    <footer class="max-w-2xl mx-auto px-6 py-4 text-center text-slate-400 text-xs">
        v<?= htmlspecialchars($v) ?>
    </footer>

    <script>lucide.createIcons();</script>
</body>
</html>
