<?php
session_start();
if (!isset($_SESSION['walance_admin'])) {
    header('Location: index.php');
    exit;
}
require_once __DIR__ . '/../api/config.php';

$v = defined('APP_VERSION') ? APP_VERSION : '1.0.0';
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <meta http-equiv="Pragma" content="no-cache">
    <meta http-equiv="Expires" content="0">
    <title>WALANCE CRM - Kalendář</title>
    <script src="https://cdn.tailwindcss.com?v=<?= htmlspecialchars($v) ?>"></script>
    <script src="https://unpkg.com/lucide@latest?v=<?= htmlspecialchars($v) ?>"></script>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;600;700&display=swap?v=<?= htmlspecialchars($v) ?>" rel="stylesheet">
    <style>body { font-family: 'DM Sans', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen">
<?php $adminCurrentPage = 'calendar'; include __DIR__ . '/includes/layout.php'; ?>
    <div class="p-6 max-w-2xl">
        <a href="dashboard.php" class="inline-flex items-center text-slate-600 hover:text-teal-600 text-sm mb-6">
            <i data-lucide="arrow-left" class="w-4 h-4 mr-1"></i> Zpět
        </a>

        <div class="bg-white rounded-xl shadow-sm p-6">
            <h2 class="text-lg font-bold text-slate-800 mb-2">Kalendář – přehled stavů</h2>
            <p class="text-slate-600 text-sm mb-6">Zelená = volné, oranžová = čeká na potvrzení, tyrkysová = potvrzeno, šedá = zamítnuto (slot volný).</p>

            <div class="flex items-center justify-between mb-4">
                <button type="button" id="cal-prev" class="p-2 rounded-lg hover:bg-slate-100 text-slate-600">
                    <i data-lucide="chevron-left" class="w-5 h-5"></i>
                </button>
                <h3 id="cal-month-title" class="font-bold text-slate-800">Únor 2026</h3>
                <button type="button" id="cal-next" class="p-2 rounded-lg hover:bg-slate-100 text-slate-600">
                    <i data-lucide="chevron-right" class="w-5 h-5"></i>
                </button>
            </div>
            <div class="grid grid-cols-7 gap-1 text-center text-xs font-medium text-slate-500 mb-2">
                <span>Po</span><span>Út</span><span>St</span><span>Čt</span><span>Pá</span><span class="text-slate-300">So</span><span class="text-slate-300">Ne</span>
            </div>
            <div id="cal-grid" class="grid grid-cols-7 gap-1 min-h-[280px]">
                <!-- Dny se vygenerují v JS -->
            </div>
            <div class="mt-3 flex flex-wrap items-center gap-3 text-xs text-slate-600">
                <span class="flex items-center gap-1"><span class="w-4 h-4 rounded bg-emerald-500"></span> Volné</span>
                <span class="flex items-center gap-1"><span class="w-4 h-4 rounded bg-red-300"></span> Blokováno</span>
                <span class="flex items-center gap-1"><span class="w-4 h-4 rounded bg-amber-400"></span> Čeká na potvrzení</span>
                <span class="flex items-center gap-1"><span class="w-4 h-4 rounded bg-teal-500"></span> Potvrzeno</span>
                <span class="flex items-center gap-1"><span class="w-4 h-4 rounded bg-slate-300 border-2 border-slate-500"></span> Zamítnuto (volné)</span>
            </div>
        </div>

        <div id="cal-time-panel" class="hidden mt-6 bg-white rounded-xl shadow-sm p-6">
            <p class="text-sm font-medium text-slate-800 mb-2">Časové sloty pro <span id="cal-selected-date"></span>:</p>
            <div id="cal-time-slots" class="flex flex-wrap gap-2"></div>
        </div>
    </div>

    <!-- Modal pro editaci rezervace -->
    <div id="cal-booking-modal" class="hidden fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50" onclick="if(event.target===this) closeBookingModal()">
        <div class="bg-white rounded-xl shadow-xl max-w-md w-full p-6" onclick="event.stopPropagation()">
            <h3 class="text-lg font-bold text-slate-800 mb-3">Rezervace</h3>
            <div id="cal-booking-modal-body" class="text-sm text-slate-600 space-y-2 mb-4"></div>
            <div class="flex flex-wrap gap-2">
                <button type="button" id="cal-modal-confirm" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-medium rounded-lg hidden">Potvrdit</button>
                <button type="button" id="cal-modal-cancel" class="px-4 py-2 bg-red-600 hover:bg-red-700 text-white text-sm font-medium rounded-lg hidden">Zamítnout</button>
                <button type="button" id="cal-modal-restore" class="px-4 py-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-medium rounded-lg hidden">Zrušit zamítnutí</button>
                <button type="button" onclick="closeBookingModal()" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-medium rounded-lg">Zavřít</button>
            </div>
        </div>
    </div>
<?php include __DIR__ . '/includes/layout-end.php'; ?>

    <script>
        lucide.createIcons();

        const apiBase = '../api';
        let calCurrentMonth = new Date().getFullYear() + '-' + String(new Date().getMonth() + 1).padStart(2, '0');
        let calData = { slots: {}, availability: {}, slots_detail: {}, bookings: {} };
        let calSelectedDate = null;

        async function loadCalendarMonth() {
            const grid = document.getElementById('cal-grid');
            grid.innerHTML = '<div class="col-span-7 py-12 text-center text-slate-500">Načítám…</div>';
            try {
                const [slotsRes, bookingsRes] = await Promise.all([
                    fetch(apiBase + '/slots.php?month=' + calCurrentMonth, { cache: 'no-store' }),
                    fetch(apiBase + '/calendar-bookings.php?month=' + calCurrentMonth, { cache: 'no-store', credentials: 'same-origin' })
                ]);
                const data = await slotsRes.json();
                data.bookings = bookingsRes.ok ? await bookingsRes.json() : {};
                calData = data;
                renderCalendar();
                if (calSelectedDate && calSelectedDate.startsWith(calCurrentMonth + '-')) {
                    selectDate(calSelectedDate);
                } else {
                    calSelectedDate = null;
                    document.getElementById('cal-time-panel').classList.add('hidden');
                }
            } catch (err) {
                grid.innerHTML = '<div class="col-span-7 py-12 text-center text-slate-500">Chyba načtení (spusťte PHP server)</div>';
            }
        }

        function renderCalendar() {
            const [y, m] = calCurrentMonth.split('-').map(Number);
            const first = new Date(y, m - 1, 1);
            const last = new Date(y, m - 1 + 1, 0);
            const daysInMonth = last.getDate();
            const startOffset = (first.getDay() + 6) % 7;

            const monthNames = ['Leden','Únor','Březen','Duben','Květen','Červen','Červenec','Srpen','Září','Říjen','Listopad','Prosinec'];
            document.getElementById('cal-month-title').textContent = monthNames[m - 1] + ' ' + y;

            const grid = document.getElementById('cal-grid');
            grid.innerHTML = '';
            const today = new Date().toISOString().slice(0, 10);

            for (let i = 0; i < startOffset; i++) {
                grid.innerHTML += '<div class="aspect-square"></div>';
            }
            for (let d = 1; d <= daysInMonth; d++) {
                const dateStr = y + '-' + String(m).padStart(2, '0') + '-' + String(d).padStart(2, '0');
                const dayOfWeek = new Date(y, m - 1, d).getDay();
                const isWeekend = dayOfWeek === 0 || dayOfWeek === 6;
                const isPast = dateStr < today;
                const avail = calData.availability[dateStr];
                const percent = avail ? avail.percent : 0;
                const pending = avail ? (avail.pending || 0) : 0;
                const confirmed = avail ? (avail.confirmed || 0) : 0;
                const hasSlots = avail && avail.free > 0;

                let bg = 'bg-slate-100';
                if (!isWeekend && !isPast) {
                    if (percent === 0) {
                        if (confirmed > 0) bg = 'bg-teal-500/80 hover:bg-teal-600';
                        else if (pending > 0) bg = 'bg-amber-500/80 hover:bg-amber-600';
                        else bg = 'bg-slate-200';
                    } else if (confirmed > 0) {
                        if (percent >= 50) bg = 'bg-teal-400 hover:bg-teal-500';
                        else bg = 'bg-teal-300 hover:bg-teal-400';
                    } else if (pending > 0) {
                        if (percent >= 50) bg = 'bg-amber-400 hover:bg-amber-500';
                        else bg = 'bg-amber-300 hover:bg-amber-400';
                    } else {
                        if (percent >= 75) bg = 'bg-emerald-600 hover:bg-emerald-700';
                        else if (percent >= 50) bg = 'bg-emerald-500 hover:bg-emerald-600';
                        else if (percent >= 25) bg = 'bg-emerald-300 hover:bg-emerald-400';
                        else bg = 'bg-emerald-100 hover:bg-emerald-200';
                    }
                }
                if (isWeekend) bg = 'bg-slate-50';
                if (isPast) bg = 'bg-slate-100 opacity-60';

                const clickable = !isWeekend && !isPast ? 'cursor-pointer' : 'cursor-default';
                const textColor = (percent === 0 || pending > 0 || confirmed > 0 || percent >= 50) ? 'text-white' : 'text-slate-800';
                const title = confirmed > 0 ? (pending > 0 ? `${confirmed} potvrzeno, ${pending} čeká` : `${confirmed} potvrzeno`) : (pending > 0 ? `${pending} čeká na potvrzení` : '');
                const selected = dateStr === calSelectedDate ? ' ring-2 ring-slate-800 ring-offset-2' : '';
                grid.innerHTML += `<div class="aspect-square rounded-lg flex flex-col items-center justify-center text-sm font-medium ${bg} ${clickable} ${textColor} min-h-[36px]${selected}" data-date="${dateStr}" title="${title}">${d}</div>`;
            }

            grid.querySelectorAll('[data-date]').forEach(cell => {
                const dateStr = cell.dataset.date;
                const dayOfWeek = new Date(dateStr + 'T12:00:00').getDay();
                const isWeekend = dayOfWeek === 0 || dayOfWeek === 6;
                const isPast = dateStr < today;
                if (!isWeekend && !isPast) {
                    cell.addEventListener('click', () => selectDate(dateStr));
                }
            });
            lucide.createIcons();
        }

        function selectDate(dateStr) {
            calSelectedDate = dateStr;
            document.querySelectorAll('#cal-grid [data-date]').forEach(cell => {
                cell.classList.toggle('ring-2', cell.dataset.date === dateStr);
                cell.classList.toggle('ring-slate-800', cell.dataset.date === dateStr);
                cell.classList.toggle('ring-offset-2', cell.dataset.date === dateStr);
            });
            const slotsDetail = calData.slots_detail?.[dateStr] ?? {};
            const bookingsByTime = calData.bookings && calData.bookings[dateStr] ? calData.bookings[dateStr] : {};
            const allTimes = Object.keys(slotsDetail).sort();
            document.getElementById('cal-selected-date').textContent = new Date(dateStr + 'T12:00:00').toLocaleDateString('cs-CZ', { weekday: 'long', day: 'numeric', month: 'long' });
            const timePanel = document.getElementById('cal-time-panel');
            const slotsEl = document.getElementById('cal-time-slots');
            slotsEl.innerHTML = '';
            if (allTimes.length === 0) {
                slotsEl.innerHTML = '<span class="text-slate-500 text-sm">Žádné sloty pro tento den.</span>';
            } else {
                const slotsDetailInfo = calData.slots_detail_info?.[dateStr] ?? {};
                allTimes.forEach(t => {
                    const status = slotsDetail[t] || 'free';
                    let bookings = bookingsByTime[t];
                    if (bookings && !Array.isArray(bookings)) bookings = [bookings];
                    if (!bookings || bookings.length === 0) bookings = null;
                    const detailInfo = slotsDetailInfo[t] || null;
                    addSlotSpan(slotsEl, t, status, dateStr, bookings, detailInfo);
                });
            }
            timePanel.classList.remove('hidden');
            requestAnimationFrame(() => {
                const main = document.querySelector('main');
                if (main && main.scrollHeight > main.clientHeight) {
                    const mr = main.getBoundingClientRect();
                    const pr = timePanel.getBoundingClientRect();
                    const scrollTarget = main.scrollTop + (pr.top - mr.top) - 24;
                    main.scrollTo({ top: Math.max(0, scrollTarget), behavior: 'smooth' });
                } else {
                    timePanel.scrollIntoView({ behavior: 'smooth', block: 'start' });
                }
            });
        }

        function addSlotSpan(container, time, status, dateStr, bookings, detailInfo) {
            const span = document.createElement('span');
            span.textContent = time;
            span.className = 'px-4 py-2 rounded-lg text-sm font-medium inline-block';
            const cancelled = bookings && bookings.length > 0 && bookings.every(b => b.status === 'cancelled');
            const hasBookings = bookings && bookings.length > 0;
            const names = hasBookings ? bookings.map(b => b.name).join(', ') : '';

            let title = '';
            let clickable = false;

            if (status === 'blocked') {
                span.className += ' bg-red-100 text-red-800';
                title = 'Blokováno ručně v administraci Dostupnost';
            } else if (cancelled) {
                span.className += ' bg-slate-300 text-slate-800 border-2 border-slate-500';
                title = 'Zamítnuto. Jména: ' + names + '. Klikněte pro obnovení.';
                clickable = true;
            } else if (status === 'free') {
                span.className += ' bg-emerald-100 text-emerald-800';
                title = 'Volné';
            } else if (status === 'pending') {
                span.className += ' bg-amber-400 text-amber-900';
                title = hasBookings ? 'Čeká na potvrzení: ' + names + '. Klikněte pro úpravu.' : 'Čeká na potvrzení.';
                clickable = hasBookings;
            } else if (status === 'confirmed') {
                if (hasBookings) {
                    span.className += ' bg-teal-100 text-teal-800';
                    title = 'Potvrzeno: ' + names + '. Klikněte pro úpravu.';
                    clickable = true;
                } else {
                    span.className += ' bg-slate-200 text-slate-700 cursor-default';
                    const label = detailInfo && detailInfo.label ? detailInfo.label : 'Událost z Google Calendar';
                    title = 'Obsazeno: ' + label + '. Nelze upravovat v rezervacích.';
                }
            } else {
                span.className += ' bg-slate-100 text-slate-600';
                title = 'Stav: ' + status;
            }

            span.title = title;
            if (clickable) {
                span.classList.add('cursor-pointer', 'hover:ring-2', 'hover:ring-slate-400', 'hover:ring-offset-1');
                span.dataset.date = dateStr;
                span.dataset.time = time;
                span.addEventListener('click', () => openBookingModal(bookings, dateStr, time));
            }
            container.appendChild(span);
        }

        function openBookingModal(bookings, dateStr, time) {
            const arr = Array.isArray(bookings) ? bookings : [bookings];
            const booking = arr[0];
            const modal = document.getElementById('cal-booking-modal');
            const body = document.getElementById('cal-booking-modal-body');
            const btnConfirm = document.getElementById('cal-modal-confirm');
            const btnCancel = document.getElementById('cal-modal-cancel');
            const btnRestore = document.getElementById('cal-modal-restore');
            let html = `<p><strong>Datum:</strong> ${new Date(dateStr + 'T12:00:00').toLocaleDateString('cs-CZ')} ${time}</p>`;
            if (arr.length > 1 && arr.every(b => b.status === 'cancelled')) {
                html += '<p class="mt-2"><strong>Zamítnutá jména:</strong></p><ul class="list-disc list-inside text-slate-600 mt-1 space-y-1">';
                arr.forEach(b => {
                    html += `<li class="flex items-center justify-between gap-2">
                        <span>${escapeHtml(b.name)}</span>
                        <button type="button" class="cal-restore-btn px-2 py-1 bg-amber-500 hover:bg-amber-600 text-white text-xs rounded" data-id="${b.id}">Obnovit</button>
                    </li>`;
                });
                html += '</ul>';
            } else {
                html += `<p><strong>Jméno:</strong> ${escapeHtml(booking.name)}</p>
                    <p><strong>E-mail:</strong> <a href="mailto:${escapeHtml(booking.email)}" class="text-teal-600 hover:underline">${escapeHtml(booking.email)}</a></p>`;
            }
            body.innerHTML = html;
            body.querySelectorAll('.cal-restore-btn').forEach(btn => {
                btn.addEventListener('click', () => updateBookingFromCalendar(parseInt(btn.dataset.id), 'restore'));
            });
            btnConfirm.classList.add('hidden');
            btnCancel.classList.add('hidden');
            btnRestore.classList.add('hidden');
            if (booking.status === 'pending') {
                btnConfirm.classList.remove('hidden');
                btnCancel.classList.remove('hidden');
            } else if (booking.status === 'confirmed') {
                btnCancel.classList.remove('hidden');
            } else if (booking.status === 'cancelled') {
                btnRestore.classList.remove('hidden');
            }
            btnConfirm.onclick = () => updateBookingFromCalendar(booking.id, 'confirm');
            btnCancel.onclick = () => updateBookingFromCalendar(booking.id, 'cancel');
            btnRestore.onclick = () => updateBookingFromCalendar(booking.id, 'restore');
            modal.classList.remove('hidden');
        }

        function closeBookingModal() {
            document.getElementById('cal-booking-modal').classList.add('hidden');
        }

        function escapeHtml(s) {
            const d = document.createElement('div');
            d.textContent = s;
            return d.innerHTML;
        }

        function updateBookingFromCalendar(id, action) {
            const btn = event && event.target ? event.target : null;
            if (btn) btn.disabled = true;

            function parseJson(r) {
                return r.text().then(t => {
                    try { return JSON.parse(t); } catch (_) { return {}; }
                });
            }

            function finish(ok) {
                if (btn) btn.disabled = false;
                if (ok) {
                    closeBookingModal();
                    loadCalendarMonth();
                }
            }

            if (action === 'confirm') {
                fetch('../api/booking-confirmation-check.php?id=' + id, { cache: 'no-store', credentials: 'same-origin' })
                    .then(parseJson)
                    .then(check => {
                        let sendEmail = 1;
                        if (check.already_sent) {
                            let msg = 'E-mail s potvrzením termínu byl již dříve odeslán.';
                            if (check.sent_at) {
                                const d = new Date(check.sent_at.replace(' ', 'T'));
                                msg += '\n\nOdesláno: ' + d.toLocaleString('cs-CZ', { dateStyle: 'medium', timeStyle: 'short' });
                            }
                            if (check.email) msg += '\nNa adresu: ' + check.email;
                            msg += '\n\nChcete odeslat znovu?';
                            if (!confirm(msg)) sendEmail = 0;
                        }
                        const fd = new FormData();
                        fd.append('id', id);
                        fd.append('send_email', sendEmail);
                        return fetch('../api/booking-confirm.php', { method: 'POST', body: fd, cache: 'no-store', credentials: 'same-origin' });
                    })
                    .then(parseJson)
                    .then(data => {
                        if (data.success) finish(true);
                        else { finish(false); alert(data.error || 'Chyba při potvrzování.'); }
                    })
                    .catch(err => { finish(false); console.error(err); alert('Chyba při odesílání.'); });
                return;
            }

            fetch('bookings.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'ajax_action=' + action + '&id=' + id,
                cache: 'no-store',
                credentials: 'same-origin'
            })
            .then(parseJson)
            .then(data => {
                if (data.success) finish(true);
                else finish(false);
            })
            .catch(() => finish(false));
        }

        document.getElementById('cal-prev').addEventListener('click', () => {
            const [y, m] = calCurrentMonth.split('-').map(Number);
            const d = new Date(y, m - 2, 1);
            calCurrentMonth = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0');
            loadCalendarMonth();
        });
        document.getElementById('cal-next').addEventListener('click', () => {
            const [y, m] = calCurrentMonth.split('-').map(Number);
            const d = new Date(y, m, 1);
            calCurrentMonth = d.getFullYear() + '-' + String(d.getMonth() + 1).padStart(2, '0');
            loadCalendarMonth();
        });

        loadCalendarMonth();
    </script>
</body>
</html>
