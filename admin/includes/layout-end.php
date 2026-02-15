    </main>
</div>
<style>
.sidebar-narrow { width: 4rem !important; }
.sidebar-narrow .sidebar-text { display: none !important; }
.sidebar-narrow .sidebar-brand { display: none; }
.sidebar-narrow .border-b { justify-content: center; }
.sidebar-narrow nav a { justify-content: center; padding: 0.5rem; }
.sidebar-narrow .sidebar-footer > p { display: none; }
.sidebar-narrow .sidebar-footer .sidebar-user { display: none; }
.sidebar-narrow .sidebar-footer a { justify-content: center; padding: 0.5rem; }
.sidebar-narrow .sidebar-footer { padding: 0.5rem; }
</style>
<script>
(function() {
    const toggle = document.getElementById('menu-toggle');
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('menu-backdrop');
    const main = document.getElementById('main-content');
    const narrowToggle = document.getElementById('sidebar-narrow-toggle');
    const STORAGE_KEY = 'admin-sidebar-narrow';

    if (!toggle || !sidebar) return;

    function openMenu() {
        sidebar.classList.remove('-translate-x-full');
        backdrop.classList.remove('hidden');
        document.body.style.overflow = 'hidden';
    }
    function closeMenu() {
        sidebar.classList.add('-translate-x-full');
        backdrop.classList.add('hidden');
        document.body.style.overflow = '';
    }
    toggle.addEventListener('click', () => {
        if (sidebar.classList.contains('-translate-x-full')) openMenu();
        else closeMenu();
    });
    backdrop.addEventListener('click', closeMenu);
    sidebar.querySelectorAll('a').forEach(a => {
        a.addEventListener('click', closeMenu);
    });

    function setNarrow(narrow) {
        if (narrow) {
            sidebar.classList.add('sidebar-narrow');
            sidebar.dataset.narrow = '1';
            if (main) main.classList.remove('md:ml-56'), main.classList.add('md:ml-16');
            if (narrowToggle) {
                const icon = narrowToggle.querySelector('i');
                if (icon) { icon.setAttribute('data-lucide', 'panel-left-open'); }
                narrowToggle.title = 'Rozšířit menu';
                narrowToggle.setAttribute('aria-label', 'Rozšířit menu');
            }
            try { localStorage.setItem(STORAGE_KEY, '1'); } catch (_) {}
        } else {
            sidebar.classList.remove('sidebar-narrow');
            sidebar.dataset.narrow = '0';
            if (main) main.classList.remove('md:ml-16'), main.classList.add('md:ml-56');
            if (narrowToggle) {
                const icon = narrowToggle.querySelector('i');
                if (icon) { icon.setAttribute('data-lucide', 'panel-left-close'); }
                narrowToggle.title = 'Zúžit menu (jen ikony)';
                narrowToggle.setAttribute('aria-label', 'Zúžit menu');
            }
            try { localStorage.setItem(STORAGE_KEY, '0'); } catch (_) {}
        }
        if (typeof lucide !== 'undefined' && lucide.createIcons) lucide.createIcons();
    }

    if (narrowToggle && main) {
        try {
            if (localStorage.getItem(STORAGE_KEY) === '1') setNarrow(true);
        } catch (_) {}
        narrowToggle.addEventListener('click', () => {
            setNarrow(sidebar.dataset.narrow === '1' ? false : true);
        });
    }
})();
</script>
