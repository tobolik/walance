<?php
/**
 * Admin layout – sidebar vlevo, hlavní obsah vpravo
 * Na mobilu: hamburger menu (výsuvné)
 * Použití: před include nastavte $adminCurrentPage = 'dashboard'|'bookings'|'calendar'|'availability'|'contact'
 */
$adminCurrentPage = $adminCurrentPage ?? '';
$adminV = defined('APP_VERSION') ? APP_VERSION : '1.0.0';
?>
<div class="flex min-h-screen">
    <!-- Mobilní header s hamburgerem -->
    <header class="md:hidden fixed top-0 left-0 right-0 h-14 bg-white border-b border-slate-200 flex items-center justify-between px-4 z-50">
        <button type="button" id="menu-toggle" class="p-2 -ml-2 rounded-lg text-slate-600 hover:bg-slate-100" aria-label="Otevřít menu">
            <i data-lucide="menu" class="w-6 h-6"></i>
        </button>
        <span class="text-lg font-bold text-slate-800">WALANCE<span class="text-teal-600">.</span></span>
        <div class="w-10"></div>
    </header>

    <!-- Backdrop pro mobilní menu -->
    <div id="menu-backdrop" class="fixed inset-0 bg-black/40 z-40 hidden md:hidden" aria-hidden="true"></div>

    <aside id="sidebar" class="fixed left-0 top-0 bottom-0 w-56 bg-white border-r border-slate-200 flex flex-col z-40 -translate-x-full md:translate-x-0 transition-transform duration-200 ease-out pt-14 md:pt-0 md:transition-[width] md:duration-200" data-narrow="0">
        <div class="p-4 md:p-3 border-b border-slate-100 flex flex-col md:flex-row md:items-center md:justify-between md:gap-2">
            <div class="sidebar-brand">
                <h1 class="text-lg font-bold text-slate-800">WALANCE<span class="text-teal-600">.</span></h1>
                <p class="text-xs text-slate-500 mt-0.5 sidebar-text">CRM administrace</p>
            </div>
            <button type="button" id="sidebar-narrow-toggle" class="hidden md:flex p-2 rounded-lg text-slate-500 hover:bg-slate-100 hover:text-slate-700 flex-shrink-0" aria-label="Zúžit menu" title="Zúžit menu (jen ikony)">
                <i data-lucide="panel-left-close" class="w-4 h-4"></i>
            </button>
        </div>
        <nav class="flex-1 p-3 space-y-0.5">
            <a href="dashboard.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $adminCurrentPage === 'dashboard' ? 'bg-teal-50 text-teal-700 font-medium' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' ?>" title="Kontakty">
                <i data-lucide="users" class="w-4 h-4 flex-shrink-0 <?= $adminCurrentPage === 'dashboard' ? 'text-teal-600' : '' ?>"></i>
                <span class="sidebar-text">Kontakty</span>
            </a>
            <a href="bookings.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $adminCurrentPage === 'bookings' ? 'bg-teal-50 text-teal-700 font-medium' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' ?>" title="Rezervace">
                <i data-lucide="calendar-check" class="w-4 h-4 flex-shrink-0 <?= $adminCurrentPage === 'bookings' ? 'text-teal-600' : '' ?>"></i>
                <span class="sidebar-text">Rezervace</span>
            </a>
            <a href="calendar.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $adminCurrentPage === 'calendar' ? 'bg-teal-50 text-teal-700 font-medium' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' ?>" title="Kalendář">
                <i data-lucide="calendar" class="w-4 h-4 flex-shrink-0 <?= $adminCurrentPage === 'calendar' ? 'text-teal-600' : '' ?>"></i>
                <span class="sidebar-text">Kalendář</span>
            </a>
            <a href="availability.php" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm <?= $adminCurrentPage === 'availability' ? 'bg-teal-50 text-teal-700 font-medium' : 'text-slate-600 hover:bg-slate-50 hover:text-slate-800' ?>" title="Dostupnost">
                <i data-lucide="clock" class="w-4 h-4 flex-shrink-0 <?= $adminCurrentPage === 'availability' ? 'text-teal-600' : '' ?>"></i>
                <span class="sidebar-text">Dostupnost</span>
            </a>
            <a href="../" class="flex items-center gap-3 px-3 py-2.5 rounded-lg text-slate-600 hover:bg-slate-50 hover:text-slate-800 text-sm mt-2" title="Web">
                <i data-lucide="external-link" class="w-4 h-4 flex-shrink-0"></i>
                <span class="sidebar-text">Web</span>
            </a>
        </nav>
        <div class="p-3 border-t border-slate-100 sidebar-footer">
            <p class="text-xs text-slate-500 px-3 sidebar-text">Přihlášen jako</p>
            <p class="flex items-center justify-between px-3 mt-0.5 sidebar-user">
                <span class="text-sm font-medium text-slate-700 truncate sidebar-text"><?= htmlspecialchars($_SESSION['walance_admin_name'] ?? 'Admin') ?></span>
                <span class="text-xs text-slate-400 flex-shrink-0 sidebar-text">v<?= htmlspecialchars($adminV) ?></span>
            </p>
            <a href="logout.php" class="flex items-center gap-3 px-3 py-2 mt-2 rounded-lg text-red-600 hover:bg-red-50 text-sm font-medium" title="Odhlásit">
                <i data-lucide="log-out" class="w-4 h-4 flex-shrink-0"></i>
                <span class="sidebar-text">Odhlásit</span>
            </a>
        </div>
    </aside>
    <main id="main-content" class="flex-1 overflow-auto pt-14 md:pt-0 ml-0 md:ml-56 transition-[margin] duration-200">
