<?php
/**
 * Veřejný blog – detail článku
 * URL: /blog/{slug} (přes .htaccess rewrite) nebo /blog/post.php?slug=xxx
 */
require_once __DIR__ . '/../api/config.php';
require_once __DIR__ . '/../api/db.php';

$v = defined('APP_VERSION') ? APP_VERSION : '1.0.0';
$db = getDb();

$slug = trim($_GET['slug'] ?? '');
if (!$slug) {
    header('Location: ./');
    exit;
}

$stmt = $db->prepare("SELECT bp.*, au.name as author_name
        FROM blog_posts bp
        LEFT JOIN admin_users au ON bp.author_id = au.id
        WHERE bp.slug = ? AND bp.valid_to IS NULL AND bp.status = 'published'");
$stmt->execute([$slug]);
$post = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$post) {
    http_response_code(404);
    $pageTitle = 'Článek nenalezen — WALANCE';
    $pageDescription = 'Požadovaný článek nebyl nalezen.';
    $notFound = true;
} else {
    $pageTitle = htmlspecialchars($post['title']) . ' — WALANCE Blog';
    $pageDescription = htmlspecialchars($post['meta_description'] ?: $post['excerpt'] ?: '');
    $notFound = false;
}

// Načíst předchozí a další článek
$prevPost = null;
$nextPost = null;
if (!$notFound) {
    $prevStmt = $db->prepare("SELECT title, slug FROM blog_posts WHERE valid_to IS NULL AND status = 'published' AND published_at < ? AND published_at IS NOT NULL ORDER BY published_at DESC LIMIT 1");
    $prevStmt->execute([$post['published_at']]);
    $prevPost = $prevStmt->fetch(PDO::FETCH_ASSOC);

    $nextStmt = $db->prepare("SELECT title, slug FROM blog_posts WHERE valid_to IS NULL AND status = 'published' AND published_at > ? AND published_at IS NOT NULL ORDER BY published_at ASC LIMIT 1");
    $nextStmt->execute([$post['published_at']]);
    $nextPost = $nextStmt->fetch(PDO::FETCH_ASSOC);
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $pageTitle ?></title>
    <?php if ($pageDescription): ?>
    <meta name="description" content="<?= $pageDescription ?>">
    <?php endif; ?>
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='6' fill='%231e293b'/%3E%3Ctext x='16' y='23' text-anchor='middle' font-family='sans-serif' font-weight='700' font-size='20' fill='%234a7c59'%3EW%3C/text%3E%3Ccircle cx='27' cy='24' r='2.5' fill='%234a7c59'/%3E%3C/svg%3E">

    <?php if (!$notFound): ?>
    <meta property="og:title" content="<?= htmlspecialchars($post['title']) ?>">
    <meta property="og:description" content="<?= $pageDescription ?>">
    <meta property="og:type" content="article">
    <meta property="og:url" content="https://walance.cz/blog/<?= htmlspecialchars($post['slug']) ?>">
    <?php if (!empty($post['featured_image'])): ?>
    <meta property="og:image" content="https://walance.cz/<?= htmlspecialchars($post['featured_image']) ?>">
    <?php endif; ?>
    <meta property="og:locale" content="cs_CZ">
    <meta property="article:published_time" content="<?= date('c', strtotime($post['published_at'])) ?>">
    <?php if ($post['author_name']): ?>
    <meta property="article:author" content="<?= htmlspecialchars($post['author_name']) ?>">
    <?php endif; ?>
    <link rel="canonical" href="https://walance.cz/blog/<?= htmlspecialchars($post['slug']) ?>">

    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "BlogPosting",
        "headline": "<?= htmlspecialchars($post['title']) ?>",
        "description": "<?= $pageDescription ?>",
        "datePublished": "<?= date('c', strtotime($post['published_at'])) ?>",
        "url": "https://walance.cz/blog/<?= htmlspecialchars($post['slug']) ?>",
        <?php if (!empty($post['featured_image'])): ?>
        "image": "https://walance.cz/<?= htmlspecialchars($post['featured_image']) ?>",
        <?php endif; ?>
        "author": {
            "@type": "Person",
            "name": "<?= htmlspecialchars($post['author_name'] ?? 'WALANCE') ?>"
        },
        "publisher": {
            "@type": "Organization",
            "name": "WALANCE",
            "url": "https://walance.cz"
        }
    }
    </script>
    <?php endif; ?>

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

        /* Article Header */
        .article-header {
            padding: 120px 0 32px;
            max-width: 760px;
            margin: 0 auto;
        }

        .article-back {
            display: inline-flex;
            align-items: center;
            gap: 6px;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--accent);
            margin-bottom: 24px;
            transition: gap 0.2s;
        }

        .article-back:hover { gap: 10px; }

        .article-header h1 {
            font-size: clamp(1.75rem, 4vw, 2.5rem);
            color: var(--ink);
            margin-bottom: 16px;
        }

        .article-meta {
            display: flex;
            flex-wrap: wrap;
            gap: 16px;
            align-items: center;
            font-size: 0.875rem;
            color: var(--ink-muted);
            padding-bottom: 24px;
            border-bottom: 1px solid var(--mist);
        }

        .article-meta-author {
            font-weight: 600;
            color: var(--ink-light);
        }

        /* Featured image */
        .article-featured-image {
            max-width: 760px;
            margin: 32px auto;
        }

        .article-featured-image img {
            width: 100%;
            border-radius: 16px;
            max-height: 420px;
            object-fit: cover;
        }

        /* Article Body */
        .article-body {
            max-width: 760px;
            margin: 0 auto;
            padding-bottom: 60px;
            font-size: 1.0625rem;
            line-height: 1.8;
            color: var(--ink);
        }

        .article-body h2 {
            font-size: 1.5rem;
            margin: 2em 0 0.75em;
            color: var(--ink);
        }

        .article-body h3 {
            font-size: 1.25rem;
            margin: 1.5em 0 0.5em;
            color: var(--ink);
        }

        .article-body p {
            margin-bottom: 1.25em;
        }

        .article-body ul, .article-body ol {
            margin: 1em 0;
            padding-left: 1.5em;
        }

        .article-body li {
            margin-bottom: 0.5em;
        }

        .article-body blockquote {
            border-left: 4px solid var(--accent);
            padding: 16px 24px;
            margin: 1.5em 0;
            background: var(--mist-light);
            border-radius: 0 12px 12px 0;
            font-style: italic;
            color: var(--ink-light);
        }

        .article-body img {
            max-width: 100%;
            height: auto;
            border-radius: 12px;
            margin: 1.5em 0;
        }

        .article-body a {
            color: var(--accent);
            text-decoration: underline;
            text-underline-offset: 3px;
        }

        .article-body a:hover {
            color: var(--accent-dark);
        }

        .article-body strong {
            font-weight: 700;
        }

        .article-body code {
            background: var(--mist-light);
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.9em;
        }

        .article-body pre {
            background: var(--ink);
            color: var(--cream);
            padding: 24px;
            border-radius: 12px;
            overflow-x: auto;
            margin: 1.5em 0;
        }

        .article-body pre code {
            background: none;
            padding: 0;
            color: inherit;
        }

        .article-body table {
            width: 100%;
            border-collapse: collapse;
            margin: 1.5em 0;
        }

        .article-body th, .article-body td {
            padding: 12px 16px;
            border: 1px solid var(--mist);
            text-align: left;
        }

        .article-body th {
            background: var(--mist-light);
            font-weight: 600;
        }

        /* Post navigation */
        .post-nav {
            max-width: 760px;
            margin: 0 auto;
            padding: 40px 0 80px;
            border-top: 1px solid var(--mist);
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 24px;
        }

        .post-nav-item {
            display: flex;
            flex-direction: column;
            gap: 4px;
        }

        .post-nav-item.next {
            text-align: right;
        }

        .post-nav-label {
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.05em;
            color: var(--ink-muted);
        }

        .post-nav-title {
            font-family: var(--font-display);
            font-size: 1.0625rem;
            font-weight: 600;
            color: var(--accent);
            transition: color 0.2s;
        }

        .post-nav-title:hover {
            color: var(--accent-dark);
        }

        /* 404 */
        .not-found {
            text-align: center;
            padding: 160px 24px 80px;
        }

        .not-found h1 {
            font-size: 3rem;
            margin-bottom: 16px;
        }

        .not-found p {
            font-size: 1.125rem;
            color: var(--ink-light);
            margin-bottom: 24px;
        }

        .not-found a {
            color: var(--accent);
            font-weight: 600;
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

    <?php if ($notFound): ?>
    <div class="container not-found">
        <h1>404</h1>
        <p>Článek nebyl nalezen.</p>
        <a href="./">&larr; Zpět na blog</a>
    </div>
    <?php else: ?>

    <div class="container">
        <!-- Article Header -->
        <div class="article-header">
            <a href="./" class="article-back">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="19" y1="12" x2="5" y2="12"/><polyline points="12 19 5 12 12 5"/></svg>
                Zpět na blog
            </a>
            <h1><?= htmlspecialchars($post['title']) ?></h1>
            <div class="article-meta">
                <?php if ($post['author_name']): ?>
                <span class="article-meta-author"><?= htmlspecialchars($post['author_name']) ?></span>
                <?php endif; ?>
                <span><?= date('d. m. Y', strtotime($post['published_at'])) ?></span>
            </div>
        </div>

        <?php if (!empty($post['featured_image'])): ?>
        <div class="article-featured-image">
            <img src="../<?= htmlspecialchars($post['featured_image']) ?>" alt="<?= htmlspecialchars($post['title']) ?>">
        </div>
        <?php endif; ?>

        <!-- Article Body -->
        <div class="article-body">
            <?= $post['body'] ?>
        </div>

        <!-- Post Navigation -->
        <?php if ($prevPost || $nextPost): ?>
        <div class="post-nav">
            <div class="post-nav-item">
                <?php if ($prevPost): ?>
                <span class="post-nav-label">&larr; Předchozí</span>
                <a href="<?= htmlspecialchars($prevPost['slug']) ?>" class="post-nav-title"><?= htmlspecialchars($prevPost['title']) ?></a>
                <?php endif; ?>
            </div>
            <div class="post-nav-item next">
                <?php if ($nextPost): ?>
                <span class="post-nav-label">Další &rarr;</span>
                <a href="<?= htmlspecialchars($nextPost['slug']) ?>" class="post-nav-title"><?= htmlspecialchars($nextPost['title']) ?></a>
                <?php endif; ?>
            </div>
        </div>
        <?php endif; ?>
    </div>

    <?php endif; ?>

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
