    </main>
</div>
<script>
(function() {
    const toggle = document.getElementById('menu-toggle');
    const sidebar = document.getElementById('sidebar');
    const backdrop = document.getElementById('menu-backdrop');
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
})();
</script>
