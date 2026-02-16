<?php require_once __DIR__ . '/api/config.php'; $v = defined('APP_VERSION') ? APP_VERSION : '1.0.0'; ?>
<!DOCTYPE html>
<html lang="cs">
<!-- VERSION: <?= htmlspecialchars($v) ?> -->
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WALANCE — Anatomie udržitelného výkonu</title>
    <meta name="description" content="Zvyšte výkon týmu i sebe. Ne tím, že budete pracovat víc, ale tím, že přestanete bojovat s vlastní biologií. Metoda WALANCE: 7 pilířů pro udržitelný výkon lídrů.">

    <!-- Open Graph -->
    <meta property="og:title" content="WALANCE — Anatomie udržitelného výkonu">
    <meta property="og:description" content="Váš byznys software je geniální. Ale váš lidský hardware hoří. Metoda WALANCE pro udržitelný výkon lídrů a týmů.">
    <meta property="og:type" content="website">
    <meta property="og:url" content="https://walance.cz">
    <meta property="og:locale" content="cs_CZ">

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
            <a href="#" class="nav-logo">WALANCE<span>.</span></a>
            <ul class="nav-links">
                <li><a href="#problem">Problém</a></li>
                <li><a href="#method">Metoda</a></li>
                <li><a href="#story">Příběh</a></li>
                <li><a href="#products">Nabídka</a></li>
                <li><a href="#contact">Kontakt</a></li>
                <li><a href="#contact" class="nav-cta">Audit zdarma</a></li>
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
            <a href="#contact" class="mobile-link" style="color: var(--accent);">Audit zdarma</a>
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
                <a href="#contact" class="btn-primary">
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
                    <span class="trust-number">3</span>
                    <span class="trust-label">Vrstvy přístupu</span>
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
                <h2 class="section-title">Váš byznys software je geniální.<br>Ale co hardware?</h2>
                <p class="section-subtitle">
                    Snažíte se řídit formuli 1 se zataženou ruční brzdou. To není stáří. To je systémová chyba.
                </p>
            </div>

            <div class="cards-grid">
                <div class="card fade-in">
                    <div class="card-icon">
                        <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M12 2a9 9 0 0 0-9 9c0 3.9 2.5 7.1 6 8.4V21a1 1 0 0 0 1 1h4a1 1 0 0 0 1-1v-1.6c3.5-1.3 6-4.5 6-8.4a9 9 0 0 0-9-9z"/><path d="M10 17h4"/></svg>
                    </div>
                    <h3>Hlava (Software)</h3>
                    <p>Mozková mlha po obědě. Rozhodovací paralýza. Neschopnost „vypnout" práci doma. Jedete na autopilot.</p>
                </div>
                <div class="card fade-in">
                    <div class="card-icon" style="background: rgba(90, 125, 90, 0.1);">
                        <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round" style="stroke: var(--sage);"><polyline points="22 12 18 12 15 21 9 3 6 12 2 12"/></svg>
                    </div>
                    <h3>Tělo (Hardware)</h3>
                    <p>Bolest krční páteře vystřelující do rukou. Mělké dýchání. Chronická únava, kterou spánek neřeší.</p>
                </div>
                <div class="card fade-in">
                    <div class="card-icon">
                        <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg>
                    </div>
                    <h3>Tým (Výkon)</h3>
                    <p>Prezentismus: lidé sedí u počítačů, ale jejich kognitivní výkon je na 40 %. Vyhořelí lidé netvoří hodnoty.</p>
                </div>
            </div>

            <div class="verdict-box fade-in">
                <h3>To není stáří. To je systémová chyba.</h3>
                <p>Metoda WALANCE opravuje všechny tři vrstvy najednou — tělo, mysl i návyky.</p>
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
                    Metoda WALANCE není wellness benefit. Je to <strong>provozní manuál k vašemu tělu</strong>. Propojuje fyzioterapii, neurovědu a leadership.
                </p>
            </div>

            <div class="solution-grid">
                <div class="solution-card fade-in">
                    <p class="solution-tag">Vrstva 1</p>
                    <h3>Hardware</h3>
                    <p>Tělo &amp; Fyzio. Odstraníme fyzické tření. Konec bolestí zad znamená lepší prokrvení mozku. Opravíme šasi, aby motor mohl běžet naplno.</p>
                </div>
                <div class="solution-card fade-in">
                    <p class="solution-tag">Vrstva 2</p>
                    <h3>Software</h3>
                    <p>Job Crafting. Designujeme práci tak, aby vás nabíjela, ne vysávala. Sladíme vaše silné stránky s náplní práce.</p>
                </div>
                <div class="solution-card fade-in">
                    <p class="solution-tag">Vrstva 3</p>
                    <h3>Operační systém</h3>
                    <p>Návyky &amp; Rituály. Mikrozměny, které běží na pozadí a šetří vaši energii. Automatizace zdraví, abyste na něj nemuseli myslet.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ============ STORY ============ -->
    <section id="story" class="section story-section">
        <div class="container">
            <div class="story-grid">
                <div class="fade-in">
                    <span class="story-label">Případová studie</span>
                    <h2>Teorie končí, když si zraníte koleno.<br><em>Já to teď žiju.</em></h2>
                    <div class="story-text">
                        <p>Jmenuji se <strong>Jana</strong>. Jsem fyzioterapeutka a manažerka. Roky jsem učila firmy, jak nevyhořet. A pak přišla lekce pokory.</p>
                        <p>V lednu 2026 mě těžký úraz kolene upoutal na lůžko. Mohla jsem zavřít firmu. Místo toho jsem se stala <strong>Klientem 0</strong>.</p>
                        <p class="story-quote">„Aplikuji metodu WALANCE v extrémních podmínkách. Řídím byznys horizontálně. A funguje to. Mám čistší hlavu než vy v kanceláři."</p>
                        <p>Pokud dokážu udržet vysoký výkon já z postele, naučím to i vás.</p>
                    </div>
                </div>
                <div class="story-image-wrap fade-in">
                    <img src="assets/images/hero-story.jpg"
                         alt="Jana na lůžku s ortézou a laptopem — autentický záběr zakladatelky WALANCE"
                         class="story-image"
                         width="1800" height="1200"
                         loading="lazy">
                </div>
            </div>
        </div>
    </section>

    <!-- ============ ROI ============ -->
    <section class="section roi-section fade-in">
        <div class="container">
            <p class="section-label" style="color: rgba(250,249,247,0.5);">Matematika je neúprosná</p>
            <div class="roi-number">8</div>
            <p class="roi-unit">MINUT</p>
            <p class="roi-text">Tolik času denně stačí ušetřit (díky absenci bolestí zad), aby se investice vrátila.</p>
            <div class="roi-highlight">
                <p>My cílíme na <strong>60 MINUT</strong> denně navíc.</p>
                <small>Vynásobte si to hodinovou sazbou vašich klíčových lidí.</small>
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
                <!-- Product 1: Office Reset -->
                <div class="product-card product-card--default fade-in">
                    <div class="product-header">Pro firmy</div>
                    <div class="product-body">
                        <h3>OFFICE RESET&trade;</h3>
                        <p class="desc">Pro týmy, které jedou na dluh a potřebují zastavit úniky energie.</p>
                        <ul class="product-features">
                            <li>
                                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>Audit „energetických děr"</span>
                            </li>
                            <li>
                                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>Fyzio-ergonomie pracoviště</span>
                            </li>
                            <li>
                                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>4 týdny hybridního mentoringu</span>
                            </li>
                            <li>
                                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>Tvrdá data, žádné ezo</span>
                            </li>
                        </ul>
                        <a href="mailto:jana@walance.cz?subject=Popt%C3%A1vka%20Office%20Reset" class="product-cta product-cta--outline">
                            POPTAT AUDIT
                        </a>
                    </div>
                </div>

                <!-- Product 2: Crisis Mentoring (Featured) -->
                <div class="product-card product-card--featured fade-in">
                    <span class="product-badge">Bestseller</span>
                    <div class="product-header">Pro lídry (1-on-1)</div>
                    <div class="product-body">
                        <h3>CRISIS MENTORING</h3>
                        <p class="desc">Strategická konzultace s Janou (Klient 0). Krizové řízení vás samotných.</p>
                        <ul class="product-features">
                            <li>
                                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>Hloubková diagnostika</span>
                            </li>
                            <li>
                                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>Redesign pracovního kalendáře</span>
                            </li>
                            <li>
                                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>Okamžitá úleva od tlaku</span>
                            </li>
                            <li>
                                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>Systém, jak fungovat horizontálně</span>
                            </li>
                        </ul>
                        <button type="button" onclick="openBookingModal()" class="product-cta product-cta--primary">
                            REZERVOVAT TERMÍN
                        </button>
                        <p class="product-note">Kapacita omezena (Úraz)</p>
                    </div>
                </div>

                <!-- Product 3: Byznys z postele -->
                <div class="product-card product-card--default fade-in">
                    <div class="product-header" style="color: var(--sage);">Start (zdarma)</div>
                    <div class="product-body">
                        <h3>BYZNYS Z POSTELE</h3>
                        <p class="desc">Webinář: Jak řídit firmu a nezbláznit se, i když tělo stávkuje.</p>
                        <ul class="product-features">
                            <li>
                                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>3 okamžité techniky</span>
                            </li>
                            <li>
                                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>E-book s manuálem</span>
                            </li>
                            <li>
                                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>Přístup do komunity</span>
                            </li>
                            <li>
                                <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="20 6 9 17 4 12"/></svg>
                                <span>Záznam masterclass</span>
                            </li>
                        </ul>
                        <a href="#contact" class="product-cta product-cta--outline">
                            STÁHNOUT ZDARMA
                        </a>
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
                <h2 class="section-title">Napište nám</h2>
                <p class="section-subtitle">Máte dotaz nebo chcete rezervovat termín? Ozvěte se nám.</p>
            </div>

            <div class="contact-grid">
                <div class="contact-form-wrap fade-in">
                    <h3 style="font-size: 1.25rem; margin-bottom: 24px;">Kontaktní formulář</h3>
                    <form id="contact-form">
                        <div class="form-group">
                            <label for="c-name">Jméno *</label>
                            <input type="text" id="c-name" name="name" required placeholder="Vaše jméno">
                        </div>
                        <div class="form-group">
                            <label for="c-email">E-mail *</label>
                            <input type="email" id="c-email" name="email" required placeholder="vas@email.cz">
                        </div>
                        <div class="form-group">
                            <label for="c-message">Zpráva *</label>
                            <textarea id="c-message" name="message" required placeholder="Jak vám můžeme pomoci?"></textarea>
                        </div>
                        <div id="contact-message" style="display:none; padding: 12px 16px; border-radius: 12px; font-size: 0.875rem; margin-bottom: 16px;"></div>
                        <button type="submit" class="btn-primary" style="width: 100%;">
                            ODESLAT ZPRÁVU
                        </button>
                    </form>
                </div>

                <div class="contact-info fade-in">
                    <div>
                        <h3>Rezervace termínu</h3>
                        <p>Vyberte si volný termín v kalendáři. Rezervace je napojena na Google Calendar.</p>
                    </div>
                    <button type="button" class="contact-booking-btn" onclick="openBookingModal()">
                        <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"/><line x1="16" y1="2" x2="16" y2="6"/><line x1="8" y1="2" x2="8" y2="6"/><line x1="3" y1="10" x2="21" y2="10"/></svg>
                        OTEVŘÍT KALENDÁŘ
                    </button>
                    <div>
                        <p>Nebo napište přímo na</p>
                        <a href="mailto:info@walance.cz" class="contact-email">info@walance.cz</a>
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
                        Jak dlouho trvá typická spolupráce?
                        <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </summary>
                    <div class="faq-answer">Office Reset&trade; je 4týdenní program pro firmy. Crisis Mentoring je individuální a přizpůsobuje se vaší situaci — od jednorázové konzultace po dlouhodobý mentoring.</div>
                </details>
                <details class="faq-item fade-in">
                    <summary>
                        Potřebuji být fyzicky v Praze?
                        <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </summary>
                    <div class="faq-answer">Ne. Spolupráce probíhá hybridně — online konzultace, video hovory a fyzio cvičení na video. Osobní setkání je možné, ale není nutné.</div>
                </details>
                <details class="faq-item fade-in">
                    <summary>
                        Jak se liší WALANCE od klasického koučingu?
                        <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </summary>
                    <div class="faq-answer">WALANCE propojuje fyzioterapii s leadershipem. Nepracujeme jen s myšlením, ale i s tělem. Začínáte od hardware (tělo) a postupujete k software (mysl) a operačnímu systému (návyky). Žádný jiný kouč toto nepropojuje.</div>
                </details>
                <details class="faq-item fade-in">
                    <summary>
                        Jak začít?
                        <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 12 15 18 9"/></svg>
                    </summary>
                    <div class="faq-answer">Napište nám přes formulář nebo si rovnou rezervujte termín v kalendáři. Začneme krátkým auditem vaší situace — zdarma a nezávazně.</div>
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
                <h2 class="font-display" style="font-size:1.75rem; font-weight:700; margin-bottom:8px;">Rezervace termínu</h2>
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
                            <label for="b-name">Jméno *</label>
                            <input type="text" id="b-name" name="name" required placeholder="Vaše jméno">
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
                <p class="footer-tagline">Byznys není sprint. Je to maraton v pohybu.</p>
            </div>
            <div class="footer-links">
                <a href="#" aria-label="LinkedIn">
                    <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M16 8a6 6 0 0 1 6 6v7h-4v-7a2 2 0 0 0-2-2 2 2 0 0 0-2 2v7h-4v-7a6 6 0 0 1 6-6z"/><rect x="2" y="9" width="4" height="12"/><circle cx="4" cy="4" r="2"/></svg>
                </a>
                <a href="mailto:jana@walance.cz" aria-label="E-mail">
                    <svg viewBox="0 0 24 24" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"/><polyline points="22,6 12,13 2,6"/></svg>
                </a>
            </div>
            <div class="footer-copy">&copy; 2026 WALANCE. Všechna práva vyhrazena. <span style="margin-left: 8px; opacity: 0.5;">v<?= htmlspecialchars($v) ?></span></div>
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
                contactMsg.textContent = 'Chyba připojení. Zkuste to později nebo napište na info@walance.cz';
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
                msg.textContent = 'Chyba připojení. Zkuste to později nebo napište na info@walance.cz';
                btn.disabled = false;
            }
        });
    </script>
</body>
</html>
