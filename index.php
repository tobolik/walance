<?php require_once __DIR__ . '/api/config.php'; $v = defined('APP_VERSION') ? APP_VERSION : '1.0.0'; ?>
<!DOCTYPE html>
<html lang="cs">
<!-- VERSION: <?= htmlspecialchars($v) ?> -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WALANCE — Anatomie udržitelného výkonu</title>
    <meta name="description" content="Zvyšte výkon týmu i sebe. Ne tím, že budete pracovat víc, ale tím, že přestanete bojovat s vlastní biologií. Metoda WALANCE: 7 pilířů pro udržitelný výkon lídrů.">
    <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'%3E%3Crect width='32' height='32' rx='6' fill='%231e293b'/%3E%3Ctext x='16' y='23' text-anchor='middle' font-family='sans-serif' font-weight='700' font-size='20' fill='%234a7c59'%3EW%3C/text%3E%3Ccircle cx='27' cy='24' r='2.5' fill='%234a7c59'/%3E%3C/svg%3E">

    <!-- Open Graph -->
    <meta property="og:title" content="WALANCE — Anatomie udržitelného výkonu">
    <meta property="og:description" content="Váš byznys software je geniální. Ale váš lidský hardware hoří. Metoda WALANCE pro udržitelný výkon lídrů a týmů.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://walance.cz">
    <meta property="og:locale" content="cs_CZ">

    <!-- Google Analytics 4 — nahraďte G-XXXXXXXXXX svým Measurement ID -->
    <?php if (defined('GA_MEASUREMENT_ID') && GA_MEASUREMENT_ID): ?>
    <script async src="https://www.googletagmanager.com/gtag/js?id=<?= htmlspecialchars(GA_MEASUREMENT_ID) ?>"></script>
    <script>
        window.dataLayer = window.dataLayer || [];
        function gtag(){dataLayer.push(arguments);}
        gtag('js', new Date());
        gtag('config', '<?= htmlspecialchars(GA_MEASUREMENT_ID) ?>');
    </script>
    <?php endif; ?>

    <link rel="canonical" href="https://walance.cz/">

    <!-- JSON-LD Structured Data -->
    <script type="application/ld+json">
    {
        "@context": "https://schema.org",
        "@type": "ProfessionalService",
        "name": "WALANCE",
        "description": "Metoda WALANCE: 7 pilířů pro udržitelný výkon lídrů a týmů. Fyzioterapie, mentální koučink a Job Crafting.",
        "url": "https://walance.cz",
        "telephone": "+420601584901",
        "email": "jana@walance.cz",
        "address": {
            "@type": "PostalAddress",
            "streetAddress": "Na Hrázi 1139/13",
            "addressLocality": "Přerov",
            "postalCode": "750 02",
            "addressCountry": "CZ"
        },
        "founder": {
            "@type": "Person",
            "name": "Jana Štěpaníková",
            "jobTitle": "Founder & Performance Consultant"
        },
        "priceRange": "$$",
        "areaServed": "CZ",
        "serviceType": ["Executive Coaching", "Corporate Wellness", "Burnout Prevention"],
        "hasOfferCatalog": {
            "@type": "OfferCatalog",
            "name": "Služby WALANCE",
            "itemListElement": [
                {
                    "@type": "Offer",
                    "name": "OFFICE RESET",
                    "description": "4týdenní týmový program pro firmy. Audit, ergonomie, hybridní mentoring.",
                    "priceCurrency": "CZK",
                    "price": "45000",
                    "priceSpecification": {
                        "@type": "PriceSpecification",
                        "priceCurrency": "CZK",
                        "price": "45000",
                        "description": "od 45 000 Kč za tým"
                    }
                },
                {
                    "@type": "Offer",
                    "name": "CRISIS MENTORING",
                    "description": "Strategická intervence 1:1 pro lídry. Diagnostika, redesign kalendáře, okamžitá úleva.",
                    "priceCurrency": "CZK",
                    "price": "3500",
                    "priceSpecification": {
                        "@type": "PriceSpecification",
                        "priceCurrency": "CZK",
                        "price": "3500",
                        "description": "od 3 500 Kč za 90min session"
                    }
                },
                {
                    "@type": "Offer",
                    "name": "BYZNYS Z POSTELE",
                    "description": "Bezplatná masterclass, e-book a komunita pro lídry.",
                    "priceCurrency": "CZK",
                    "price": "0"
                }
            ]
        }
    }
    </script>

    <!-- Preload critical fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&family=Fraunces:opsz,wght@9..144,400;9..144,600;9..144,700;9..144,900&display=swap&v=<?= htmlspecialchars($v) ?>" rel="stylesheet">

    <style>
        /* ============================================
           WALANCE HOMEPAGE V2 — Minimalist Design
           Inline critical CSS (no Tailwind CDN needed)
           ============================================ */

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

        /* ---- TYPOGRAPHY ---- */
        .font-display { font-family: var(--font-display); }
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

        /* ---- NAVIGATION ---- */
        .nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 100;
            background: rgba(250, 249, 247, 0.92);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--mist);
            transition: all 0.3s ease;
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
            text-transform: uppercase;
            letter-spacing: 0.1em;
            color: var(--ink-light);
            transition: color 0.2s;
        }

        .nav-links a:hover { color: var(--ink); }

        .nav-links a::after,
        .mobile-menu a::after {
            content: '.';
            color: var(--accent);
            font-weight: 900;
            margin-left: 1px;
        }

        .nav-links a.nav-cta::after { color: var(--cream); }

        .nav-cta {
            background: var(--accent);
            color: var(--cream) !important;
            padding: 10px 24px;
            border-radius: 100px;
            font-weight: 700;
            font-size: 0.8125rem;
            letter-spacing: 0.05em;
            transition: all 0.2s;
            box-shadow: 0 4px 16px rgba(13, 148, 136, 0.25);
        }

        .nav-cta:hover {
            background: var(--accent-dark);
            transform: translateY(-1px);
            box-shadow: 0 6px 24px rgba(13, 148, 136, 0.3);
        }

        /* Mobile menu */
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
            left: 0;
            right: 0;
            bottom: 0;
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

        .mobile-menu a:hover { color: var(--accent); }

        /* ---- HERO ---- */
        .hero {
            padding: 160px 0 100px;
            position: relative;
            overflow: hidden;
            min-height: 90vh;
            display: flex;
            align-items: center;
        }

        @media (min-width: 768px) {
            .hero { padding: 180px 0 120px; }
        }

        .hero-accent-line {
            display: inline-block;
            font-size: 0.8125rem;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.25em;
            color: var(--accent);
            margin-bottom: 24px;
        }

        .hero h1 {
            font-size: clamp(2.5rem, 6vw, 4.5rem);
            font-weight: 900;
            letter-spacing: -0.03em;
            margin-bottom: 24px;
            max-width: 680px;
        }

        .hero h1 em {
            font-style: normal;
            color: var(--accent);
        }

        .hero-sub {
            font-size: 1.125rem;
            color: var(--ink-light);
            max-width: 520px;
            margin-bottom: 40px;
            line-height: 1.7;
        }

        @media (min-width: 768px) {
            .hero-sub { font-size: 1.25rem; }
        }

        .hero-sub strong {
            color: var(--ink);
            font-weight: 600;
        }

        .hero-cta-row {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        @media (min-width: 480px) {
            .hero-cta-row { flex-direction: row; gap: 16px; }
        }

        .btn-primary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: var(--accent);
            color: var(--cream);
            padding: 16px 32px;
            border-radius: 100px;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: 0.02em;
            transition: all 0.25s ease;
            box-shadow: 0 4px 24px rgba(13, 148, 136, 0.3);
            border: none;
            cursor: pointer;
            font-family: var(--font-body);
        }

        .btn-primary:hover {
            background: var(--accent-dark);
            transform: translateY(-2px);
            box-shadow: 0 8px 32px rgba(13, 148, 136, 0.35);
        }

        .btn-primary:disabled { opacity: 0.5; cursor: not-allowed; transform: none; box-shadow: none; }

        .btn-secondary {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            background: transparent;
            color: var(--ink);
            padding: 16px 32px;
            border-radius: 100px;
            font-weight: 700;
            font-size: 1rem;
            border: 2px solid var(--mist);
            transition: all 0.25s ease;
            cursor: pointer;
        }

        .btn-secondary:hover {
            border-color: var(--accent);
            color: var(--accent);
            background: rgba(13, 148, 136, 0.04);
        }

        /* Hero decorative */
        .hero-deco {
            position: absolute;
            top: 50%;
            right: -5%;
            transform: translateY(-50%);
            font-family: var(--font-display);
            font-size: 28rem;
            font-weight: 900;
            color: var(--mist);
            opacity: 0.3;
            pointer-events: none;
            user-select: none;
            line-height: 1;
            display: none;
        }

        @media (min-width: 1024px) {
            .hero-deco { display: block; }
        }

        /* ---- TRUST BAR ---- */
        .trust-bar {
            padding: 48px 0;
            border-top: 1px solid var(--mist);
            border-bottom: 1px solid var(--mist);
            background: var(--white);
        }

        .trust-bar-inner {
            display: flex;
            flex-direction: column;
            gap: 32px;
            align-items: center;
            text-align: center;
        }

        @media (min-width: 768px) {
            .trust-bar-inner {
                flex-direction: row;
                justify-content: center;
                gap: 64px;
                text-align: left;
            }
        }

        .trust-item {
            display: flex;
            flex-direction: column;
            align-items: center;
        }

        .trust-number {
            font-family: var(--font-display);
            font-size: 2rem;
            font-weight: 900;
            color: var(--accent);
            line-height: 1;
        }

        .trust-label {
            font-size: 0.8125rem;
            color: var(--ink-muted);
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            margin-top: 4px;
        }

        /* ---- SECTIONS ---- */
        .section { padding: 96px 0; }

        @media (min-width: 768px) {
            .section { padding: 120px 0; }
        }

        .section-label {
            font-size: 0.75rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            color: var(--accent);
            margin-bottom: 16px;
        }

        .section-title {
            font-size: clamp(1.75rem, 4vw, 3rem);
            font-weight: 700;
            letter-spacing: -0.02em;
            margin-bottom: 16px;
        }

        .section-subtitle {
            font-size: 1.125rem;
            color: var(--ink-light);
            max-width: 600px;
            line-height: 1.7;
        }

        .section-header {
            text-align: center;
            margin-bottom: 64px;
        }

        .section-header .section-subtitle {
            margin: 0 auto;
        }

        /* Benefits section */
        .benefits-section {
            background: var(--ink);
            color: var(--cream);
        }

        .benefits-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
        }

        @media (min-width: 768px) {
            .benefits-grid { grid-template-columns: repeat(3, 1fr); }
        }

        .benefit-card {
            padding: 40px 32px;
            background: rgba(250,249,247,0.06);
            border-radius: 24px;
            border: 1px solid rgba(250,249,247,0.1);
            transition: all 0.3s ease;
        }

        .benefit-card:hover {
            background: rgba(250,249,247,0.1);
            transform: translateY(-4px);
        }

        .benefit-tag {
            font-size: 0.6875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: var(--accent);
            margin-bottom: 12px;
        }

        .benefit-card h3 {
            font-size: 1.375rem;
            font-weight: 700;
            margin-bottom: 16px;
            color: var(--cream);
        }

        .benefit-card p {
            font-size: 0.9375rem;
            line-height: 1.7;
            color: rgba(250,249,247,0.7);
        }

        /* Problem cards */
        .cards-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
        }

        @media (min-width: 768px) {
            .cards-grid { grid-template-columns: repeat(3, 1fr); }
        }

        .card {
            padding: 40px 32px;
            background: var(--white);
            border-radius: 24px;
            border: 1px solid var(--mist);
            transition: all 0.3s ease;
        }

        .card:hover {
            transform: translateY(-4px);
            box-shadow: 0 16px 48px rgba(30, 41, 59, 0.08);
            border-color: transparent;
        }

        .card-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 20px;
            background: rgba(13, 148, 136, 0.08);
        }

        .card-icon svg {
            width: 24px;
            height: 24px;
            stroke: var(--accent);
            stroke-width: 2;
            fill: none;
        }

        .card h3 {
            font-size: 1.25rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .card p {
            color: var(--ink-light);
            font-size: 0.9375rem;
            line-height: 1.7;
        }

        .verdict-box {
            margin-top: 48px;
            padding: 40px;
            background: var(--ink);
            border-radius: 24px;
            text-align: center;
        }

        .verdict-box h3 {
            font-size: clamp(1.25rem, 3vw, 1.75rem);
            color: var(--cream);
            margin-bottom: 8px;
        }

        .verdict-box p {
            color: rgba(250, 249, 247, 0.7);
            font-size: 1.0625rem;
        }

        /* ---- SOLUTION / METHOD ---- */
        .solution-section {
            background: var(--white);
        }

        .solution-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
        }

        @media (min-width: 768px) {
            .solution-grid { grid-template-columns: repeat(3, 1fr); }
        }

        .solution-card {
            padding: 48px 32px;
            border-radius: 24px;
            background: var(--mist-light);
            border: 1px solid var(--mist);
            transition: all 0.3s ease;
            position: relative;
            overflow: hidden;
        }

        .solution-card:hover {
            border-color: var(--accent);
            transform: translateY(-2px);
        }

        .solution-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 3px;
            background: var(--accent);
            opacity: 0;
            transition: opacity 0.3s;
        }

        .solution-card:hover::before { opacity: 1; }

        .solution-tag {
            font-size: 0.6875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            color: var(--accent);
            margin-bottom: 16px;
        }

        .solution-card h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 12px;
        }

        .solution-card p {
            color: var(--ink-light);
            line-height: 1.7;
        }

        /* ---- PILLARS (7 pilířů WALANCE) ---- */
        .pillars-section {
            background: var(--ink);
            color: var(--cream);
            padding: 100px 0;
            position: relative;
            overflow: hidden;
        }

        .pillars-section .section-title {
            color: var(--cream);
        }

        .pillars-section .section-title span {
            color: var(--accent);
        }

        .pillars-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 48px;
            align-items: start;
        }

        .pillar-buttons {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .pillar-btn {
            display: flex;
            align-items: center;
            width: 100%;
            text-align: left;
            padding: 18px 20px;
            border-radius: 16px;
            border: none;
            border-left: 4px solid rgba(250,249,247,0.2);
            background: rgba(250,249,247,0.05);
            cursor: pointer;
            transition: all 0.3s ease;
            font-family: var(--font-body);
        }

        .pillar-btn:hover {
            background: rgba(250,249,247,0.1);
        }

        .pillar-btn.active {
            background: rgba(250,249,247,0.1);
            border-left-color: var(--accent);
        }

        .pillar-btn-letter {
            font-family: var(--font-display);
            font-size: 1.5rem;
            font-weight: 900;
            width: 32px;
            margin-right: 20px;
            color: rgba(250,249,247,0.4);
            transition: color 0.3s;
        }

        .pillar-btn:hover .pillar-btn-letter,
        .pillar-btn.active .pillar-btn-letter {
            color: var(--accent);
        }

        .pillar-btn-title {
            font-size: 1.1rem;
            font-weight: 700;
            color: rgba(250,249,247,0.7);
            transition: color 0.3s;
        }

        .pillar-btn:hover .pillar-btn-title,
        .pillar-btn.active .pillar-btn-title {
            color: var(--cream);
        }

        .pillar-btn-arrow {
            margin-left: auto;
            opacity: 0;
            transition: opacity 0.3s;
            color: var(--accent);
        }

        .pillar-btn.active .pillar-btn-arrow {
            opacity: 1;
        }

        .pillar-detail {
            background: rgba(250,249,247,0.08);
            backdrop-filter: blur(8px);
            padding: 48px;
            border-radius: 24px;
            border: 1px solid rgba(250,249,247,0.15);
            box-shadow: 0 25px 50px rgba(0,0,0,0.25);
            min-height: 360px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        .pillar-detail-bg {
            position: absolute;
            top: -20px;
            right: -20px;
            font-family: var(--font-display);
            font-size: 10rem;
            font-weight: 900;
            color: rgba(250,249,247,0.06);
            pointer-events: none;
            user-select: none;
            line-height: 1;
        }

        .pillar-detail-content {
            position: relative;
            z-index: 1;
        }

        .pillar-detail-tag {
            color: var(--accent);
            font-size: 0.8125rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            margin-bottom: 8px;
        }

        .pillar-detail-subtitle {
            font-family: var(--font-display);
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--cream);
            margin-bottom: 20px;
        }

        .pillar-detail-desc {
            font-size: 1.15rem;
            color: rgba(250,249,247,0.75);
            line-height: 1.7;
        }

        @media (max-width: 767px) {
            .pillars-section { padding: 64px 0; }
            .pillars-grid { grid-template-columns: 1fr; gap: 24px; }
            .pillar-detail { padding: 32px; min-height: auto; order: -1; }
            .pillar-detail-bg { font-size: 6rem; }
            .pillar-detail-subtitle { font-size: 1.4rem; }
            .pillar-buttons { gap: 4px; }
            .pillar-btn { padding: 14px 16px; }
        }

        /* ---- STORY ---- */
        .story-section {
            background: var(--ink);
            color: var(--cream);
            position: relative;
            overflow: hidden;
        }

        .story-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 48px;
            align-items: center;
        }

        @media (min-width: 1024px) {
            .story-grid { grid-template-columns: 1fr 1fr; gap: 80px; }
        }

        .story-image-wrap {
            position: relative;
            order: -1;
        }

        @media (min-width: 1024px) {
            .story-image-wrap { order: 0; }
        }

        .story-image-wrap::after {
            content: '';
            position: absolute;
            inset: 8px -8px -8px 8px;
            background: var(--accent);
            border-radius: 20px;
            z-index: 0;
        }

        .story-image {
            width: 100%;
            aspect-ratio: 3/2;
            object-fit: cover;
            border-radius: 20px;
            position: relative;
            z-index: 1;
            display: block;
        }

        .story-label {
            display: inline-block;
            background: var(--accent);
            color: var(--cream);
            font-size: 0.6875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.2em;
            padding: 6px 16px;
            border-radius: 100px;
            margin-bottom: 24px;
        }

        .story-section h2 {
            font-size: clamp(1.75rem, 4vw, 2.75rem);
            font-weight: 700;
            margin-bottom: 32px;
            line-height: 1.2;
        }

        .story-section h2 em {
            font-style: normal;
            color: var(--accent);
        }

        .story-text {
            display: flex;
            flex-direction: column;
            gap: 20px;
        }

        .story-text p {
            font-size: 1.0625rem;
            color: rgba(250, 249, 247, 0.8);
            line-height: 1.7;
        }

        .story-text strong { color: var(--cream); }

        .story-quote {
            border-left: 3px solid var(--accent);
            padding-left: 24px;
            font-style: italic;
            color: var(--cream) !important;
        }

        /* ---- ROI HIGHLIGHT ---- */
        .roi-section {
            background: linear-gradient(135deg, var(--ink) 0%, #0f172a 100%);
            color: var(--cream);
            text-align: center;
            position: relative;
            overflow: hidden;
        }

        .roi-number {
            font-family: var(--font-display);
            font-size: clamp(6rem, 15vw, 12rem);
            font-weight: 900;
            line-height: 1;
            background: linear-gradient(180deg, var(--cream) 0%, rgba(250,249,247,0.4) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .roi-unit {
            font-family: var(--font-display);
            font-size: clamp(1.5rem, 3vw, 2rem);
            font-weight: 700;
            margin-bottom: 16px;
        }

        .roi-text {
            font-size: 1.125rem;
            color: rgba(250, 249, 247, 0.7);
            max-width: 560px;
            margin: 0 auto 32px;
            line-height: 1.7;
        }

        .roi-highlight {
            display: inline-block;
            background: rgba(250, 249, 247, 0.08);
            backdrop-filter: blur(8px);
            border: 1px solid rgba(250, 249, 247, 0.15);
            border-radius: 20px;
            padding: 24px 40px;
        }

        .roi-highlight p {
            font-size: 1.125rem;
            color: var(--cream);
        }

        .roi-highlight strong {
            font-family: var(--font-display);
            font-size: 1.75rem;
            font-weight: 700;
            color: var(--accent-light);
        }

        .roi-highlight small {
            display: block;
            color: rgba(250, 249, 247, 0.5);
            font-size: 0.8125rem;
            margin-top: 4px;
        }

        /* ---- CASE STUDIES (příběhy klientů) ---- */
        .casestudy-list {
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .casestudy-item {
            background: var(--white);
            border: 1px solid var(--mist);
            border-left: 3px solid var(--accent);
            border-radius: 12px;
            overflow: hidden;
            transition: box-shadow 0.3s ease;
        }

        .casestudy-item[open] {
            box-shadow: 0 8px 32px rgba(30, 41, 59, 0.08);
        }

        .casestudy-item summary {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 20px 24px;
            cursor: pointer;
            list-style: none;
        }

        .casestudy-item summary::-webkit-details-marker { display: none; }

        .casestudy-item summary svg {
            width: 20px;
            height: 20px;
            stroke: var(--ink-muted);
            fill: none;
            stroke-width: 2;
            flex-shrink: 0;
            transition: transform 0.3s ease;
        }

        .casestudy-item[open] summary svg {
            transform: rotate(180deg);
        }

        .casestudy-summary { flex: 1; }

        .casestudy-tag {
            font-size: 0.6875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.15em;
            color: var(--accent);
            margin-bottom: 4px;
        }

        .casestudy-summary h3 {
            font-family: var(--font-display);
            font-size: 1.125rem;
            font-weight: 700;
            color: var(--ink);
            margin-bottom: 2px;
        }

        .casestudy-subtitle {
            font-size: 0.8125rem;
            color: var(--ink-muted);
        }

        .casestudy-body {
            padding: 0 24px 24px;
        }

        .casestudy-phase {
            margin-bottom: 20px;
        }

        .casestudy-phase:last-child { margin-bottom: 0; }

        .casestudy-phase h4 {
            font-family: var(--font-display);
            font-size: 0.875rem;
            font-weight: 700;
            color: var(--accent);
            text-transform: uppercase;
            letter-spacing: 0.05em;
            margin-bottom: 8px;
        }

        .casestudy-phase p {
            font-size: 0.9375rem;
            line-height: 1.7;
            color: var(--ink-light);
        }

        .casestudy-phase ul {
            list-style: none;
            padding: 0;
            margin: 8px 0 0;
        }

        .casestudy-phase li {
            font-size: 0.9375rem;
            line-height: 1.7;
            color: var(--ink-light);
            padding-left: 16px;
            position: relative;
            margin-bottom: 8px;
        }

        .casestudy-phase li::before {
            content: '';
            position: absolute;
            left: 0;
            top: 10px;
            width: 6px;
            height: 6px;
            background: var(--accent);
            border-radius: 50%;
        }

        .casestudy-phase li strong {
            color: var(--ink);
        }

        /* ---- PRODUCTS ---- */
        .products-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 24px;
        }

        @media (min-width: 768px) {
            .products-grid { grid-template-columns: repeat(3, 1fr); }
        }

        .product-card {
            border-radius: 24px;
            overflow: hidden;
            display: flex;
            flex-direction: column;
            transition: all 0.3s ease;
        }

        .product-card:hover { transform: translateY(-4px); }

        .product-card--default {
            background: var(--white);
            border: 1px solid var(--mist);
        }

        .product-card--default:hover {
            box-shadow: 0 16px 48px rgba(30, 41, 59, 0.08);
        }

        .product-card--featured {
            background: var(--ink);
            border: 2px solid var(--accent);
            position: relative;
            box-shadow: 0 8px 40px rgba(13, 148, 136, 0.2);
        }

        @media (min-width: 768px) {
            .product-card--featured { transform: translateY(-12px); }
            .product-card--featured:hover { transform: translateY(-16px); }
        }

        .product-badge {
            position: absolute;
            top: 16px;
            right: 16px;
            background: var(--accent);
            color: var(--cream);
            font-size: 0.6875rem;
            font-weight: 700;
            padding: 4px 12px;
            border-radius: 100px;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .product-header {
            padding: 12px 0;
            text-align: center;
            font-size: 0.6875rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.15em;
        }

        .product-card--default .product-header {
            background: var(--mist-light);
            color: var(--ink-muted);
        }

        .product-card--featured .product-header {
            background: rgba(13, 148, 136, 0.2);
            color: var(--cream);
        }

        .product-body {
            padding: 32px;
            flex: 1;
            display: flex;
            flex-direction: column;
        }

        .product-body h3 {
            font-size: 1.375rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .product-card--featured .product-body h3 { color: var(--cream); }

        .product-body .desc {
            font-size: 0.875rem;
            color: var(--ink-light);
            margin-bottom: 24px;
            line-height: 1.6;
        }

        .product-card--featured .product-body .desc { color: rgba(250, 249, 247, 0.7); }

        .product-features {
            list-style: none;
            display: flex;
            flex-direction: column;
            gap: 12px;
            margin-bottom: 32px;
            flex: 1;
        }

        .product-features li {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            font-size: 0.9375rem;
        }

        .product-card--featured .product-features li { color: rgba(250, 249, 247, 0.9); }

        .product-features li svg {
            width: 18px;
            height: 18px;
            flex-shrink: 0;
            margin-top: 2px;
            stroke: var(--accent);
            stroke-width: 2.5;
            fill: none;
        }

        .product-cta {
            display: block;
            width: 100%;
            text-align: center;
            padding: 14px 24px;
            border-radius: 100px;
            font-weight: 700;
            font-size: 0.875rem;
            letter-spacing: 0.02em;
            transition: all 0.25s;
            border: none;
            cursor: pointer;
            font-family: var(--font-body);
        }

        .product-cta--primary {
            background: var(--accent);
            color: var(--cream);
            box-shadow: 0 4px 16px rgba(13, 148, 136, 0.3);
        }

        .product-cta--primary:hover { background: var(--accent-dark); }

        .product-cta--outline {
            background: transparent;
            border: 2px solid var(--mist);
            color: var(--ink);
        }

        .product-cta--outline:hover {
            border-color: var(--accent);
            color: var(--accent);
        }

        .product-note {
            text-align: center;
            font-size: 0.75rem;
            color: var(--ink-muted);
            margin-top: 8px;
        }

        .product-card--featured .product-note { color: rgba(250, 249, 247, 0.5); }

        /* ---- CONTACT ---- */
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr;
            gap: 48px;
        }

        @media (min-width: 768px) {
            .contact-grid { grid-template-columns: 1fr 1fr; gap: 64px; }
        }

        .contact-form-wrap {
            background: var(--white);
            border-radius: 24px;
            border: 1px solid var(--mist);
            padding: 40px;
        }

        .form-group { margin-bottom: 20px; }

        .form-group label {
            display: block;
            font-size: 0.875rem;
            font-weight: 600;
            color: var(--ink);
            margin-bottom: 6px;
        }

        .form-group input,
        .form-group textarea {
            width: 100%;
            padding: 14px 16px;
            border: 1px solid var(--mist);
            border-radius: 12px;
            font-family: var(--font-body);
            font-size: 0.9375rem;
            color: var(--ink);
            background: var(--cream);
            transition: border-color 0.2s, box-shadow 0.2s;
            outline: none;
        }

        .form-group input:focus,
        .form-group textarea:focus {
            border-color: var(--accent);
            box-shadow: 0 0 0 3px rgba(13, 148, 136, 0.1);
        }

        .form-group textarea { resize: vertical; min-height: 120px; }

        .contact-info {
            display: flex;
            flex-direction: column;
            justify-content: center;
            gap: 32px;
        }

        .contact-info h3 {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 8px;
        }

        .contact-info p {
            color: var(--ink-light);
            line-height: 1.7;
        }

        .contact-booking-btn {
            display: flex;
            align-items: center;
            gap: 12px;
            background: var(--ink);
            color: var(--cream);
            padding: 18px 32px;
            border-radius: 16px;
            font-weight: 700;
            transition: all 0.2s;
            border: none;
            cursor: pointer;
            font-size: 1rem;
            font-family: var(--font-body);
            width: fit-content;
        }

        .contact-booking-btn:hover {
            background: var(--accent);
            transform: translateY(-1px);
        }

        .contact-booking-btn svg {
            width: 20px;
            height: 20px;
            stroke: currentColor;
            stroke-width: 2;
            fill: none;
        }

        .contact-email {
            color: var(--accent);
            font-weight: 600;
            font-size: 1rem;
        }

        .contact-email:hover { text-decoration: underline; }

        /* ---- FAQ ---- */
        .faq-section {
            background: var(--mist-light);
        }

        .faq-list {
            max-width: 720px;
            margin: 0 auto;
            display: flex;
            flex-direction: column;
            gap: 12px;
        }

        .faq-item {
            background: var(--white);
            border-radius: 16px;
            border: 1px solid var(--mist);
            overflow: hidden;
            transition: border-color 0.2s;
        }

        .faq-item:hover { border-color: rgba(13, 148, 136, 0.3); }

        .faq-item summary {
            padding: 20px 24px;
            font-weight: 700;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
            list-style: none;
            user-select: none;
        }

        .faq-item summary::-webkit-details-marker { display: none; }

        .faq-item summary svg {
            width: 20px;
            height: 20px;
            stroke: var(--ink-muted);
            stroke-width: 2;
            fill: none;
            transition: transform 0.3s;
            flex-shrink: 0;
            margin-left: 16px;
        }

        .faq-item[open] summary svg { transform: rotate(180deg); }

        .faq-answer {
            padding: 0 24px 20px;
            color: var(--ink-light);
            line-height: 1.7;
        }

        /* ---- FOOTER ---- */
        .footer {
            background: var(--ink);
            color: rgba(250, 249, 247, 0.6);
            padding: 64px 0;
        }

        .footer-inner {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 24px;
            text-align: center;
        }

        @media (min-width: 768px) {
            .footer-inner {
                flex-direction: row;
                justify-content: space-between;
                text-align: left;
            }
        }

        .footer-brand {
            font-family: var(--font-display);
            font-size: 1.25rem;
            font-weight: 700;
            color: var(--cream);
            margin-bottom: 4px;
        }

        .footer-tagline {
            font-size: 0.875rem;
        }

        .footer-links {
            display: flex;
            gap: 20px;
        }

        .footer-links a {
            color: rgba(250, 249, 247, 0.5);
            transition: color 0.2s;
        }

        .footer-links a:hover { color: var(--accent); }

        .footer-links svg {
            width: 22px;
            height: 22px;
            stroke: currentColor;
            stroke-width: 2;
            fill: none;
        }

        .footer-copy {
            font-size: 0.8125rem;
        }

        /* ---- ANIMATIONS ---- */
        .fade-in {
            opacity: 0;
            transform: translateY(20px);
            transition: opacity 0.6s ease-out, transform 0.6s ease-out;
        }

        .fade-in.visible {
            opacity: 1;
            transform: translateY(0);
        }

        /* ---- BOOKING MODAL ---- */
        #cal-grid > div {
            aspect-ratio: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
            font-size: 0.875rem;
            font-weight: 600;
            transition: all 0.15s ease;
        }

        .cal-day-clickable { cursor: pointer; }
        .cal-day-clickable:hover { opacity: 0.85; transform: scale(1.05); }
        .cal-day-selected { box-shadow: 0 0 0 2px var(--accent) !important; }

        .time-slot-btn {
            padding: 10px 20px;
            border-radius: 12px;
            border: 1px solid var(--mist);
            background: var(--mist-light);
            font-size: 0.875rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.15s ease;
            color: var(--ink);
            font-family: var(--font-body);
        }

        .time-slot-btn:hover { border-color: var(--accent); color: var(--accent); }
        .time-slot-selected { background: var(--accent) !important; color: var(--cream) !important; border-color: var(--accent) !important; }

        /* ---- UTILITIES ---- */
        .text-center { text-align: center; }
        .mt-2 { margin-top: 8px; }
        .mt-4 { margin-top: 16px; }
        .mb-2 { margin-bottom: 8px; }
    </style>
</head>
<body>

    <!-- ============ NAVIGATION ============ -->
    <nav class="nav">
        <div class="container nav-inner">
            <a href="#pillars" class="nav-logo">WALANCE<span>.</span></a>
            <ul class="nav-links">
                <li><a href="#problem">Problém</a></li>
                <li><a href="#method">Metoda</a></li>
                <li><a href="#story">Příběh</a></li>
                <li><a href="#products">Nabídka</a></li>
                <li><a href="#contact">Kontakt</a></li>
                <li><a href="#contact" class="nav-cta">Konzultace zdarma</a></li>
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
            <a href="#problem" class="mobile-link">Problém</a>
            <a href="#method" class="mobile-link">Metoda</a>
            <a href="#story" class="mobile-link">Příběh</a>
            <a href="#products" class="mobile-link">Nabídka</a>
            <a href="#contact" class="mobile-link">Kontakt</a>
            <a href="#contact" class="mobile-link" style="color: var(--accent);">Konzultace zdarma</a>
        </div>
    </nav>

    <!-- ============ HERO ============ -->
    <header class="hero">
        <div class="hero-deco">W</div>
        <div class="container">
            <span class="hero-accent-line">Anatomie udržitelného výkonu</span>
            <h1>Váš lidský<br><em>hardware hoří.</em></h1>
            <p class="hero-sub">
                Zvyšte výkon týmu i sebe ne tím, že budete pracovat víc, ale tím, že <strong>přestanete bojovat s vlastní biologií</strong>.
            </p>
            <div class="hero-cta-row">
                <a href="#products" class="btn-primary">
                    ZASTAVIT VYHOŘENÍ
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                </a>
                <a href="#story" class="btn-secondary">PŘÍBĚH ZAKLADATELKY</a>
            </div>
        </div>
    </header>

    <!-- ============ TRUST BAR ============ -->
    <section class="trust-bar fade-in">
        <div class="container">
            <div class="trust-bar-inner">
                <div class="trust-item">
                    <span class="trust-number">7</span>
                    <span class="trust-label">Pilířů metody</span>
                </div>
                <div class="trust-item">
                    <span class="trust-number">60 min</span>
                    <span class="trust-label">Ušetřený čas denně</span>
                </div>
                <div class="trust-item">
                    <span class="trust-number">5</span>
                    <span class="trust-label">Ověřených případových studií</span>
                </div>
                <div class="trust-item">
                    <span class="trust-number">4 týdny</span>
                    <span class="trust-label">Program Office Reset</span>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ PROBLEM ============ -->
    <section id="problem" class="section">
        <div class="container">
            <div class="section-header fade-in">
                <p class="section-label">Poznáváte se?</p>
                <h2 class="section-title">Výkon neklesá kvůli neschopnosti.<br>Klesá kvůli přetížení.</h2>
                <p class="section-subtitle">
                    Jste schopní lidé v náročných rolích. Problém není v tom, co děláte — ale v tom, že váš systém běží bez údržby.
                </p>
            </div>

            <div class="cards-grid">
                <div class="card fade-in">
                    <div class="card-icon">
                        <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a9 9 0 0 0-9 9c0 3.9 2.5 7.1 6 8.4V21a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1v-1.6c3.5-1.3 6-4.5 6-8.4a9 9 0 0 0-9-9z"/><path d="M10 17h4"/></svg>
                    </div>
                    <h3>Rozhodujete pod mlhou</h3>
                    <p>Neustálý stres a neschopnost „vypnout" po práci. Hlava jede nonstop, ale místo jasných rozhodnutí generuje zmatky. Strategické myšlení ustupuje hašení požárů.</p>
                </div>
                <div class="card fade-in">
                    <div class="card-icon" style="background: rgba(90, 125, 90, 0.1);">
                        <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" style="stroke: var(--sage);"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    </div>
                    <h3>Tělo táhne za ruční brzdu</h3>
                    <p>Bolest zad, chronická únava, spadlá energie po obědě. Kompenzujete to kávou a vůlí, ale to není řešení — to je odklad.</p>
                </div>
                <div class="card fade-in">
                    <div class="card-icon">
                        <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <h3>Práce vysává místo nabíjí</h3>
                    <p>Děláte hodně, ale málo z toho odpovídá tomu, v čem jste opravdu dobří. Role není nastavená na vaše silné stránky. Výsledek? Dřete víc, ale výsledky neodpovídají úsilí.</p>
                </div>
            </div>

            <div class="verdict-box fade-in">
                <h3>Tohle se dá změnit. Celé. Systematicky.</h3>
                <p>Metoda WALANCE pracuje se všemi třemi vrstvami současně — s tělem, myslí i nastavením práce. Protože když opravíte jen jednu, ty další vás stáhnou zpět.</p>
            </div>
        </div>
    </section>

    <!-- ============ SOLUTION / METHOD ============ -->
    <section id="method" class="section solution-section">
        <div class="container">
            <div class="section-header fade-in">
                <p class="section-label">Metoda WALANCE</p>
                <h2 class="section-title">Neučím vás cvičit.<br>Učím vás pracovat.</h2>
                <p class="section-subtitle">
                    Metoda WALANCE není wellness benefit. Je to <strong>strategický manuál k vašemu výkonu</strong>. Propojuje fyzioterapii (hardware), mentální koučink (software) a Job Crafting (systém práce).
                </p>
            </div>

            <div class="solution-grid">
                <div class="solution-card fade-in">
                    <p class="solution-tag">Vrstva 1</p>
                    <h3>Hardware (Tělo &amp; Fyzio)</h3>
                    <p>Opravíme šasi, aby motor běžel naplno. Bolest zad je mechanická závada, která škrtí výkon hlavy. Vyladíme sezení a pohyb. Tělo přestane „drhnout" a stane se pevnou konstrukcí, která bezpečně unese jakékoliv tempo.</p>
                </div>
                <div class="solution-card fade-in">
                    <p class="solution-tag">Vrstva 2</p>
                    <h3>Software (Hlava &amp; Koučink)</h3>
                    <p>Odviruji procesor. Rozhodovací paralýza a neschopnost vypnout jsou jako zasekané aplikace na pozadí. Pomocí koučinku uvolním vaši mentální kapacitu. Získáte čistou hlavu pro strategická rozhodnutí, ne pro stres.</p>
                </div>
                <div class="solution-card fade-in">
                    <p class="solution-tag">Vrstva 3</p>
                    <h3>Operační systém (Návyky &amp; Job Crafting)</h3>
                    <p>Automatizace zdraví a práce. Mikrozměny a rituály nastavím tak, aby běžely na pozadí a šetřily energii. Pomocí Job Craftingu designuji práci tak, aby vás nabíjela. Sladím vaše silné stránky s náplní role.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ PILLARS (7 pilířů WALANCE) ============ -->
    <section id="pillars" class="pillars-section">
        <div class="container">
            <div class="section-header fade-in" style="text-align:center; margin-bottom:56px;">
                <h2 class="section-title"><span>WALANCE</span> — Anatomie udržitelného lídra</h2>
                <p class="section-subtitle">7 pilířů, 7 písmen. Každé stojí za jedním principem.</p>
            </div>

            <div class="pillars-grid">
                <div class="pillar-buttons" id="pillar-buttons">
                    <button class="pillar-btn active" data-index="0">
                        <span class="pillar-btn-letter">W</span>
                        <span class="pillar-btn-title">Walk &amp; Work</span>
                        <span class="pillar-btn-arrow">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </span>
                    </button>
                    <button class="pillar-btn" data-index="1">
                        <span class="pillar-btn-letter">A</span>
                        <span class="pillar-btn-title">Awareness</span>
                        <span class="pillar-btn-arrow">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </span>
                    </button>
                    <button class="pillar-btn" data-index="2">
                        <span class="pillar-btn-letter">L</span>
                        <span class="pillar-btn-title">Longevity</span>
                        <span class="pillar-btn-arrow">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </span>
                    </button>
                    <button class="pillar-btn" data-index="3">
                        <span class="pillar-btn-letter">A</span>
                        <span class="pillar-btn-title">Alignment</span>
                        <span class="pillar-btn-arrow">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </span>
                    </button>
                    <button class="pillar-btn" data-index="4">
                        <span class="pillar-btn-letter">N</span>
                        <span class="pillar-btn-title">New Habits</span>
                        <span class="pillar-btn-arrow">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </span>
                    </button>
                    <button class="pillar-btn" data-index="5">
                        <span class="pillar-btn-letter">C</span>
                        <span class="pillar-btn-title">Clarity</span>
                        <span class="pillar-btn-arrow">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </span>
                    </button>
                    <button class="pillar-btn" data-index="6">
                        <span class="pillar-btn-letter">E</span>
                        <span class="pillar-btn-title">Energy</span>
                        <span class="pillar-btn-arrow">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="5" y1="12" x2="19" y2="12"/><polyline points="12 5 19 12 12 19"/></svg>
                        </span>
                    </button>
                </div>

                <div class="pillar-detail">
                    <div class="pillar-detail-bg" id="p-bg-letter">W</div>
                    <div class="pillar-detail-content">
                        <p class="pillar-detail-tag" id="p-title">Walk &amp; Work</p>
                        <h3 class="pillar-detail-subtitle" id="p-subtitle">Pohyb jako nástroj myšlení</h3>
                        <p class="pillar-detail-desc" id="p-desc">Nejtěžší rozhodnutí se nedělají na židli. Chůze zvyšuje prokrvení mozku a kreativitu o 60 %. Aktivujte svůj procesor v pohybu a nechte nápady volně proudit.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ STORY ============ -->
    <section id="story" class="section story-section">
        <div class="container">
            <div class="story-grid">
                <div class="fade-in">
                    <span class="story-label">Případová studie: Klient 0</span>
                    <h2>Teorie končí ve chvíli, kdy si zraníte koleno.<br><em>Já to teď žiju.</em></h2>
                    <div class="story-text">
                        <p>Jmenuji se <strong>Jana</strong>. Roky učím lídry, jak nevyhořet a držet vysoký výkon. Pak přišla nečekaná lekce pokory.</p>
                        <p>V lednu 2026 mě těžký úraz kolene upoutal na lůžko. Mohla jsem projekty pozastavit a na měsíce zmizet z trhu. Místo toho jsem se stala <strong>Klientem 0</strong>. Rozhodla jsem se otestovat metodu WALANCE v těch nejextrémnějších podmínkách.</p>
                        <p class="story-quote">„Řídím byznys horizontálně. A funguje to. Moje hlava je díky systému ostřejší než kdy dřív, přestože tělo je v opravě. Moje omezení se stalo laboratoří pro vaši efektivitu."</p>
                        <p>Pokud dokážu udržet vysoké pracovní tempo, jasnou mysl a strategický nadhled já z postele, garantuji vám, že váš systém vyladím kdekoli — v kanceláři i pod největším tlakem.</p>
                    </div>
                </div>
                <div class="story-image-wrap fade-in">
                    <img src="assets/images/hero-story-jana.jpg"
                         alt="Jana Štěpaníková — zakladatelka WALANCE"
                         class="story-image"
                         width="1800" height="1200"
                         loading="lazy"
                         decoding="async"
                         fetchpriority="low">
                </div>
            </div>
        </div>
    </section>

    <!-- ============ ROI ============ -->
    <section class="section roi-section fade-in">
        <div class="container">
            <p class="section-label" style="color: rgba(250,249,247,0.5);">Proč se to vyplatí</p>
            <div class="roi-number">3 v 1</div>
            <p class="roi-unit">METODA</p>
            <p class="roi-text">Fyzioterapie, koučink a Job Crafting v jednom systému. Místo tří specialistů pracujete s jedním člověkem, který vidí celý obraz.</p>
            <div class="roi-highlight">
                <p><strong>Méně mikrostresů, víc čisté kapacity.</strong> Když přestanete bojovat s bolestí zad, únavou a rozhodovací paralýzou, uvolníte energii pro to, co opravdu potřebujete řešit.</p>
                <small>WALANCE není wellness benefit. Je to investice do kapacity vašich lidí — vašeho nejdražšího aktiva.</small>
            </div>
        </div>
    </section>

    <!-- ============ PŘÍBĚHY KLIENTŮ ============ -->
    <section id="results" class="section" style="background: var(--white);">
        <div class="container">
            <div class="section-header fade-in">
                <p class="section-label">Reálné výsledky</p>
                <h2 class="section-title">5 lidí, 5 příběhů, 1 metoda</h2>
                <p class="section-subtitle">Každý přišel s jiným problémem. Všichni odcházeli s funkčním systémem.</p>
            </div>
            <div class="casestudy-list">

                <!-- PŘÍBĚH 1 — Markéta -->
                <details class="casestudy-item fade-in">
                    <summary>
                        <div class="casestudy-summary">
                            <p class="casestudy-tag">Deprese &amp; dechové bloky</p>
                            <h3>Markéta — Konečně se nadechnout</h3>
                            <p class="casestudy-subtitle">Jak dech a pohyb rozpustily letité úzkosti</p>
                        </div>
                        <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </summary>
                    <div class="casestudy-body">
                        <div class="casestudy-phase">
                            <h4>Výchozí stav: Život v „uzlíčku"</h4>
                            <p>Když ke mně tato klientka (shodou okolností skvělá učitelka) přišla poprvé, byla zosobněním vnitřního stažení. Trpěla těžkými depresemi, brala silná antidepresiva a každé jaro pro ni bylo kritické — cítila fyzickou nemožnost se nadechnout. Přestože svou práci s dětmi milovala, její tělo i mysl byly v totální křeči. Byla na nemocenské, vyčerpaná a odpojená od sebe sama.</p>
                        </div>
                        <div class="casestudy-phase">
                            <h4>Cesta WALANCE</h4>
                            <p>Nešli jsme na to jen přes „povídání", ale vzali jsme to skutečně holisticky. Propojili jsme fyzioterapii s mentálním nastavením:</p>
                            <ul>
                                <li><strong>Uvolnění těla:</strong> Začali jsme tejpováním, cvičením s overbally a expandéry, abychom fyzicky otevřeli hrudník.</li>
                                <li><strong>Pohyb jako lék:</strong> Nordic Walking, kde se pohyb v přírodě stal prostorem pro mentoring.</li>
                                <li><strong>Strategie v práci:</strong> Upravili jsme její přístup k výuce. Naučila se nastavit hodiny tak, aby ji nevyčerpávaly, ale dobíjely.</li>
                                <li><strong>Dech jako kotva:</strong> Intenzivně jsme trénovali dechové techniky, které jí umožnily „roztáhnout ruce" a nebát se nadechnout světa.</li>
                            </ul>
                        </div>
                        <div class="casestudy-phase">
                            <h4>Bod zlomu</h4>
                            <p>Nejsilnější moment přišel, když si uvědomila, že svůj stav může ovlivnit sama. Že to sevření není její osud, ale vzorec, který se dá změnit. Zjistila, že když se dokáže fyzicky narovnat a nadechnout, její sebevědomí i psychická odolnost vystřelí nahoru.</p>
                        </div>
                        <div class="casestudy-phase">
                            <h4>Nová realita</h4>
                            <p>Dnes je z ní jiná žena. Depresivní stavy ustoupily, antidepresiva už nejsou hlavním tématem a ona se vrátila ke své vášni — k dětem — s úplně novou energií. I když už spolu dnes pracujeme převážně online, dokáže své tělo vnímat natolik citlivě, že případné přicházející napětí pozná včas a sama ho pomocí naučených technik „rozpustí". Už neuhýbá, už se neschovává. Žije v rovnováze.</p>
                        </div>
                    </div>
                </details>

                <!-- PŘÍBĚH 2 — Renata -->
                <details class="casestudy-item fade-in">
                    <summary>
                        <div class="casestudy-summary">
                            <p class="casestudy-tag">Ztráta sebevědomí &amp; toxické prostředí</p>
                            <h3>Renata — Vlastní síla nad systémem</h3>
                            <p class="casestudy-subtitle">Cesta od role oběti k pevným hranicím a sebeúctě</p>
                        </div>
                        <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </summary>
                    <div class="casestudy-body">
                        <div class="casestudy-phase">
                            <h4>Výchozí stav: Neviditelná bolest</h4>
                            <p>Přišla ve chvíli, kdy ji „systém" zlomil. Trpěla bolestmi, které lékaři ignorovali a označovali ji za simulanta. K tomu se přidal rozpad toxického vztahu a pocit, že její tělo je nepřítel. Navenek působila sebevědomě, ale její tělo vyprávělo příběh o naprostém stažení a rezignaci. Pracovala jako jeřábnice, vystavena chladu i drsnému kolektivu.</p>
                        </div>
                        <div class="casestudy-phase">
                            <h4>Cesta WALANCE</h4>
                            <p>Klíčové bylo pochopit, že žádný „zázračný proutek" zvenčí neexistuje:</p>
                            <ul>
                                <li><strong>Fyzické nastavení v extrému:</strong> Řešili jsme ergonomii a nastavení těla přímo v kabině jeřábu, aby ji práce v zimě a nepohodlí neničila.</li>
                                <li><strong>Mentální štít:</strong> Pracovali jsme na tom, jak se ráno naladit, aby ji toxický kolektiv nevyčerpával. Naučila se rozlišovat mezi svými pocity a problémy druhých.</li>
                                <li><strong>Seznámení se s vlastním tělem:</strong> Přes cvičení a úpravu stravy začala poprvé v životě své tělo skutečně vnímat, ne ho jen „používat".</li>
                                <li><strong>Online podpora:</strong> Kdykoliv se objevila krize, využili jsme online konzultace pro okamžité srovnání směru.</li>
                            </ul>
                        </div>
                        <div class="casestudy-phase">
                            <h4>Bod zlomu: „Mám se ráda"</h4>
                            <p>Zlom nenastal, když zmizela veškerá bolest, ale v momentě, kdy prohlásila: „Začínám mít ráda sama sebe a cítím ke svému tělu úctu." Přestala čekat, až ji zachrání lékaři nebo okolí, a začala se zachraňovat sama.</p>
                        </div>
                        <div class="casestudy-phase">
                            <h4>Nová realita</h4>
                            <p>Dnes je to žena, která stojí pevně na nohou. V práci už nenechá útoky ostatních proniknout pod kůži — chápe, že je to jejich boj, ne její. I když tělo občas po starých traumatech zastávkuje, ona už ví, co s tím. Už není obětí okolností, ale tvůrkyní své vlastní rovnováhy.</p>
                        </div>
                    </div>
                </details>

                <!-- PŘÍBĚH 3 — David -->
                <details class="casestudy-item fade-in">
                    <summary>
                        <div class="casestudy-summary">
                            <p class="casestudy-tag">Stres &amp; kariérní nesoulad</p>
                            <h3>David — Když tělo křičí „STOP"</h3>
                            <p class="casestudy-subtitle">Proč bolest ramene vyřešila až změna kariéry</p>
                        </div>
                        <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </summary>
                    <div class="casestudy-body">
                        <div class="casestudy-phase">
                            <h4>Výchozí stav: Úspěch vykoupený křečí</h4>
                            <p>Do ordinace mi vstoupil prototyp úspěchu: mladý, charismatický manažer, sportovec, vedoucí týmu. Přišel s „prostou" bolestí ramene. Ale viděla jsem víc než jen zánět šlach. Viděla jsem muže v permanentním stresu, sešněrovaného v roli, která mu neseděla. I přes naučené úsměvy jeho tělo vysílalo jasný signál: „Takhle už to dál nejde."</p>
                        </div>
                        <div class="casestudy-phase">
                            <h4>Cesta WALANCE</h4>
                            <p>Šli jsme do hloubky:</p>
                            <ul>
                                <li><strong>Diagnostika těla i role:</strong> Zatímco jsme tejpovali a stabilizovali rameno, rozebírali jsme jeho pracovní dny. Jako pozorovatelka na jeho poradě jsem viděla ten rozpor — byl skvělý odborník, ale v roli šéfa byl v křeči. Rozený sólista nucený hrát týmovou hru.</li>
                                <li><strong>Analýza komfortní zóny:</strong> „Proč děláš něco, co tě vnitřně láme?" Zjistili jsme, že jeho ambice ho dohnaly do pozice, která nebyla v souladu s jeho přirozeností.</li>
                                <li><strong>Fyzické uvolnění:</strong> Propojili jsme dechové techniky s posturálním nastavením. Jakmile začal chápat příčinu svého stresu, i jeho rameno začalo „povolovat".</li>
                            </ul>
                        </div>
                        <div class="casestudy-phase">
                            <h4>Bod zlomu: Odvaha k autenticitě</h4>
                            <p>Zásadní moment nastal, když si přiznal, že nepotřebuje vést lidi, aby byl úspěšný. Pochopil, že jeho síla je v individualitě, ne v managementu. Toto uvědomění doslova „shodilo balvan" z jeho ramen.</p>
                        </div>
                        <div class="casestudy-phase">
                            <h4>Nová realita</h4>
                            <p>Dnes je z něj úspěšný podnikatel na volné noze. Už to není ten vystresovaný kluk. Je to chlap, ze kterého sálá opravdové, vnitřní sebevědomí. Bolest ramene zmizela — ne proto, že by bral prášky, ale proto, že přestal bojovat sám se sebou. Našel svou WALANCE.</p>
                        </div>
                    </div>
                </details>

                <!-- PŘÍBĚH 4 — Vašek -->
                <details class="casestudy-item fade-in">
                    <summary>
                        <div class="casestudy-summary">
                            <p class="casestudy-tag">Úraz mozku &amp; hledání identity</p>
                            <h3>Vašek — Odvaha k pravdě</h3>
                            <p class="casestudy-subtitle">Jak po těžkém úrazu mozku najít sebe sama a vnitřní klid</p>
                        </div>
                        <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </summary>
                    <div class="casestudy-body">
                        <div class="casestudy-phase">
                            <h4>Výchozí stav: Život v mlze a bolesti</h4>
                            <p>Vašek ke mně přišel ve svých 20 letech se zavazadlem, které by bylo těžké i pro padesátníka. Po tragickém úrazu hlavy a náročné operaci mozku trpěl chronickými bolestmi, hučením v uších a neustálou únavou. Jako vedlejší (téměř zázračný) efekt úrazu se u něj projevila schopnost hrát na klavír, na který dříve nikdy nehrál. Přesto byl ztracený — fyzicky i duševně. Nevěděl, kým je, kam patří, a schovával se v životě, který nebyl jeho.</p>
                        </div>
                        <div class="casestudy-phase">
                            <h4>Cesta WALANCE</h4>
                            <p>Metodu jsme aplikovali v několika rovinách, které se postupně propojily:</p>
                            <ul>
                                <li><strong>Fyzické uvolnění a režim:</strong> Práce s jizvou po operaci, srovnání postavení hlavy a řešení „banalit" jako pitný režim. Chronické bolesti hlavy živil i extrémní nedostatek vody a nadmíra kofeinu.</li>
                                <li><strong>Job Crafting:</strong> Vašek hledal své místo v pracovním světě. Pomocí mentoringu jsme hledali roli, která nebude dráždit jeho citlivý nervový systém.</li>
                                <li><strong>Odvaha k pravdě:</strong> Nejtěžší téma — jeho identita. Vašek žil v partnerství se ženou, ale uvnitř věděl, že je gay. Strach z odmítnutí a pocit viny mu způsobovaly obrovské úzkosti, které se projevovaly i fyzickou bolestí.</li>
                            </ul>
                        </div>
                        <div class="casestudy-phase">
                            <h4>Bod zlomu: Rozhovor, který osvobozuje</h4>
                            <p>Společně jsme se připravili na klíčový životní krok. Pomohla jsem mu nastavit vnitřní klid a připravit se na upřímný rozhovor s přítelkyní. Nakonec jsme celou situaci rozebírali i společně v bezpečné atmosféře mé poradny. Ten moment, kdy pravda vyšla ven a byla přijata, byl pro něj skutečným uzdravením.</p>
                        </div>
                        <div class="casestudy-phase">
                            <h4>Nová realita</h4>
                            <p>Vašek nakonec našel odvahu žít autenticky. S bývalou partnerkou zůstali blízkými přáteli, našel si práci, která ho naplňuje, a hlavně — přestal se bát sám sebe. Bolesti hlavy ustoupily do pozadí, protože zmizelo to největší vnitřní pnutí. Ukázalo se, že „rozvázat" uzel v životě je stejně důležité jako uvolnit stažený sval.</p>
                        </div>
                    </div>
                </details>

                <!-- PŘÍBĚH 5 — Zdeňka -->
                <details class="casestudy-item fade-in">
                    <summary>
                        <div class="casestudy-summary">
                            <p class="casestudy-tag">Mrtvice &amp; ztráta sebeúcty</p>
                            <h3>Zdeňka — Hranice jsou jen v naší hlavě</h3>
                            <p class="casestudy-subtitle">Jak jedno setkání v nemocnici vrátilo chuť do života a sebeúctu</p>
                        </div>
                        <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </summary>
                    <div class="casestudy-body">
                        <div class="casestudy-phase">
                            <h4>Výchozí stav: „Já jsem blbá…"</h4>
                            <p>Tento příběh se neodehrál v mé poradně, ale v nemocničním pokoji. Ležela jsem tam po operaci kolene, sama v bolestech, a vedle mě ležela paní Zdenička. Byla po mrtvici a čekala na operaci krčku. Neustále o sobě opakovala: „Já jsem blbá, já nic nezvládnu." Byla to uťápnutá žena bez kouska sebevědomí, kterou systém a její vlastní stav přesvědčily o tom, že už za nic nestojí.</p>
                        </div>
                        <div class="casestudy-phase">
                            <h4>Moment WALANCE: Síla slova a motivace</h4>
                            <p>I když mi nebylo dobře, nemohla jsem to nechat jen tak:</p>
                            <ul>
                                <li><strong>Změna paradigmatu:</strong> Vysvětlila jsem jí, že její mozek jen teď pracuje jiným tempem. Že pomalost není hloupost.</li>
                                <li><strong>Malá vítězství:</strong> Motivovala jsem ji, aby si sama podala sklenici vody, na kterou dříve jen odevzdaně koukala. Ten záblesk v jejích očích, když zjistila, že to dokáže, byl silnější než jakékoliv léky.</li>
                                <li><strong>Lidskost jako metoda:</strong> Smály jsme se, plakaly a já jsem ji z postele mentorovala, jak se k sobě chovat s laskavostí, i když tělo zrovna neposlouchá.</li>
                            </ul>
                        </div>
                        <div class="casestudy-phase">
                            <h4>Bod zlomu: Nová identita</h4>
                            <p>Zdeňka pochopila, že její hodnota nezmizela s nemocí. Slíbila mi, že už o sobě nikdy neřekne, že je blbá. Našla v sobě sílu se personálu i světu postavit s větou: „Jsem jen pomalejší, dejte mi čas."</p>
                        </div>
                        <div class="casestudy-phase">
                            <h4>Proč to dělám</h4>
                            <p>Tento zážitek mi potvrdil, že metoda WALANCE není jen práce. Je to dar vidět v lidech to dobré, i když oni sami to už nevidí. Pomáhám lidem najít jejich sebehodnotu a sílu, ať už jsou v manažerském křesle, nebo na nemocničním lůžku. Protože rovnováha začíná v hlavě — v tom, co si o sobě říkáme.</p>
                        </div>
                    </div>
                </details>

            </div>
        </div>
    </section>

    <!-- ============ PRODUCTS ============ -->
    <section id="products" class="section">
        <div class="container">
            <div class="section-header fade-in">
                <p class="section-label">Spolupráce</p>
                <h2 class="section-title">Jak můžeme spolupracovat?</h2>
                <p class="section-subtitle">Strategie šitá na míru vaší aktuální situaci.</p>
            </div>

            <div class="products-grid">
                <!-- Product 1: Byznys z postele (zdarma) -->
                <div class="product-card product-card--default fade-in">
                    <div class="product-header" style="color: var(--sage);">Start (zdarma)</div>
                    <div class="product-body">
                        <h3>BYZNYS Z POSTELE</h3>
                        <p class="desc">Manuál přežití pro lídry. Nečekejte, až vás systém vypne natvrdo. Připojte se k živému vysílání a získejte návod, jak řídit firmu a nezbláznit se, i když tělo stávkuje.</p>
                        <p style="margin-bottom: 20px;"><span style="font-family: var(--font-display); font-size: 1.75rem; font-weight: 700; color: var(--accent);">0 Kč</span><br><span style="font-size: 0.8125rem; color: var(--ink-muted);">e-book + masterclass + komunita zdarma</span></p>
                        <ul class="product-features">
                            <li>
                                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>LIVE Masterclass: strategie horizontálního řízení</span>
                            </li>
                            <li>
                                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>E-book: Krizový manuál Klienta 0</span>
                            </li>
                            <li>
                                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>3 okamžité techniky pro úlevu od bolesti a stresu</span>
                            </li>
                            <li>
                                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>Vstup do komunity lídrů, kteří odmítají vyhořet</span>
                            </li>
                        </ul>
                        <a href="#contact" class="product-cta product-cta--outline">
                            VSTOUPIT DO PROGRAMU ZDARMA
                        </a>
                    </div>
                </div>

                <!-- Product 2: Crisis Mentoring (Featured) -->
                <div class="product-card product-card--featured fade-in">
                    <span class="product-badge">Doporučuji</span>
                    <div class="product-header">Pro lídry (1-on-1)</div>
                    <div class="product-body">
                        <h3>CRISIS MENTORING</h3>
                        <p class="desc">Strategická intervence 1:1. Krizové řízení vás samotných pod vedením Klienta 0. Hloubková diagnostika vašeho systému a okamžité nastavení podmínek pro udržitelné vládnutí.</p>
                        <p style="margin-bottom: 20px;"><span style="font-family: var(--font-display); font-size: 1.75rem; font-weight: 700; color: var(--cream);">od 3 500 Kč</span><br><span style="font-size: 0.8125rem; color: rgba(250,249,247,0.5);">za 90min session / balíčky od 9 000 Kč</span></p>
                        <ul class="product-features">
                            <li>
                                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>Hloubková diagnostika: Tělo, Hlava, Práce</span>
                            </li>
                            <li>
                                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>Redesign kalendáře: plánování podle energie, ne času</span>
                            </li>
                            <li>
                                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>Okamžitá úleva od tlaku a fyzické bolesti</span>
                            </li>
                        </ul>
                        <button type="button" onclick="openBookingModal()" class="product-cta product-cta--primary">
                            REZERVOVAT TERMÍN
                        </button>
                        <p class="product-note">Úvodní konzultace (30 min) zdarma.</p>
                    </div>
                </div>

                <!-- Product 3: Office Reset (pro firmy) -->
                <div class="product-card product-card--default fade-in">
                    <div class="product-header">Pro firmy</div>
                    <div class="product-body">
                        <h3>OFFICE RESET&trade;</h3>
                        <p class="desc">Systematické řešení výkonu a zdraví. Zastavíme úniky energie a zvýšíme kapacitu vašich lidí. Fyziologie, ergonomie a výsledky podložené auditem.</p>
                        <p style="margin-bottom: 20px;"><span style="font-family: var(--font-display); font-size: 1.75rem; font-weight: 700; color: var(--ink);">od 45 000 Kč</span><br><span style="font-size: 0.8125rem; color: var(--ink-muted);">za tým / 4týdenní program</span></p>
                        <ul class="product-features">
                            <li>
                                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>Audit „energetických děr" (měření kondice týmu)</span>
                            </li>
                            <li>
                                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>Fyzio-ergonomie: nastavení těl a židlí pro výkon</span>
                            </li>
                            <li>
                                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>4 týdny hybridního mentoringu pro fixaci návyků</span>
                            </li>
                        </ul>
                        <a href="mailto:jana@walance.cz?subject=Popt%C3%A1vka%20Office%20Reset" class="product-cta product-cta--outline">
                            POPTAT AUDIT
                        </a>
                        <p class="product-note">Finální cena závisí na velikosti týmu a rozsahu auditu.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ CONTACT ============ -->
    <section id="contact" class="section">
        <div class="container">
            <div class="section-header fade-in">
                <p class="section-label">Kontakt</p>
                <h2 class="section-title">Napište mi</h2>
                <p class="section-subtitle">Zaujala vás metoda WALANCE? Ozvěte se. Ať už řešíte OFFICE RESET&trade; pro firmu, CRISIS MENTORING pro sebe, nebo máte jen dotaz k termínům — jsem tady pro vás.</p>
            </div>

            <div class="contact-grid">
                <div class="contact-form-wrap fade-in">
                    <h3 style="font-size: 1.25rem; margin-bottom: 24px;">Kontaktní formulář</h3>
                    <form id="contact-form">
                        <div class="form-group">
                            <label for="c-name">Jméno a příjmení *</label>
                            <input type="text" id="c-name" name="name" required placeholder="Vaše jméno a příjmení">
                        </div>
                        <div class="form-group">
                            <label for="c-email">E-mail *</label>
                            <input type="email" id="c-email" name="email" required placeholder="vas@email.cz">
                        </div>
                        <div class="form-group">
                            <label for="c-message">Zpráva *</label>
                            <textarea id="c-message" name="message" required placeholder="Jak vám mohu pomoci?"></textarea>
                        </div>
                        <div id="contact-message" style="display:none; padding: 12px 16px; border-radius: 12px; font-size: 0.875rem; margin-bottom: 16px;"></div>
                        <button type="submit" class="btn-primary" style="width: 100%;">
                            ODESLAT ZPRÁVU
                        </button>
                    </form>
                </div>

                <div class="contact-info fade-in">
                    <div>
                        <h3>Rezervace konzultace zdarma</h3>
                        <p>Vyberte si volný termín v kalendáři. Rezervace je napojena na Google Calendar.</p>
                    </div>
                    <button type="button" class="contact-booking-btn" onclick="openBookingModal()">
                        <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        OTEVŘÍT KALENDÁŘ
                    </button>
                    <div>
                        <p><strong>Jana Štěpaníková</strong></p>
                        <a href="mailto:jana@walance.cz" class="contact-email">jana@walance.cz</a>
                        <p style="margin-top: 4px;"><a href="tel:+420601584901" style="color: var(--accent); text-decoration: none;">+420 601 584 901</a></p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ FAQ ============ -->
    <section class="section faq-section">
        <div class="container">
            <div class="section-header fade-in">
                <p class="section-label">FAQ</p>
                <h2 class="section-title">Časté dotazy</h2>
            </div>

            <div class="faq-list">
                <details class="faq-item fade-in">
                    <summary>
                        Jak dlouho trvá spolupráce?
                        <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </summary>
                    <div class="faq-answer">Zaměřuji se na skutečnou implementaci, ne na jednorázové teorie. Respektuji čas potřebný pro změnu návyků.<br><br><strong>OFFICE RESET&trade; (firemní týmy):</strong> 4týdenní cyklus. První týden nastavím systém a provedu audit (LIVE), další tři týdny slouží k fixaci nových návyků do praxe (hybridní formou).<br><br><strong>CRISIS MENTORING (lídři 1:1):</strong> Přizpůsobuji se stavu vašeho systému. Nabízím buď jednorázovou strategickou intervenci (90 min) pro okamžitou úlevu, nebo 3měsíční mentoring pro kompletní přestavbu pracovních návyků.</div>
                </details>
                <details class="faq-item fade-in">
                    <summary>
                        Kde probíhá spolupráce?
                        <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </summary>
                    <div class="faq-answer">Působím po celé ČR. Za klienty jezdím tam, kde je jejich byznys.<br><br><strong>Pro firmy (OFFICE RESET&trade;):</strong> Úvodní audit a nastavení pracoviště probíhá vždy fyzicky u vás ve firmě. Následný mentoring pro fixaci návyků řídím hybridně/online.<br><br><strong>Pro lídry (CRISIS MENTORING):</strong> Tělo nelze plně nastavit přes webkameru. Proto kombinuji osobní setkání (pro hloubkovou diagnostiku a manuální techniky) s efektivními online konzultacemi (pro řízení kalendáře a strategie).</div>
                </details>
                <details class="faq-item fade-in">
                    <summary>
                        Jak se liší WALANCE od klasického koučingu?
                        <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </summary>
                    <div class="faq-answer">Většina koučů pracuje „shora dolů" (od hlavy k tělu). Já jdu opačně. Klasický koučink ladí váš software (mindset). Já ale začínám opravou hardwaru (těla).<br><br>WALANCE respektuje biologii výkonu:<br><strong>Hardware (Tělo):</strong> Nejdřív odstraním fyzické bloky, bolest a chronickou únavu.<br><strong>Software (Hlava):</strong> Teprve když systém nebolí, nastavuji strategie a myšlení.<br><strong>Operační systém (Návyky):</strong> Vše ukotvím do kalendáře, aby změna vydržela.</div>
                </details>
                <details class="faq-item fade-in">
                    <summary>
                        Kolik to stojí?
                        <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </summary>
                    <div class="faq-answer">
                        <strong>CRISIS MENTORING (1:1):</strong> Od 3 500 Kč za 90minutovou session. Balíčky (3 session) od 9 000 Kč. Tříměsíční mentoring na míru — cena dle rozsahu.<br><br>
                        <strong>OFFICE RESET&trade; (firmy):</strong> Od 45 000 Kč za tým. Závisí na velikosti týmu a rozsahu auditu. Pošlu vám detailní nabídku do 48 hodin od prvního hovoru.<br><br>
                        <strong>BYZNYS Z POSTELE:</strong> Zcela zdarma — masterclass, e-book i komunita.<br><br>
                        Úvodní konzultace (30 min) je vždy zdarma a nezávazná.
                    </div>
                </details>
                <details class="faq-item fade-in">
                    <summary>
                        Jak začít?
                        <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </summary>
                    <div class="faq-answer">
                        <strong>Krok 1:</strong> <a href="javascript:void(0)" onclick="openBookingModal()" style="color: var(--accent); text-decoration: underline;">Rezervujte si bezplatnou konzultaci</a> (30 min) v kalendáři — nebo <a href="#contact" style="color: var(--accent); text-decoration: underline;">vyplňte formulář</a>.<br><br>
                        <strong>Krok 2:</strong> Společně zhodnotíme stav vašeho systému (tělo, hlava, práce) a určíme, zda je pro vás vhodnější osobní mentoring nebo firemní Office Reset.<br><br>
                        <strong>Krok 3:</strong> Dostanete konkrétní nabídku s cenou, rozsahem a časovým plánem. Žádné překvapení.<br><br>
                        Chcete začít hned? Stáhněte si zdarma <strong>Byznys z postele</strong> — e-book + masterclass bez závazků.
                    </div>
                </details>
            </div>
        </div>
    </section>

    <!-- ============ BOOKING MODAL ============ -->
    <div id="booking-modal" style="display:none; position:fixed; inset:0; z-index:200;">
        <div onclick="closeBookingModal()" style="position:absolute; inset:0; background:rgba(30,41,59,0.6); backdrop-filter:blur(4px); -webkit-backdrop-filter:blur(4px);"></div>
        <div style="position:absolute; top:50%; left:50%; transform:translate(-50%,-50%); width:calc(100% - 32px); max-width:640px; max-height:90vh; overflow-y:auto; background:var(--cream); border-radius:24px; box-shadow:0 24px 64px rgba(0,0,0,0.2);">
            <div style="position:sticky; top:0; background:var(--cream); z-index:10; display:flex; justify-content:flex-end; padding:16px 20px 0;">
                <button onclick="closeBookingModal()" style="background:none; border:none; cursor:pointer; padding:8px; color:var(--ink);" aria-label="Zavřít">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
                </button>
            </div>
            <div style="padding:0 32px 32px;">
                <h2 class="font-display" style="font-size:1.75rem; font-weight:700; margin-bottom:8px;">Rezervace konzultace zdarma</h2>
                <p style="color:var(--ink-light); font-size:0.875rem; margin-bottom:24px;">Zelená = volné termíny. Vyberte den, pak čas.</p>

                <div style="display:flex; justify-content:space-between; align-items:center; margin-bottom:16px;">
                    <button id="cal-prev" style="background:none; border:1px solid var(--mist); border-radius:12px; padding:8px 12px; cursor:pointer; color:var(--ink);">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="15 18 9 12 15 6"/></svg>
                    </button>
                    <span id="cal-month-title" class="font-display" style="font-weight:700; font-size:1.125rem;"></span>
                    <button id="cal-next" style="background:none; border:1px solid var(--mist); border-radius:12px; padding:8px 12px; cursor:pointer; color:var(--ink);">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><polyline points="9 18 15 12 9 6"/></svg>
                    </button>
                </div>

                <div style="display:grid; grid-template-columns:repeat(7,1fr); gap:4px; text-align:center; margin-bottom:4px;">
                    <span style="font-size:0.6875rem; font-weight:700; color:var(--ink-muted); text-transform:uppercase; padding:4px 0;">Po</span>
                    <span style="font-size:0.6875rem; font-weight:700; color:var(--ink-muted); text-transform:uppercase; padding:4px 0;">Út</span>
                    <span style="font-size:0.6875rem; font-weight:700; color:var(--ink-muted); text-transform:uppercase; padding:4px 0;">St</span>
                    <span style="font-size:0.6875rem; font-weight:700; color:var(--ink-muted); text-transform:uppercase; padding:4px 0;">Čt</span>
                    <span style="font-size:0.6875rem; font-weight:700; color:var(--ink-muted); text-transform:uppercase; padding:4px 0;">Pá</span>
                    <span style="font-size:0.6875rem; font-weight:700; color:var(--ink-muted); text-transform:uppercase; padding:4px 0;">So</span>
                    <span style="font-size:0.6875rem; font-weight:700; color:var(--ink-muted); text-transform:uppercase; padding:4px 0;">Ne</span>
                </div>

                <div id="cal-grid" style="display:grid; grid-template-columns:repeat(7,1fr); gap:4px; margin-bottom:24px;"></div>

                <div id="cal-time-panel" style="display:none; margin-bottom:24px;">
                    <p id="cal-time-label" style="font-weight:600; margin-bottom:12px;"></p>
                    <div id="cal-time-slots" style="display:flex; flex-wrap:wrap; gap:8px;"></div>
                </div>

                <form id="booking-form">
                    <input type="hidden" id="booking-date" name="date">
                    <input type="hidden" id="booking-time" name="time">

                    <div id="booking-fields" style="display:none;">
                        <div class="form-group">
                            <label for="b-name">Jméno a příjmení *</label>
                            <input type="text" id="b-name" name="name" required placeholder="Vaše jméno a příjmení">
                        </div>
                        <div class="form-group">
                            <label for="b-email">E-mail *</label>
                            <input type="email" id="b-email" name="email" required placeholder="vas@email.cz">
                        </div>
                        <div class="form-group">
                            <label for="b-phone">Telefon</label>
                            <input type="tel" id="b-phone" name="phone" placeholder="+420...">
                        </div>
                        <div class="form-group">
                            <label for="b-message">Poznámka</label>
                            <textarea id="b-message" name="message" rows="3" placeholder="Stručně popište vaši situaci..."></textarea>
                        </div>
                    </div>

                    <div id="booking-message" style="display:none; padding:12px 16px; border-radius:12px; font-size:0.875rem; margin-bottom:16px;"></div>

                    <button type="submit" id="booking-submit" disabled class="btn-primary" style="width:100%; display:none;">
                        REZERVOVAT
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- ============ FOOTER ============ -->
    <footer class="footer">
        <div class="container footer-inner">
            <div>
                <div class="footer-brand">WALANCE.</div>
                <p class="footer-tagline" style="margin-bottom: 12px; font-style: italic; max-width: 320px;">„Byznys je management energie. Každý udržitelný výkon stojí na stabilitě svého nositele."</p>
                <p style="font-size: 0.75rem; font-weight: 700; opacity: 0.6; margin-bottom: 6px;">NetWalking Pro s.r.o.</p>
                <p style="font-size: 0.8125rem; line-height: 1.6;">
                    Jana Štěpaníková<br>
                    <a href="mailto:jana@walance.cz" style="color: rgba(250,249,247,0.6); text-decoration: none;">jana@walance.cz</a><br>
                    <a href="tel:+420601584901" style="color: rgba(250,249,247,0.6); text-decoration: none;">+420 601 584 901</a>
                </p>
                <p style="font-size: 0.75rem; margin-top: 8px; opacity: 0.5; line-height: 1.5;">
                    IČ: 22107321 · DIČ: CZ22107321<br>
                    Na Hrázi 1139/13, 750 02 Přerov<br>
                    DS: 9khhxiv · Zapsáno u KOS, oddíl C, vložka 97337
                </p>
            </div>
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

    <!-- ============ JAVASCRIPT ============ -->
    <script>
        // Mobile menu toggle
        const mobileToggle = document.getElementById('mobile-toggle');
        const mobileMenu = document.getElementById('mobile-menu');
        const hamburgerIcon = document.getElementById('hamburger-icon');
        const closeIcon = document.getElementById('close-icon');

        mobileToggle.addEventListener('click', () => {
            const isOpen = mobileMenu.classList.toggle('open');
            hamburgerIcon.style.display = isOpen ? 'none' : 'block';
            closeIcon.style.display = isOpen ? 'block' : 'none';
            document.body.style.overflow = isOpen ? 'hidden' : '';
        });

        document.querySelectorAll('.mobile-link').forEach(link => {
            link.addEventListener('click', () => {
                mobileMenu.classList.remove('open');
                hamburgerIcon.style.display = 'block';
                closeIcon.style.display = 'none';
                document.body.style.overflow = '';
            });
        });

        // Pillars interactive (WALANCE acronym)
        const pillarsData = [
            { letter: 'W', title: 'Walk & Work', subtitle: 'Pohyb jako nástroj myšlení', desc: 'Nejtěžší rozhodnutí se nedělají na židli. Chůze zvyšuje prokrvení mozku a kreativitu o 60 %. Aktivujte svůj procesor v pohybu a nechte nápady volně proudit.' },
            { letter: 'A', title: 'Awareness', subtitle: 'Senzory pro včasné varování', desc: 'Nemůžete řídit to, co necítíte. Učím vás vnímat signály těla — mělké dýchání nebo tuhnoucí šíji — dříve, než se z drobného napětí stane problém, který vás zastaví.' },
            { letter: 'L', title: 'Longevity', subtitle: 'Maximální životnost systému', desc: 'Nejde o to dožít se stovky, ale nebýt v padesáti „na odpis". Budujeme tělo, které udrží plnou kapacitu pro vysoký výkon tak dlouho, dokud se sami rozhodnete zůstat ve hře.' },
            { letter: 'A', title: 'Alignment', subtitle: 'Geometrie těla i role', desc: 'Odstraňujeme tření v systému. Srovnáme vaši páteř (fyzický postoj) a pomocí Job Craftingu zarovnáme pracovní náplň s vašimi silnými stránkami. Když vše lícuje, výkon roste bez odporu.' },
            { letter: 'N', title: 'New Habits', subtitle: 'Systémový upgrade návyků', desc: 'Velké revoluce nefungují. Fungují mikrozměny, které využívají neuroplasticitu mozku. Nastavíme nové rutiny tak, aby se staly přirozenou součástí vašeho dne — bez stresu a bez nutnosti železné vůle.' },
            { letter: 'C', title: 'Clarity', subtitle: 'Ochrana procesoru před šumem', desc: 'Nevyhoříte z práce, ale z chaosu. Nastavíme principy informační hygieny a hluboké práce (Deep Work). Odstraníme digitální smog, aby vaše mysl zůstala ostrá a schopná soustředění i pod tlakem.' },
            { letter: 'E', title: 'Energy', subtitle: 'Optimalizace biologického paliva', desc: 'Time management sám o sobě nestačí, pokud nemáte energii. Zaměříme se na její řízení. Naučíte se pracovat se svými biorytmy a nejtěžší úkoly plánovat do výkonnostních špiček. Výsledkem je stabilní přísun sil po celý den, ne jen do oběda.' },
        ];

        document.querySelectorAll('#pillar-buttons .pillar-btn').forEach(btn => {
            btn.addEventListener('click', () => {
                const idx = parseInt(btn.dataset.index);
                const data = pillarsData[idx];
                document.querySelectorAll('#pillar-buttons .pillar-btn').forEach(b => b.classList.remove('active'));
                btn.classList.add('active');
                document.getElementById('p-bg-letter').textContent = data.letter;
                document.getElementById('p-title').textContent = data.title;
                document.getElementById('p-subtitle').textContent = data.subtitle;
                document.getElementById('p-desc').textContent = data.desc;
            });
        });

        // Scroll reveal (Intersection Observer)
        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('visible');
                }
            });
        }, { threshold: 0.05, rootMargin: '0px 0px -40px 0px' });

        document.querySelectorAll('.fade-in').forEach(el => observer.observe(el));

        // Smooth nav shadow on scroll
        const nav = document.querySelector('.nav');
        window.addEventListener('scroll', () => {
            if (window.scrollY > 10) {
                nav.style.boxShadow = '0 1px 8px rgba(0,0,0,0.06)';
            } else {
                nav.style.boxShadow = 'none';
            }
        }, { passive: true });

        // Contact form (AJAX)
        const contactForm = document.getElementById('contact-form');
        const contactMsg = document.getElementById('contact-message');

        contactForm.addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = contactForm.querySelector('button[type="submit"]');
            btn.disabled = true;
            contactMsg.style.display = 'none';

            const fd = new FormData(contactForm);
            const data = Object.fromEntries(fd);

            try {
                const r = await fetch('api/contact.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const res = await r.json();
                contactMsg.style.display = 'block';
                if (res.success) {
                    contactMsg.style.background = '#dcfce7';
                    contactMsg.style.color = '#166534';
                    contactMsg.textContent = res.message;
                    contactForm.reset();
                } else {
                    contactMsg.style.background = '#fef2f2';
                    contactMsg.style.color = '#991b1b';
                    contactMsg.textContent = res.error || 'Chyba při odesílání.';
                }
            } catch (err) {
                contactMsg.style.display = 'block';
                contactMsg.style.background = '#fef2f2';
                contactMsg.style.color = '#991b1b';
                contactMsg.textContent = 'Chyba připojení. Zkuste to později nebo napište na jana@walance.cz';
            }
            btn.disabled = false;
        });

        // ---- BOOKING CALENDAR ----
        const monthNames = ['Leden','Únor','Březen','Duben','Květen','Červen','Červenec','Srpen','Září','Říjen','Listopad','Prosinec'];
        const dayNames = ['neděle','pondělí','úterý','středa','čtvrtek','pátek','sobota'];
        const dayNamesGen = ['ledna','února','března','dubna','května','června','července','srpna','září','října','listopadu','prosince'];

        let calCurrentMonth = '';
        let calData = { slots: {}, availability: {} };
        let calSelectedDate = null;

        function openBookingModal() {
            const modal = document.getElementById('booking-modal');
            modal.style.display = 'block';
            document.body.style.overflow = 'hidden';
            calSelectedDate = null;
            document.getElementById('cal-time-panel').style.display = 'none';
            document.getElementById('booking-fields').style.display = 'none';
            document.getElementById('booking-submit').style.display = 'none';
            document.getElementById('booking-message').style.display = 'none';
            document.getElementById('booking-date').value = '';
            document.getElementById('booking-time').value = '';
            document.getElementById('booking-form').reset();
            const now = new Date();
            calCurrentMonth = now.getFullYear() + '-' + String(now.getMonth() + 1).padStart(2, '0');
            loadCalendarMonth();
        }

        function closeBookingModal() {
            document.getElementById('booking-modal').style.display = 'none';
            document.body.style.overflow = '';
        }

        document.addEventListener('keydown', (e) => {
            if (e.key === 'Escape') closeBookingModal();
        });

        async function loadCalendarMonth() {
            const grid = document.getElementById('cal-grid');
            grid.innerHTML = '<div style="grid-column:1/-1; text-align:center; padding:24px; color:var(--ink-muted);">Načítám…</div>';
            try {
                const r = await fetch('api/slots.php?month=' + calCurrentMonth, { cache: 'no-store' });
                calData = await r.json();
                renderCalendar();
            } catch (err) {
                grid.innerHTML = '<div style="grid-column:1/-1; text-align:center; padding:24px; color:#991b1b;">Chyba načtení kalendáře.</div>';
            }
        }

        function renderCalendar() {
            const [year, month] = calCurrentMonth.split('-').map(Number);
            document.getElementById('cal-month-title').textContent = monthNames[month - 1] + ' ' + year;
            const grid = document.getElementById('cal-grid');
            grid.innerHTML = '';

            const firstDay = new Date(year, month - 1, 1);
            const offset = (firstDay.getDay() + 6) % 7;
            const daysInMonth = new Date(year, month, 0).getDate();
            const today = new Date();
            today.setHours(0, 0, 0, 0);

            for (let i = 0; i < offset; i++) {
                const empty = document.createElement('div');
                grid.appendChild(empty);
            }

            for (let d = 1; d <= daysInMonth; d++) {
                const dateStr = year + '-' + String(month).padStart(2, '0') + '-' + String(d).padStart(2, '0');
                const dateObj = new Date(year, month - 1, d);
                const dayOfWeek = dateObj.getDay();
                const isPast = dateObj < today;
                const isWeekend = dayOfWeek === 0 || dayOfWeek === 6;

                const cell = document.createElement('div');
                cell.textContent = d;

                const slots = calData.slots?.[dateStr];
                const avail = calData.availability?.[dateStr];

                if (isPast || isWeekend || !slots || slots.length === 0) {
                    cell.style.background = 'rgba(226,232,240,0.2)';
                    cell.style.color = 'var(--ink-muted)';
                    if (isPast) cell.style.opacity = '0.5';
                } else {
                    const pct = avail ? avail.percent : 100;
                    const hasReservations = avail && (avail.pending > 0 || avail.confirmed > 0);

                    if (!hasReservations) {
                        if (pct >= 75) { cell.style.background = '#059669'; cell.style.color = '#fff'; }
                        else if (pct >= 50) { cell.style.background = '#10b981'; cell.style.color = '#fff'; }
                        else if (pct >= 25) { cell.style.background = '#6ee7b7'; cell.style.color = 'var(--ink)'; }
                        else { cell.style.background = '#d1fae5'; cell.style.color = 'var(--ink)'; }
                    } else {
                        if (pct >= 75) { cell.style.background = '#34d399'; cell.style.color = '#fff'; }
                        else if (pct >= 50) { cell.style.background = '#6ee7b7'; cell.style.color = 'var(--ink)'; }
                        else if (pct >= 25) { cell.style.background = '#a7f3d0'; cell.style.color = 'var(--ink)'; }
                        else { cell.style.background = '#d1fae5'; cell.style.color = 'var(--ink)'; }
                    }

                    cell.classList.add('cal-day-clickable');
                    cell.addEventListener('click', () => selectDate(dateStr));
                }

                if (calSelectedDate === dateStr) cell.classList.add('cal-day-selected');
                grid.appendChild(cell);
            }
        }

        function selectDate(dateStr) {
            calSelectedDate = dateStr;
            renderCalendar();

            const slots = calData.slots[dateStr] || [];
            const timeSlotsEl = document.getElementById('cal-time-slots');
            timeSlotsEl.innerHTML = '';

            const dateObj = new Date(dateStr + 'T00:00:00');
            const dayName = dayNames[dateObj.getDay()];
            const day = dateObj.getDate();
            const monthGen = dayNamesGen[dateObj.getMonth()];
            document.getElementById('cal-time-label').textContent = 'Vyberte čas — ' + dayName + ' ' + day + '. ' + monthGen;

            slots.forEach(time => {
                const btn = document.createElement('button');
                btn.type = 'button';
                btn.className = 'time-slot-btn';
                btn.textContent = time;
                btn.addEventListener('click', () => {
                    document.querySelectorAll('.time-slot-btn').forEach(b => b.classList.remove('time-slot-selected'));
                    btn.classList.add('time-slot-selected');
                    document.getElementById('booking-date').value = dateStr;
                    document.getElementById('booking-time').value = time;
                    document.getElementById('booking-fields').style.display = 'block';
                    document.getElementById('booking-submit').style.display = 'block';
                    document.getElementById('booking-submit').disabled = false;
                    document.getElementById('booking-submit').scrollIntoView({ behavior: 'smooth', block: 'nearest' });
                    setTimeout(() => document.getElementById('b-name').focus(), 400);
                });
                timeSlotsEl.appendChild(btn);
            });

            document.getElementById('cal-time-panel').style.display = 'block';
            document.getElementById('booking-fields').style.display = 'none';
            document.getElementById('booking-submit').style.display = 'none';
        }

        // Calendar navigation
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

        // Booking form submit
        document.getElementById('booking-form').addEventListener('submit', async (e) => {
            e.preventDefault();
            const btn = document.getElementById('booking-submit');
            const msg = document.getElementById('booking-message');
            btn.disabled = true;
            msg.style.display = 'none';

            const fd = new FormData(e.target);
            const data = Object.fromEntries(fd);

            try {
                const r = await fetch('api/booking.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify(data)
                });
                const res = await r.json();
                msg.style.display = 'block';
                if (res.success) {
                    msg.style.background = '#dcfce7';
                    msg.style.color = '#166534';
                    msg.textContent = res.message || 'Rezervace odeslána!';
                    e.target.reset();
                    document.getElementById('booking-fields').style.display = 'none';
                    document.getElementById('booking-submit').style.display = 'none';
                    document.getElementById('cal-time-panel').style.display = 'none';
                    calSelectedDate = null;
                    loadCalendarMonth();
                    setTimeout(closeBookingModal, 2000);
                } else {
                    msg.style.background = '#fef2f2';
                    msg.style.color = '#991b1b';
                    msg.textContent = res.error || 'Chyba při odesílání.';
                    btn.disabled = false;
                }
            } catch (err) {
                msg.style.display = 'block';
                msg.style.background = '#fef2f2';
                msg.style.color = '#991b1b';
                msg.textContent = 'Chyba připojení. Zkuste to později nebo napište na jana@walance.cz';
                btn.disabled = false;
            }
        });
    </script>
</body>
</html>