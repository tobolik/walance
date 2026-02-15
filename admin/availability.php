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
    $slotInterval = (int)($_POST['slot_interval'] ?? 30);
    $workDays = isset($_POST['work_days']) ? array_map('intval', (array)$_POST['work_days']) : [1,2,3,4,5];
    $excludedDates = trim($_POST['excluded_dates'] ?? '');
    $manualIds = trim($_POST['google_calendar_id_manual'] ?? '');
    $googleCalendarIds = [];
    if ($manualIds !== '') {
        $googleCalendarIds = preg_split('/[\s,]+/', $manualIds, -1, PREG_SPLIT_NO_EMPTY);
        $googleCalendarIds = array_values(array_filter(array_map('trim', $googleCalendarIds)));
    }
    if (empty($googleCalendarIds)) {
        $single = trim($_POST['google_calendar_id'] ?? '');
        if ($single !== '') $googleCalendarIds = [$single];
    }
    
    $slotRanges = [];
    $starts = (array)($_POST['slot_range_start'] ?? []);
    $ends = (array)($_POST['slot_range_end'] ?? []);
    foreach ($starts as $i => $s) {
        $s = (int)$s;
        $e = (int)($ends[$i] ?? $s);
        if ($s < 0 || $s > 23) $s = 9;
        if ($e < 1 || $e > 24) $e = 17;
        if ($e <= $s) $e = $s + 1;
        $slotRanges[] = [(string)$s, (string)$e];
    }
    if (empty($slotRanges)) {
        $slotRanges = [['9', '17']];
    }
    
    // Kontrola kolizí – rozsahy se nesmí překrývat
    for ($i = 0; $i < count($slotRanges); $i++) {
        for ($j = $i + 1; $j < count($slotRanges); $j++) {
            $a1 = (int)$slotRanges[$i][0];
            $b1 = (int)$slotRanges[$i][1];
            $a2 = (int)$slotRanges[$j][0];
            $b2 = (int)$slotRanges[$j][1];
            if ($a1 < $b2 && $a2 < $b1) {
                $error = "Rozsahy se překrývají: {$slotRanges[$i][0]}-{$slotRanges[$i][1]} a {$slotRanges[$j][0]}-{$slotRanges[$j][1]} hod. Nastavte nesouvisející intervaly (např. 9–12 a 15–19).";
                break 2;
            }
        }
    }
    
    if (!in_array($slotInterval, [15, 30, 60])) $slotInterval = 30;
    
    if (empty($error)) {
        $data = [
            'slot_start' => (int)($slotRanges[0][0] ?? 9),
            'slot_end' => (int)($slotRanges[count($slotRanges) - 1][1] ?? 17),
            'slot_ranges' => $slotRanges,
            'slot_interval' => $slotInterval,
            'work_days' => $workDays,
            'excluded_dates' => array_filter(array_map('trim', explode("\n", $excludedDates))),
            'google_calendar_ids' => $googleCalendarIds,
        ];
        if (saveAvailabilitySettings($data)) {
            $saved = true;
            $settings = getAvailabilitySettings();
            if (!empty($googleCalendarIds) && GOOGLE_CALENDAR_ENABLED && file_exists(__DIR__ . '/../api/GoogleCalendar.php')) {
                try {
                    require_once __DIR__ . '/../api/GoogleCalendar.php';
                    $gc = new GoogleCalendar();
                    foreach ($googleCalendarIds as $cid) {
                        $gc->addCalendarToList($cid);
                    }
                } catch (Exception $e) {}
            }
        } else {
            $error = 'Nepodařilo se uložit. Zkontrolujte oprávnění složky data/.';
        }
    }
}

$dayNames = ['Ne', 'Po', 'Út', 'St', 'Čt', 'Pá', 'So'];
if (!($error && $_SERVER['REQUEST_METHOD'] === 'POST')) {
    $slotRanges = $settings['slot_ranges'] ?? [];
    if (empty($slotRanges)) {
        $slotRanges = [[(string)($settings['slot_start'] ?? 9), (string)($settings['slot_end'] ?? 17)]];
    }
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>WALANCE CRM - Dostupnost</title>
    <script src="https://cdn.tailwindcss.com?v=<?= htmlspecialchars($v) ?>"></script>
    <script src="https://unpkg.com/lucide@latest?v=<?= htmlspecialchars($v) ?>"></script>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;600;700&display=swap?v=<?= htmlspecialchars($v) ?>" rel="stylesheet">
    <style>body { font-family: 'DM Sans', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen">
<?php $adminCurrentPage = 'availability'; include __DIR__ . '/includes/layout.php'; ?>
    <div class="p-6 max-w-2xl">
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

            <?php
            $calendarList = [];
            if (GOOGLE_CALENDAR_ENABLED) {
                try {
                    require_once __DIR__ . '/../api/GoogleCalendar.php';
                    $gc = new GoogleCalendar();
                    $calendarList = $gc->getCalendarList();
                } catch (Exception $e) {
                    $calendarList = ['error' => $e->getMessage()];
                }
            }
            $hasCalendarList = is_array($calendarList) && !isset($calendarList['error']);
            ?>
            <form method="POST" class="space-y-6">
                <?php if (GOOGLE_CALENDAR_ENABLED): ?>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Google Calendar</label>
                    <?php
                    $calIds = $settings['google_calendar_ids'] ?? [];
                    if (empty($calIds) && !empty($settings['google_calendar_id'])) $calIds = [$settings['google_calendar_id']];
                    $calIdsStr = implode("\n", $calIds);
                    ?>
                    <?php if ($hasCalendarList): ?>
                    <select name="google_calendar_id" id="gc-select" class="w-full px-4 py-2 border border-slate-300 rounded-lg mb-2">
                        <option value="">— Přidat z výběru —</option>
                        <?php foreach ($calendarList as $cal): ?>
                        <option value="<?= htmlspecialchars($cal['id']) ?>"><?= htmlspecialchars($cal['summary']) ?><?= !empty($cal['primary']) ? ' (primární)' : '' ?></option>
                        <?php endforeach; ?>
                    </select>
                    <?php else: ?>
                    <?php if (isset($calendarList['error'])): ?>
                    <p class="text-amber-600 text-xs mt-1 mb-2">Seznam nelze načíst: <?= htmlspecialchars($calendarList['error']) ?>. Zadejte ID ručně.</p>
                    <?php endif; ?>
                    <?php endif; ?>
                    <p class="text-slate-600 text-sm font-medium mb-1">Calendar ID (jeden na řádek nebo oddělené čárkou) – můžete kombinovat více kalendářů:</p>
                    <textarea name="google_calendar_id_manual" id="gc-manual" rows="3" placeholder="vase@gmail.com&#10;xxx@group.calendar.google.com" class="w-full px-4 py-2 border border-slate-300 rounded-lg font-mono text-sm"><?= htmlspecialchars($calIdsStr) ?></textarea>
                    <p class="text-slate-500 text-xs mt-1">E-mail vašeho Google účtu = váš primární kalendář. Nebo: Google Calendar → Nastavení → Váš kalendář → Integrace kalendáře → Zkopírujte ID. Rezervace se zapisují do prvního kalendáře.</p>
                </div>
                <?php endif; ?>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-2">Pracovní doba</label>
                    <p class="text-slate-500 text-xs mb-2">Můžete přidat více rozsahů (např. 9–12 a 15–19).</p>
                    <div id="slot-ranges-container" class="space-y-2">
                        <?php foreach ($slotRanges as $i => $r): ?>
                        <div class="slot-range-row flex items-center gap-2">
                            <span class="text-slate-500 text-sm">Od</span>
                            <input type="number" name="slot_range_start[]" min="0" max="23" value="<?= (int)($r[0] ?? 9) ?>" class="w-16 px-2 py-1.5 border border-slate-300 rounded-lg">
                            <span class="text-slate-500 text-sm">hod</span>
                            <span class="text-slate-500 text-sm">Do</span>
                            <input type="number" name="slot_range_end[]" min="1" max="24" value="<?= (int)($r[1] ?? 17) ?>" class="w-16 px-2 py-1.5 border border-slate-300 rounded-lg">
                            <span class="text-slate-500 text-sm">hod</span>
                            <button type="button" class="slot-range-remove p-1.5 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded" title="Odebrat rozsah">
                                <i data-lucide="trash-2" class="w-4 h-4"></i>
                            </button>
                        </div>
                        <?php endforeach; ?>
                    </div>
                    <button type="button" id="slot-range-add" class="mt-2 text-sm text-teal-600 hover:text-teal-700 font-medium flex items-center gap-1">
                        <i data-lucide="plus" class="w-4 h-4"></i> Přidat rozsah
                    </button>
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
        <?php
        $gcEvents = ['items' => []];
        $calendarIds = getCalendarIds();
        $gcCalId = $calendarIds[0] ?? null;
        $serviceAccountEmail = null;
        if (file_exists(__DIR__ . '/../api/credentials/google-calendar.json')) {
            $creds = json_decode(file_get_contents(__DIR__ . '/../api/credentials/google-calendar.json'), true);
            $serviceAccountEmail = $creds['client_email'] ?? null;
        }
        $effectiveCalId = $gcCalId ?: (defined('GOOGLE_CALENDAR_ID') ? GOOGLE_CALENDAR_ID : 'primary');
        try {
            require_once __DIR__ . '/../api/GoogleCalendar.php';
            $gc = new GoogleCalendar($gcCalId);
            $ids = !empty($calendarIds) ? $calendarIds : null;
            $gcEvents = $gc->getEventsForDisplay(date('Y-m-d'), 14, $ids);
            $effectiveCalId = $gc->getCalendarId();
        } catch (Exception $e) {
            $gcEvents = ['error' => $e->getMessage(), 'items' => []];
        }
        ?>
        <div class="mt-6 p-4 bg-teal-50 rounded-lg text-sm text-teal-800">
            <strong>Google Calendar</strong> je napojen – události z kalendáře automaticky blokují sloty.
        </div>
        <details class="mt-4 bg-white rounded-xl shadow-sm overflow-hidden">
            <summary class="p-4 cursor-pointer font-medium text-slate-800 hover:bg-slate-50">Události z kalendáře (kontrola)</summary>
            <div class="p-4 border-t border-slate-100">
                <?php if (!empty($gcEvents['error'])): ?>
                <p class="text-red-600 text-sm">Chyba: <?= htmlspecialchars($gcEvents['error']) ?></p>
                <?php elseif (empty($gcEvents['items'])): ?>
                <p class="text-slate-600 font-medium mb-3">Žádné události v příštích 14 dnech.</p>
                <p class="text-slate-600 text-sm mb-2"><?= count($calendarIds) > 1 ? 'Kalendáře' : 'Kalendář' ?>: <code class="bg-slate-100 px-1 rounded"><?= htmlspecialchars(implode(', ', !empty($calendarIds) ? $calendarIds : [$effectiveCalId ?? '?'])) ?></code></p>
                <?php if ($effectiveCalId === 'primary'): ?>
                <div class="bg-amber-50 border border-amber-200 rounded-lg p-3 text-sm text-amber-800 mb-3">
                    <strong>Problém:</strong> „primary“ = kalendář Service Accountu (prázdný). Musíte použít <strong>váš</strong> kalendář s událostmi.
                </div>
                <?php endif; ?>
                <p class="text-slate-600 text-sm font-medium mb-1">Postup:</p>
                <ol class="list-decimal list-inside text-sm text-slate-600 space-y-1 mb-3">
                    <li>V Google Calendar (calendar.google.com) otevřete <strong>nastavení kalendáře</strong> (ikona ozubeného kolečka u vašeho kalendáře).</li>
                    <li>V sekci „Sdílet s konkrétními lidmi“ přidejte e-mail: <code class="bg-slate-100 px-1 rounded"><?= htmlspecialchars($serviceAccountEmail ?? 'client_email z google-calendar.json') ?></code> s oprávněním „Zobrazit všechny podrobnosti události“.</li>
                    <li>V poli „Zadejte Calendar ID ručně“ zadejte <strong>e-mail vašeho Google účtu</strong> (např. jana@gmail.com) – to je ID vašeho primárního kalendáře. Nebo z Google Calendar: Nastavení → Váš kalendář → Integrace kalendáře → zkopírujte ID.</li>
                    <li>Uložte nastavení a obnovte stránku.</li>
                </ol>
                <?php else: ?>
                <ul class="space-y-2 text-sm">
                    <?php foreach ($gcEvents['items'] as $ev): ?>
                    <li class="flex gap-4">
                        <span class="font-mono text-slate-600"><?= htmlspecialchars($ev['date']) ?> <?= htmlspecialchars($ev['start']) ?>–<?= htmlspecialchars($ev['end']) ?></span>
                        <span class="font-medium"><?= htmlspecialchars($ev['summary']) ?></span>
                    </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
        </details>
        <?php else: ?>
        <div class="mt-6 p-4 bg-amber-50 rounded-lg text-sm text-amber-800">
            Google Calendar není nakonfigurován. Pro blokování časů z kalendáře nahrajte <code class="bg-amber-100 px-1 rounded">api/credentials/google-calendar.json</code>.
        </div>
        <?php endif; ?>

        <div class="mt-8 bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-bold text-slate-800 mb-2">Blokovat konkrétní časy</h2>
            <p class="text-slate-600 text-sm mb-6">Klikněte na den v kalendáři, pak na časový slot. Klik na volný slot = blokovat (nedostupný). Klik na blokovaný = odblokovat.</p>
            <div class="flex items-center justify-between mb-4">
                <button type="button" id="block-cal-prev" class="p-2 rounded-lg hover:bg-slate-100 text-slate-600">
                    <i data-lucide="chevron-left" class="w-5 h-5"></i>
                </button>
                <h3 id="block-cal-month" class="font-bold text-slate-800">Únor 2026</h3>
                <button type="button" id="block-cal-next" class="p-2 rounded-lg hover:bg-slate-100 text-slate-600">
                    <i data-lucide="chevron-right" class="w-5 h-5"></i>
                </button>
            </div>
            <div class="grid grid-cols-7 gap-1 text-center text-xs font-medium text-slate-500 mb-2">
                <span>Po</span><span>Út</span><span>St</span><span>Čt</span><span>Pá</span><span class="text-slate-300">So</span><span class="text-slate-300">Ne</span>
            </div>
            <div id="block-cal-grid" class="grid grid-cols-7 gap-1 min-h-[200px]"></div>
            <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-slate-600">
                <span class="flex items-center gap-1"><span class="w-4 h-4 rounded bg-emerald-100"></span> Volné (klik = blokovat)</span>
                <span class="flex items-center gap-1"><span class="w-4 h-4 rounded bg-emerald-100 border-2 border-slate-400"></span> Volné, bylo zamítnuto</span>
                <span class="flex items-center gap-1"><span class="w-4 h-4 rounded bg-amber-200"></span> Čeká na potvrzení</span>
                <span class="flex items-center gap-1"><span class="w-4 h-4 rounded bg-red-200"></span> Blokováno (klik = odblokovat)</span>
                <span class="flex items-center gap-1"><span class="w-4 h-4 rounded bg-slate-200"></span> Obsazeno rezervací</span>
            </div>
        </div>

        <div id="block-slots-panel" class="hidden mt-6 bg-white rounded-xl shadow-sm p-6">
            <p class="text-sm font-medium text-slate-800 mb-2">Časy pro <span id="block-selected-date"></span>:</p>
            <div id="block-slots-list" class="flex flex-wrap gap-2"></div>
        </div>
    </div>
<?php include __DIR__ . '/includes/layout-end.php'; ?>

    <script>
        lucide.createIcons();
        const gcSelect = document.getElementById('gc-select');
        const gcManual = document.getElementById('gc-manual');
        if (gcSelect && gcManual) {
            gcSelect.addEventListener('change', () => {
                const v = gcSelect.value;
                if (!v) return;
                const lines = gcManual.value.split(/[\n,]+/).map(s => s.trim()).filter(Boolean);
                if (!lines.includes(v)) lines.push(v);
                gcManual.value = lines.join('\n');
                gcSelect.selectedIndex = 0;
            });
        }

        document.getElementById('slot-range-add').addEventListener('click', () => {
            const tpl = document.querySelector('.slot-range-row');
            if (!tpl) return;
            const row = tpl.cloneNode(true);
            row.querySelectorAll('input').forEach(inp => { inp.value = inp.name.includes('start') ? '9' : '17'; });
            row.querySelector('.slot-range-remove').addEventListener('click', () => row.remove());
            document.getElementById('slot-ranges-container').appendChild(row);
            lucide.createIcons();
        });
        document.querySelectorAll('.slot-range-remove').forEach(btn => {
            btn.addEventListener('click', () => btn.closest('.slot-range-row').remove());
        });

        const apiBase = '../api';
        let blockCalMonth = new Date().getFullYear() + '-' + String(new Date().getMonth() + 1).padStart(2, '0');
        let blockCalData = { slots_detail: {}, availability: {}, bookings: {} };
        let blockCalSelectedDate = null;

        async function loadBlockCalendar() {
            const grid = document.getElementById('block-cal-grid');
            grid.innerHTML = '<div class="col-span-7 py-8 text-center text-slate-500">Načítám…</div>';
            try {
                const [slotsRes, bookingsRes] = await Promise.all([
                    fetch(apiBase + '/slots.php?month=' + blockCalMonth, { cache: 'no-store' }),
                    fetch(apiBase + '/calendar-bookings.php?month=' + blockCalMonth, { cache: 'no-store', credentials: 'same-origin' })
                ]);
                const data = await slotsRes.json();
                blockCalData = { ...data, bookings: {} };
                if (bookingsRes.ok) {
                    const bookings = await bookingsRes.json();
                    blockCalData.bookings = bookings;
                }
                renderBlockCalendar();
            } catch (err) {
                grid.innerHTML = '<div class="col-span-7 py-8 text-center text-slate-500">Chyba načtení</div>';
            }
        }

        function renderBlockCalendar() {
            const [y, m] = blockCalMonth.split('-').map(Number);
            const first = new Date(y, m - 1, 1);
            const last = new Date(y, m - 1 + 1, 0);
            const daysInMonth = last.getDate();
            const startOffset = (first.getDay() + 6) % 7;
            const monthNames = ['Leden','Únor','Březen','Duben','Květen','Červen','Červenec','Srpen','Září','Říjen','Listopad','Prosinec'];
            document.getElementById('block-cal-month').textContent = monthNames[m - 1] + ' ' + y;
            const grid = document.getElementById('block-cal-grid');
            grid.innerHTML = '';
            const today = new Date().toISOString().slice(0, 10);
            for (let i = 0; i < startOffset; i++) grid.innerHTML += '<div class="aspect-square"></div>';
            for (let d = 1; d <= daysInMonth; d++) {
                const dateStr = y + '-' + String(m).padStart(2, '0') + '-' + String(d).padStart(2, '0');
                const dayOfWeek = new Date(y, m - 1, d).getDay();
                const isWeekend = dayOfWeek === 0 || dayOfWeek === 6;
                const isPast = dateStr < today;
                const avail = blockCalData.availability?.[dateStr];
                const hasSlots = avail && (avail.free > 0 || avail.pending > 0 || avail.confirmed > 0);
                const slotsDetail = blockCalData.slots_detail?.[dateStr];
                const hasBlocked = slotsDetail && Object.values(slotsDetail).includes('blocked');
                const hasPending = slotsDetail && Object.values(slotsDetail).includes('pending');
                let bg = 'bg-slate-100';
                if (!isWeekend && !isPast && slotsDetail) {
                    if (hasBlocked) bg = 'bg-red-100 hover:bg-red-200';
                    else if (hasPending) bg = 'bg-amber-50 hover:bg-amber-100';
                    else if (hasSlots) bg = 'bg-emerald-50 hover:bg-emerald-100';
                    else bg = 'bg-slate-50 hover:bg-slate-100';
                }
                if (isWeekend) bg = 'bg-slate-50';
                if (isPast) bg = 'bg-slate-100 opacity-60';
                const clickable = !isWeekend && !isPast && slotsDetail ? 'cursor-pointer' : 'cursor-default';
                const selected = dateStr === blockCalSelectedDate ? ' ring-2 ring-slate-800 ring-offset-2' : '';
                grid.innerHTML += `<div class="aspect-square rounded-lg flex items-center justify-center text-sm font-medium ${bg} ${clickable} min-h-[28px]${selected}" data-date="${dateStr}">${d}</div>`;
            }
            grid.querySelectorAll('[data-date]').forEach(cell => {
                const dateStr = cell.dataset.date;
                const dayOfWeek = new Date(dateStr + 'T12:00:00').getDay();
                const isWeekend = dayOfWeek === 0 || dayOfWeek === 6;
                const isPast = dateStr < today;
                if (!isWeekend && !isPast && blockCalData.slots_detail?.[dateStr]) {
                    cell.addEventListener('click', () => showBlockSlots(dateStr));
                }
            });
            lucide.createIcons();
        }

        function showBlockSlots(dateStr) {
            blockCalSelectedDate = dateStr;
            document.querySelectorAll('#block-cal-grid [data-date]').forEach(cell => {
                cell.classList.toggle('ring-2', cell.dataset.date === dateStr);
                cell.classList.toggle('ring-slate-800', cell.dataset.date === dateStr);
                cell.classList.toggle('ring-offset-2', cell.dataset.date === dateStr);
            });
            const slotsDetail = blockCalData.slots_detail?.[dateStr] || {};
            const allTimes = Object.keys(slotsDetail).sort();
            document.getElementById('block-selected-date').textContent = new Date(dateStr + 'T12:00:00').toLocaleDateString('cs-CZ', { weekday: 'long', day: 'numeric', month: 'long' });
            const list = document.getElementById('block-slots-list');
            list.innerHTML = '';
            list.dataset.date = dateStr;
            const bookingsByTime = blockCalData.bookings?.[dateStr] || {};
            allTimes.forEach(t => {
                const status = slotsDetail[t];
                let bookings = bookingsByTime[t];
                if (bookings && !Array.isArray(bookings)) bookings = [bookings];
                const hasRejected = bookings && bookings.length > 0 && bookings.every(b => b.status === 'cancelled');
                const span = document.createElement('button');
                span.type = 'button';
                span.textContent = t;
                span.dataset.time = t;
                span.className = 'px-4 py-2 rounded-lg text-sm font-medium transition-colors';
                if (status === 'blocked') {
                    span.className += ' bg-red-200 hover:bg-red-300 text-red-900 cursor-pointer';
                    span.title = 'Klik pro odblokování';
                } else if (status === 'pending') {
                    span.className += ' bg-amber-200 text-amber-900 cursor-default';
                    span.disabled = true;
                    span.title = 'Čeká na potvrzení';
                } else if (status === 'confirmed') {
                    span.className += ' bg-slate-200 text-slate-600 cursor-default';
                    span.disabled = true;
                    span.title = 'Obsazeno rezervací';
                } else if (status === 'free' && hasRejected) {
                    span.className += ' bg-emerald-100 hover:bg-emerald-200 text-emerald-800 border-2 border-slate-400 cursor-pointer';
                    span.title = 'Volné (bylo zamítnuto): ' + bookings.map(b => b.name).join(', ');
                } else if (status === 'free') {
                    span.className += ' bg-emerald-100 hover:bg-emerald-200 text-emerald-800 cursor-pointer';
                    span.title = 'Klik pro blokování';
                } else {
                    span.className += ' bg-slate-200 text-slate-600 cursor-default';
                    span.disabled = true;
                    span.title = 'Obsazeno rezervací';
                }
                if (status === 'blocked' || status === 'free') {
                    span.addEventListener('click', () => toggleBlock(dateStr, t, span));
                }
                list.appendChild(span);
            });
            const panel = document.getElementById('block-slots-panel');
            panel.classList.remove('hidden');
            requestAnimationFrame(() => {
                const main = document.querySelector('main');
                if (main && main.scrollHeight > main.clientHeight) {
                    const mr = main.getBoundingClientRect();
                    const pr = panel.getBoundingClientRect();
                    const scrollTarget = main.scrollTop + (pr.top - mr.top) - 24;
                    main.scrollTo({ top: Math.max(0, scrollTarget), behavior: 'smooth' });
                } else {
                    panel.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        }

        async function toggleBlock(dateStr, time, el) {
            el.disabled = true;
            try {
                const fd = new FormData();
                fd.append('date', dateStr);
                fd.append('time', time);
                const r = await fetch(apiBase + '/availability-block.php', { method: 'POST', body: fd, cache: 'no-store' });
                const data = await r.json();
                if (data.success) {
                    blockCalData.slots_detail[dateStr] = blockCalData.slots_detail[dateStr] || {};
                    blockCalData.slots_detail[dateStr][time] = data.blocked ? 'blocked' : 'free';
                    el.className = 'px-4 py-2 rounded-lg text-sm font-medium transition-colors';
                    if (data.blocked) {
                        el.className += ' bg-red-200 hover:bg-red-300 text-red-900 cursor-pointer';
                        el.title = 'Klik pro odblokování';
                    } else {
                        el.className += ' bg-emerald-100 hover:bg-emerald-200 text-emerald-800 cursor-pointer';
                        el.title = 'Klik pro blokování';
                    }
                }
            } catch (e) {}
            el.disabled = false;
        }

        document.getElementById('block-cal-prev').addEventListener('click', () => {
            const [y, m] = blockCalMonth.split('-').map(Number);
            const d = new Date(y, m - 2, 1);
            blockCalMonth = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0');
            loadBlockCalendar();
        });
        document.getElementById('block-cal-next').addEventListener('click', () => {
            const [y, m] = blockCalMonth.split('-').map(Number);
            const d = new Date(y, m, 1);
            blockCalMonth = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0');
            loadBlockCalendar();
        });

        loadBlockCalendar();
    </script>
</body>
</html>
