<?php
/**
 * Admin layout – sidebar vlevo, hlavní obsah vpravo
 * Použití: před include nastavte $adminCurrentPage = 'dashboard'|'bookings'|'calendar'|'availability'|'contact'
 */
$adminCurrentPage = $adminCurrentPage ?? '';
$adminV = defined('APP_VERSION') ? APP_VERSION : '1.0.0';
?>
<div class="flex min-h-screen">
    <aside class="fixed left-0 top-0 bottom-0 w-56 bg-white border-r border-slate-200 flex flex-col z-40">
        <div class="p-4 border-b border-slate-100">
            <h1 class="text-lg font-bold text-slate-800">WALANCE<span class="text-teal-600">.</span></h1>
            <p class="text-xs text-slate-500 mt-0.5">CRM administrace</p>
        </div>
        <nav class="flex-1 p-3 space-y-0.5">
            <a href="dashboard.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $adminCurrentPage === 'dashboard' ? 'bg-teal-50 text-teal-700 font-medium' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' ?>">
                <i data-lucide="users" class="w-4 h-4 <?= $adminCurrentPage === 'dashboard' ? 'text-teal-600' : '' ?>"></i>
                Kontakty
            </a>
            <a href="bookings.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $adminCurrentPage === 'bookings' ? 'bg-teal-50 text-teal-700 font-medium' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' ?>">
                <i data-lucide="calendar-check" class="w-4 h-4 <?= $adminCurrentPage === 'bookings' ? 'text-teal-600' : '' ?>"></i>
                Rezervace
            </a>
            <a href="calendar.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $adminCurrentPage === 'calendar' ? 'bg-teal-50 text-teal-700 font-medium' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' ?>">
                <i data-lucide="calendar" class="w-4 h-4 <?= $adminCurrentPage === 'calendar' ? 'text-teal-600' : '' ?>"></i>
                Kalendář
            </a>
            <a href="availability.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $adminCurrentPage === 'availability' ? 'bg-teal-50 text-teal-700 font-medium' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' ?>">
                <i data-lucide="clock" class="w-4 h-4 <?= $adminCurrentPage === 'availability' ? 'text-teal-600' : '' ?>"></i>
                Dostupnost
            </a>
            <a href="../" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-slate-800 text-sm mt-2">
                <i data-lucide="external-link" class="w-4 h-4"></i>
                Web
            </a>
        </nav>
        <div class="p-3 border-t border-slate-100">
            <p class="text-xs text-slate-500 px-3">Přihlášen jako</p>
            <p class="flex items-center justify-between px-3 mt-0.5">
                <span class="text-sm font-medium text-slate-700"><?= htmlspecialchars($_SESSION['walance_admin_name'] ?? 'Admin') ?></span>
                <span class="text-xs text-slate-400">v<?= htmlspecialchars($adminV) ?></span>
            </p>
            <a href="logout.php" class="flex items-center gap-3 px-3 py-2 mt-2 rounded-lg text-red-600 hover:bg-red-50 text-sm font-medium">
                <i data-lucide="log-out" class="w-4 h-4"></i>
                Odhlásit
            </a>
        </div>
    </aside>
    <main class="flex-1 overflow-auto ml-56">
