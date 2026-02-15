# Návrh blog modulu WALANCE

## 1. Přehled

Blog pro publikaci článků o metodě WALANCE, leadershipu, udržitelném výkonu. Napojení na hlavní web.

---

## 2. Datový model (soft-update)

### 2.1 Tabulka `blog_posts`

| Sloupec | Typ | Popis |
|---------|-----|-------|
| id | INT | PK |
| blog_posts_id | INT | entity_id |
| title | VARCHAR(255) | Nadpis |
| slug | VARCHAR(255) | URL slug (unique) |
| excerpt | TEXT | Krátký úvod |
| body | TEXT | Celý obsah (Markdown nebo HTML) |
| author_id | INT | FK admin_users |
| status | VARCHAR(20) | draft, published |
| published_at | DATETIME | Kdy zveřejněno |
| featured_image | VARCHAR(255) | Cesta k obrázku |
| valid_from, valid_to, valid_user_* | | soft-update |

### 2.2 Tabulka `blog_categories` (volitelně)
Pro kategorizaci článků (WALANCE pilíře, leadership, rekonvalescence…).

---

## 3. URL struktura

- **Seznam článků:** `/blog/` nebo `blog/index.php`
- **Detail článku:** `/blog/{slug}` např. `/blog/metoda-walance-v-praxi`
- **RSS:** `/blog/feed.xml`

---

## 4. Napojení na web

### 4.1 Hlavní stránka (index.html)
- Sekce „Nejnovější z blogu“ – 3 poslední články s odkazem na blog
- Odkaz v navigaci: „Blog“

### 4.2 Design
- Stejná paleta (ink, accent, sage, cream)
- Stejné fonty (Fraunces, DM Sans)
- Responzivní, čitelné články

---

## 5. Admin rozhraní

- **blog/posts.php** – seznam článků (draft/published), přidat, upravit, smazat
- **blog/edit.php** – editor článku (název, slug, excerpt, body, obrázek, status)
- Editor: textarea s Markdown nebo WYSIWYG (TinyMCE, Quill)

---

## 6. Implementační pořadí

1. **Fáze 1:** Migrace `blog_posts`, základní CRUD v admin
2. **Fáze 2:** Veřejná stránka `/blog/` – seznam + detail
3. **Fáze 3:** Sekce „Nejnovější z blogu“ na index.html
4. **Fáze 4:** Navigace, RSS feed
5. **Fáze 5:** Kategorie, fulltextové vyhledávání (volitelně)

---

## 7. Technické poznámky

- **Slug** – generovat z title (transliterace češtiny)
- **Markdown** – Parsedown nebo podobná knihovna
- **Obrázky** – `assets/blog/` nebo upload do složky
- **SEO** – meta description z excerpt, Open Graph
