<?php
/**
 * Veřejný blog – seznam článků
 */
require_once __DIR__ . '/../api/config.php';
require_once __DIR__ . '/../api/db.php';

$v = defined('APP_VERSION') ? APP_VERSION : '1.0.0';
$db = getDb();

$page = max((int)($_GET['page'] ?? 1), 1);
$perPage = 9;
$offset = ($page - 1) * $perPage;

$countStmt = $db->query("SELECT COUNT(*) FROM blog_posts WHERE valid_to IS NULL AND status = 'published' AND published_at IS NOT NULL");
$total = (int)$countStmt->fetchColumn();
$totalPages = max(1, ceil($total / $perPage));

$stmt = $db->prepare("SELECT bp.id, bp.blog_posts_id, bp.title, bp.slug, bp.excerpt, bp.featured_image, 
                       bp.published_at, bp.meta_description, au.name as author_name
                FROM blog_posts bp
                LEFT JOIN admin_users au ON bp.author_id = au.id
                WHERE bp.valid_to IS NULL AND bp.status = 'published' AND bp.published_at IS NOT NULL
                ORDER BY bp.published_at DESC
                LIMIT ? OFFSET ?");
$stmt->execute([$perPage, $offset]);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Blog — WALANCE</title>
    <meta name="description" content="Články o udržitelném výkonu, leadershipu a metodě WALANCE. Tipy pro lídry, jak zvládnout stres a předcházet vyhoření.">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='6' fill='%231e293b'/%3E%3Ctext x='16' y='23' text-anchor='middle' font-family='sans-serif' font-weight='700' font-size='20' fill='%234a7c59'%3EW%3C/text%3E%3Ccircle cx='27' cy='24' r='2.5' fill='%234a7c59'/%3E%3C/svg%3E">

    <meta property="og:title" content="Blog — WALANCE">
    <meta property="og:description" content="Články o udržitelném výkonu, leadershipu a metodě WALANCE.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://walance.cz/blog/">
    <meta property="og:locale" content="cs_CZ">

    <link rel="canonical" href="https://walance.cz/blog/">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700;9..144,900&display=swap&v=<?= htmlspecialchars($v) ?>" rel="stylesheet">

    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --cream: #FAF9F7;
            --ink: #1e293b;
            --ink-light: #475569;
            --ink-muted: #94a3b8;
            --accent: #0d9488;
            --accent-dark: #0a7a70;
            --accent-light: #14b8a6;
            --sage: #5a7d5a;
            --mist: #e2e8f0;
            --mist-light: #f1f5f9;
            --white: #ffffff;
            --font-body: 'DM Sans', -apple-system, BlinkMacSystemFont, sans-serif;
            --font-display: 'Fraunces', Georgia, serif;
        }

        ::selection { background-color: var(--accent); color: var(--cream); }

        html { scroll-behavior: smooth; -webkit-font-smoothing: antialiased; }

        body {
            font-family: var(--font-body);
            color: var(--ink);
            background: var(--cream);
            line-height: 1.6;
            overflow-x: hidden;
        }

        h1, h2, h3, h4 { font-family: var(--font-display); line-height: 1.15; }

        a { color: inherit; text-decoration: none; }

        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 24px;
        }

        @media (min-width: 768px) {
            .container { padding: 0 40px; }
        }

        /* Navigation */
        .nav {
            position: fixed;
            top: 0; left: 0; right: 0;
            z-index: 100;
            background: rgba(250, 249, 247, 0.92);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--mist);
        }

        .nav-inner {
            display: flex;
            justify-content: space-between;
            align-items: center;
            height: 72px;
        }

        .nav-logo {
            font-family: var(--font-display);
            font-size: 1.5rem;
            font-weight: 700;
            letter-spacing: -0.02em;
            color: var(--ink);
        }

        .nav-logo span { color: var(--accent); }

        .nav-links {
            display: none;
            list-style: none;
            gap: 32px;
            align-items: center;
        }

        @media (min-width: 768px) {
            .nav-links { display: flex; }
        }

        .nav-links a {
            font-size: 0.8125rem;
            font-weight: 600;
            letter-spacing: 0.05em;
            text-transform: uppercase;
            color: var(--ink-light);
            transition: color 0.2s;
        }

        .nav-links a:hover { color: var(--ink); }

        .nav-links a::after {
            content: '.';
            color: var(--accent);
            font-weight: 900;
        }

        .nav-cta {
            background: var(--accent) !important;
            color: var(--white) !important;
            padding: 10px 24px;
            border-radius: 8px;
            font-size: 0.75rem !important;
            font-weight: 700 !important;
            letter-spacing: 0.08em;
        }

        .nav-cta:hover {
            background: var(--accent-dark) !important;
        }

        .nav-cta::after { display: none !important; }

        .nav-mobile-btn {
            display: flex;
            background: none;
            border: none;
            cursor: pointer;
            padding: 8px;
            color: var(--ink);
        }

        @media (min-width: 768px) {
            .nav-mobile-btn { display: none; }
        }

        .mobile-menu {
            display: none;
            position: fixed;
            top: 72px;
            left: 0; right: 0; bottom: 0;
            background: var(--cream);
            z-index: 99;
            padding: 32px 24px;
        }

        .mobile-menu.open { display: flex; flex-direction: column; gap: 4px; }

        .mobile-menu a {
            display: block;
            padding: 16px 0;
            font-size: 1.125rem;
            font-weight: 600;
            color: var(--ink);
            border-bottom: 1px solid var(--mist);
            transition: color 0.2s;
        }

        .mobile-menu a::after {
            content: '.';
            color: var(--accent);
            font-weight: 900;
        }

        .mobile-menu a:hover { color: var(--accent); }

        /* Blog Header */
        .blog-header {
            padding: 120px 0 48px;
            text-align: center;
        }

        .blog-header h1 {
            font-size: clamp(2rem, 4vw, 3rem);
            color: var(--ink);
            margin-bottom: 12px;
        }

        .blog-header h1 em {
            color: var(--accent);
            font-style: normal;
        }

        .blog-header p {
            font-size: 1.125rem;
            color: var(--ink-light);
            max-width: 560px;
            margin: 0 auto;
        }

        /* Post Grid */
        .blog-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 32px;
            padding-bottom: 80px;
        }

        @media (min-width: 640px) {
            .blog-grid { grid-template-columns: repeat(2, 1fr); }
        }

        @media (min-width: 960px) {
            .blog-grid { grid-template-columns: repeat(3, 1fr); }
        }

        .blog-card {
            background: var(--white);
            border-radius: 16px;
            overflow: hidden;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            border: 1px solid var(--mist);
        }

        .blog-card:hover {
            transform: translateY(-4px);
            box-shadow: 0 12px 40px rgba(0,0,0,0.08);
        }

        .blog-card-image {
            width: 100%;
            height: 200px;
            object-fit: cover;
            background: var(--mist-light);
        }

        .blog-card-placeholder {
            width: 100%;
            height: 200px;
            background: linear-gradient(135deg, var(--mist-light) 0%, var(--mist) 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--ink-muted);
            font-family: var(--font-display);
            font-size: 2rem;
            font-weight: 700;
        }

        .blog-card-body {
            padding: 24px;
        }

        .blog-card-meta {
            font-size: 0.8125rem;
            color: var(--ink-muted);
            margin-bottom: 8px;
            display: flex;
            gap: 12px;
            align-items: center;
        }

        .blog-card-title {
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 8px;
            line-height: 1.3;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .blog-card-title a {
            color: var(--ink);
            transition: color 0.2s;
        }

        .blog-card-title a:hover {
            color: var(--accent);
        }

        .blog-card-excerpt {
            font-size: 0.9375rem;
            color: var(--ink-light);
            line-height: 1.6;
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }

        .blog-card-link {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            margin-top: 16px;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--accent);
            transition: gap 0.2s;
        }

        .blog-card-link:hover {
            gap: 10px;
        }

        /* Pagination */
        .pagination {
            display: flex;
            justify-content: center;
            gap: 8px;
            padding-bottom: 80px;
        }

        .pagination a, .pagination span {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-width: 40px;
            height: 40px;
            padding: 0 12px;
            border-radius: 8px;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.2s;
        }

        .pagination a {
            background: var(--white);
            color: var(--ink-light);
            border: 1px solid var(--mist);
        }

        .pagination a:hover {
            background: var(--accent);
            color: var(--white);
            border-color: var(--accent);
        }

        .pagination .active {
            background: var(--accent);
            color: var(--white);
            border: 1px solid var(--accent);
        }

        /* Empty state */
        .blog-empty {
            text-align: center;
            padding: 80px 24px;
            color: var(--ink-light);
        }

        .blog-empty svg {
            width: 64px;
            height: 64px;
            margin: 0 auto 16px;
            color: var(--ink-muted);
        }

        .blog-empty h2 {
            font-size: 1.5rem;
            margin-bottom: 8px;
        }

        /* Footer */
        .footer {
            background: var(--ink);
            color: var(--cream);
            padding: 48px 0 32px;
        }

        .footer-inner {
            display: flex;
            flex-direction: column;
            gap: 24px;
            align-items: center;
            text-align: center;
        }

        .footer-brand {
            font-family: var(--font-display);
            font-size: 1.5rem;
            font-weight: 700;
        }

        .footer-copy {
            font-size: 0.75rem;
            opacity: 0.5;
        }

        .footer-links {
            display: flex;
            gap: 16px;
        }

        .footer-links a {
            width: 36px; height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            background: rgba(250,249,247,0.1);
            transition: background 0.2s;
        }

        .footer-links a:hover {
            background: rgba(250,249,247,0.2);
        }

        .footer-links svg {
            width: 18px; height: 18px;
            fill: none;
            stroke: var(--cream);
            stroke-width: 2;
        }
    </style>
</head>
<body>

    <!-- Navigation -->
    <nav class="nav">
        <div class="container nav-inner">
            <a href="../" class="nav-logo">WALANCE<span>.</span></a>
            <ul class="nav-links">
                <li><a href="../#problem">Problém</a></li>
                <li><a href="../#method">Metoda</a></li>
                <li><a href="../#story">Příběh</a></li>
                <li><a href="./">Blog</a></li>
                <li><a href="../#products">Nabídka</a></li>
                <li><a href="../#contact" class="nav-cta">Konzultace zdarma</a></li>
            </ul>
            <button class="nav-mobile-btn" id="mobile-toggle" aria-label="Menu">
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" id="hamburger-icon">
                    <line x1="3" y1="6" x2="21" y2="6"/>
                    <line x1="3" y1="12" x2="21" y2="12"/>
                    <line x1="3" y1="18" x2="21" y2="18"/>
                </svg>
                <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" id="close-icon" style="display:none">
                    <line x1="18" y1="6" x2="6" y2="18"/>
                    <line x1="6" y1="6" x2="18" y2="18"/>
                </svg>
            </button>
        </div>
        <div class="mobile-menu" id="mobile-menu">
            <a href="../#problem" class="mobile-link">Problém</a>
            <a href="../#method" class="mobile-link">Metoda</a>
            <a href="../#story" class="mobile-link">Příběh</a>
            <a href="./" class="mobile-link" style="color: var(--accent);">Blog</a>
            <a href="../#products" class="mobile-link">Nabídka</a>
            <a href="../#contact" class="mobile-link">Konzultace zdarma</a>
        </div>
    </nav>

    <!-- Blog Header -->
    <section class="blog-header">
        <div class="container">
            <h1>Blog <em>WALANCE</em></h1>
            <p>Články o udržitelném výkonu, leadershipu a tom, jak přestat bojovat s vlastní biologií.</p>
        </div>
    </section>

    <!-- Blog Grid -->
    <section class="container">
        <?php if (empty($posts)): ?>
        <div class="blog-empty">
            <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round">
                <path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/>
                <polyline points="14 2 14 8 20 8"/>
                <line x1="16" y1="13" x2="8" y2="13"/>
                <line x1="16" y1="17" x2="8" y2="17"/>
                <polyline points="10 9 9 9 8 9"/>
            </svg>
            <h2>Zatím žádné články</h2>
            <p>Připravujeme pro vás obsah. Brzy se tu objeví první články.</p>
        </div>
        <?php else: ?>
        <div class="blog-grid">
            <?php foreach ($posts as $p): ?>
            <article class="blog-card">
                <a href="<?= htmlspecialchars($p['slug']) ?>">
                    <?php if (!empty($p['featured_image'])): ?>
                    <img src="../<?= htmlspecialchars($p['featured_image']) ?>" alt="<?= htmlspecialchars($p['title']) ?>" class="blog-card-image" loading="lazy">
                    <?php else: ?>
                    <div class="blog-card-placeholder">W</div>
                    <?php endif; ?>
                </a>
                <div class="blog-card-body">
                    <div class="blog-card-meta">
                        <span><?= date('d.m.Y', strtotime($p['published_at'])) ?></span>
                        <?php if ($p['author_name']): ?>
                        <span>&middot; <?= htmlspecialchars($p['author_name']) ?></span>
                        <?php endif; ?>
                    </div>
                    <h2 class="blog-card-title">
                        <a href="<?= htmlspecialchars($p['slug']) ?>"><?= htmlspecialchars($p['title']) ?></a>
                    </h2>
                    <?php if ($p['excerpt']): ?>
                    <p class="blog-card-excerpt"><?= htmlspecialchars($p['excerpt']) ?></p>
                    <?php endif; ?>
                    <a href="<?= htmlspecialchars($p['slug']) ?>" class="blog-card-link">
                        Číst dál
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                    </a>
                </div>
            </article>
            <?php endforeach; ?>
        </div>

        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
            <a href="?page=<?= $page - 1 ?>">&larr;</a>
            <?php endif; ?>
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i === $page): ?>
                <span class="active"><?= $i ?></span>
                <?php else: ?>
                <a href="?page=<?= $i ?>"><?= $i ?></a>
                <?php endif; ?>
            <?php endfor; ?>
            <?php if ($page < $totalPages): ?>
            <a href="?page=<?= $page + 1 ?>">&rarr;</a>
            <?php endif; ?>
        </div>
        <?php endif; ?>
        <?php endif; ?>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container footer-inner">
            <div class="footer-brand">WALANCE.</div>
            <div class="footer-links">
                <a href="https://www.linkedin.com/in/janastepanikova" target="_blank" rel="noopener" aria-label="LinkedIn">
                    <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
                </a>
                <a href="mailto:jana@walance.cz" aria-label="E-mail">
                    <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                </a>
            </div>
            <div class="footer-copy">&copy; 2026 NetWalking Pro s.r.o. Všechna práva vyhrazena. <span style="margin-left: 8px; opacity: 0.5;">v<?= htmlspecialchars($v) ?></span></div>
        </div>
    </footer>

    <script>
        var mobileToggle = document.getElementById('mobile-toggle');
        var mobileMenu = document.getElementById('mobile-menu');
        var hamburgerIcon = document.getElementById('hamburger-icon');
        var closeIcon = document.getElementById('close-icon');

        mobileToggle.addEventListener('click', function() {
            var isOpen = mobileMenu.classList.toggle('open');
            hamburgerIcon.style.display = isOpen ? 'none' : 'block';
            closeIcon.style.display = isOpen ? 'block' : 'none';
            document.body.style.overflow = isOpen ? 'hidden' : '';
        });

        document.querySelectorAll('.mobile-link').forEach(function(link) {
            link.addEventListener('click', function() {
                mobileMenu.classList.remove('open');
                hamburgerIcon.style.display = 'block';
                closeIcon.style.display = 'none';
                document.body.style.overflow = '';
            });
        });
    </script>
</body>
</html>
