<?php
/**
 * Admin – Editor blogového článku (nový / úprava)
 */
session_start();
if (!isset($_SESSION['walance_admin'])) {
    header('Location: index.php');
    exit;
}
require_once __DIR__ . '/../api/config.php';
require_once __DIR__ . '/../api/db.php';
require_once __DIR__ . '/../api/crud.php';

$db = getDb();
$v = defined('APP_VERSION') ? APP_VERSION : '1.0.0';

$id = (int)($_GET['id'] ?? 0);
$post = null;
if ($id) {
    $post = findActive('blog_posts', $id);
    if (!$post) {
        header('Location: blog.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <title>WALANCE CRM - <?= $post ? 'Upravit článek' : 'Nový článek' ?></title>
    <script src="https://cdn.tailwindcss.com?v=<?= htmlspecialchars($v) ?>"></script>
    <script src="https://unpkg.com/lucide@latest?v=<?= htmlspecialchars($v) ?>"></script>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;600;700&display=swap&v=<?= htmlspecialchars($v) ?>" rel="stylesheet">
    <script src="https://cdn.tiny.cloud/1/no-api-key/tinymce/6/tinymce.min.js" referrerpolicy="origin"></script>
    <style>
        body { font-family: 'DM Sans', sans-serif; }
        .tox-tinymce { border-radius: 0.5rem !important; border-color: #e2e8f0 !important; }
        .slug-preview { color: #64748b; font-size: 0.8125rem; }
        .autosave-indicator { transition: opacity 0.3s; }
    </style>
</head>
<body class="bg-slate-100 min-h-screen">
<?php $adminCurrentPage = 'blog'; include __DIR__ . '/includes/layout.php'; ?>
    <div class="p-6 max-w-5xl">
        <!-- Header -->
        <div class="flex flex-wrap items-center justify-between gap-4 mb-6">
            <div class="flex items-center gap-3">
                <a href="blog.php" class="text-slate-500 hover:text-slate-700">
                    <i data-lucide="arrow-left" class="w-5 h-5"></i>
                </a>
                <h2 class="text-lg font-bold text-slate-800"><?= $post ? 'Upravit článek' : 'Nový článek' ?></h2>
                <span id="autosave-msg" class="autosave-indicator text-xs text-green-600 opacity-0"></span>
            </div>
            <div class="flex gap-2">
                <button type="button" id="btn-save-draft" onclick="savePost('draft')" class="px-4 py-2 bg-slate-200 hover:bg-slate-300 text-slate-700 text-sm font-medium rounded-lg transition-colors">
                    Uložit jako koncept
                </button>
                <button type="button" id="btn-publish" onclick="savePost('published')" class="px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <?= ($post && $post['status'] === 'published') ? 'Aktualizovat' : 'Publikovat' ?>
                </button>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
            <!-- Main content area -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Title -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Nadpis článku</label>
                    <input type="text" id="post-title" value="<?= htmlspecialchars($post['title'] ?? '') ?>"
                        class="w-full px-4 py-3 text-lg border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                        placeholder="Zadejte nadpis..."
                        oninput="autoGenerateSlug()">
                </div>

                <!-- Body -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <label class="block text-sm font-medium text-slate-700 mb-2">Obsah článku</label>
                    <textarea id="post-body" class="w-full"><?= htmlspecialchars($post['body'] ?? '') ?></textarea>
                </div>

                <!-- Excerpt -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Krátký úvod (excerpt)</label>
                    <p class="text-xs text-slate-400 mb-2">Zobrazí se v seznamu článků a v Open Graph meta tagu.</p>
                    <textarea id="post-excerpt" rows="3"
                        class="w-full px-4 py-3 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500"
                        placeholder="Stručný popis článku..."><?= htmlspecialchars($post['excerpt'] ?? '') ?></textarea>
                </div>
            </div>

            <!-- Sidebar -->
            <div class="space-y-6">
                <!-- Status & Slug -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-sm font-bold text-slate-800 mb-4">Nastavení</h3>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Stav</label>
                        <div class="flex gap-2">
                            <span id="status-badge" class="px-2 py-1 rounded text-xs font-medium <?= ($post && $post['status'] === 'published') ? 'bg-green-100 text-green-800' : 'bg-amber-100 text-amber-800' ?>">
                                <?= ($post && $post['status'] === 'published') ? 'Publikováno' : 'Koncept' ?>
                            </span>
                            <?php if ($post && $post['published_at']): ?>
                            <span class="text-xs text-slate-400 self-center"><?= date('d.m.Y H:i', strtotime($post['published_at'])) ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">URL slug</label>
                        <div class="flex">
                            <span class="px-3 py-2 bg-slate-100 border border-r-0 border-slate-300 rounded-l-lg text-sm text-slate-500">/blog/</span>
                            <input type="text" id="post-slug" value="<?= htmlspecialchars($post['slug'] ?? '') ?>"
                                class="flex-1 px-3 py-2 border border-slate-300 rounded-r-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-sm"
                                placeholder="url-slug">
                        </div>
                    </div>

                    <div class="mb-4">
                        <label class="block text-sm font-medium text-slate-700 mb-1">Meta popis (SEO)</label>
                        <textarea id="post-meta-description" rows="2"
                            class="w-full px-3 py-2 border border-slate-300 rounded-lg focus:ring-2 focus:ring-teal-500 focus:border-teal-500 text-sm"
                            placeholder="Popis pro vyhledávače..."
                            maxlength="160"><?= htmlspecialchars($post['meta_description'] ?? '') ?></textarea>
                        <span class="text-xs text-slate-400" id="meta-chars">0/160</span>
                    </div>
                </div>

                <!-- Featured Image -->
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-sm font-bold text-slate-800 mb-4">Náhledový obrázek</h3>
                    <div id="image-preview" class="mb-4 <?= empty($post['featured_image']) ? 'hidden' : '' ?>">
                        <?php if (!empty($post['featured_image'])): ?>
                        <img src="../<?= htmlspecialchars($post['featured_image']) ?>" class="w-full rounded-lg object-cover max-h-48" id="preview-img">
                        <?php else: ?>
                        <img src="" class="w-full rounded-lg object-cover max-h-48 hidden" id="preview-img">
                        <?php endif; ?>
                    </div>
                    <input type="hidden" id="post-featured-image" value="<?= htmlspecialchars($post['featured_image'] ?? '') ?>">
                    <div class="flex gap-2">
                        <label class="flex-1 cursor-pointer">
                            <span class="block px-3 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium rounded-lg text-center transition-colors">
                                <i data-lucide="upload" class="w-4 h-4 inline mr-1"></i> Nahrát obrázek
                            </span>
                            <input type="file" accept="image/*" class="hidden" id="image-upload" onchange="uploadImage(this)">
                        </label>
                        <button type="button" onclick="removeImage()" class="px-3 py-2 bg-red-50 hover:bg-red-100 text-red-600 text-sm font-medium rounded-lg transition-colors <?= empty($post['featured_image']) ? 'hidden' : '' ?>" id="btn-remove-image">
                            <i data-lucide="trash-2" class="w-4 h-4"></i>
                        </button>
                    </div>
                    <div id="upload-progress" class="hidden mt-2">
                        <div class="w-full bg-slate-200 rounded-full h-1.5">
                            <div class="bg-teal-600 h-1.5 rounded-full transition-all" style="width: 0%" id="upload-bar"></div>
                        </div>
                    </div>
                </div>

                <!-- Quick info -->
                <?php if ($post): ?>
                <div class="bg-white rounded-xl shadow-sm p-6">
                    <h3 class="text-sm font-bold text-slate-800 mb-3">Informace</h3>
                    <dl class="text-sm space-y-2">
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Vytvořeno:</dt>
                            <dd class="text-slate-700"><?= date('d.m.Y H:i', strtotime($post['valid_from'])) ?></dd>
                        </div>
                        <?php if ($post['published_at']): ?>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">Publikováno:</dt>
                            <dd class="text-slate-700"><?= date('d.m.Y H:i', strtotime($post['published_at'])) ?></dd>
                        </div>
                        <?php endif; ?>
                        <div class="flex justify-between">
                            <dt class="text-slate-500">ID:</dt>
                            <dd class="text-slate-700">#<?= $post['id'] ?></dd>
                        </div>
                    </dl>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
<?php include __DIR__ . '/includes/layout-end.php'; ?>

    <script>
        lucide.createIcons();

        const postId = <?= $id ?: 'null' ?>;
        let slugManual = <?= ($post && $post['slug']) ? 'true' : 'false' ?>;
        let saving = false;

        // TinyMCE editor
        tinymce.init({
            selector: '#post-body',
            height: 500,
            menubar: true,
            language: 'cs',
            plugins: 'lists link image table code fullscreen media autolink',
            toolbar: 'undo redo | blocks | bold italic underline strikethrough | alignleft aligncenter alignright | bullist numlist | link image media | table | code fullscreen',
            content_style: "body { font-family: 'DM Sans', -apple-system, sans-serif; font-size: 16px; line-height: 1.7; max-width: 100%; padding: 16px; color: #1e293b; } img { max-width: 100%; height: auto; border-radius: 8px; } h2 { font-size: 1.5em; margin-top: 1.5em; } h3 { font-size: 1.25em; margin-top: 1.25em; } blockquote { border-left: 4px solid #0d9488; padding-left: 16px; margin: 1em 0; color: #475569; font-style: italic; }",
            branding: false,
            promotion: false,
            relative_urls: false,
            remove_script_host: false,
            convert_urls: true,
            images_upload_handler: function(blobInfo) {
                return new Promise(function(resolve, reject) {
                    var fd = new FormData();
                    fd.append('image', blobInfo.blob(), blobInfo.filename());
                    fd.append('action', 'upload_image');
                    fetch('../api/blog.php', {
                        method: 'POST',
                        body: fd,
                        credentials: 'same-origin'
                    })
                    .then(function(r) { return r.json(); })
                    .then(function(data) {
                        if (data.success) resolve('../' + data.url);
                        else reject('Upload selhal: ' + (data.error || 'Neznámá chyba'));
                    })
                    .catch(function() { reject('Chyba spojení.'); });
                });
            },
            setup: function(editor) {
                editor.on('init', function() {
                    updateMetaChars();
                });
            }
        });

        // Slug generation
        function autoGenerateSlug() {
            if (slugManual && postId) return;
            const title = document.getElementById('post-title').value;
            document.getElementById('post-slug').value = generateSlug(title);
        }

        document.getElementById('post-slug').addEventListener('input', function() {
            slugManual = true;
        });

        function generateSlug(str) {
            const map = {'á':'a','č':'c','ď':'d','é':'e','ě':'e','í':'i','ň':'n','ó':'o','ř':'r','š':'s','ť':'t','ú':'u','ů':'u','ý':'y','ž':'z'};
            return str.toLowerCase().replace(/[áčďéěíňóřšťúůýž]/g, function(c) { return map[c] || c; })
                .replace(/[^a-z0-9]+/g, '-').replace(/^-|-$/g, '');
        }

        // Meta description chars counter
        document.getElementById('post-meta-description').addEventListener('input', updateMetaChars);
        function updateMetaChars() {
            var el = document.getElementById('post-meta-description');
            if (el) document.getElementById('meta-chars').textContent = el.value.length + '/160';
        }
        updateMetaChars();

        // Image upload
        function uploadImage(input) {
            if (!input.files || !input.files[0]) return;
            var file = input.files[0];
            if (file.size > 5 * 1024 * 1024) {
                alert('Maximální velikost obrázku je 5 MB.');
                return;
            }

            var fd = new FormData();
            fd.append('image', file);
            fd.append('action', 'upload_image');

            document.getElementById('upload-progress').classList.remove('hidden');
            document.getElementById('upload-bar').style.width = '30%';

            fetch('../api/blog.php', {
                method: 'POST',
                body: fd,
                credentials: 'same-origin'
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                document.getElementById('upload-bar').style.width = '100%';
                setTimeout(function() {
                    document.getElementById('upload-progress').classList.add('hidden');
                    document.getElementById('upload-bar').style.width = '0%';
                }, 500);

                if (data.success) {
                    document.getElementById('post-featured-image').value = data.url;
                    var preview = document.getElementById('image-preview');
                    var img = document.getElementById('preview-img');
                    if (!img) {
                        img = document.createElement('img');
                        img.id = 'preview-img';
                        img.className = 'w-full rounded-lg object-cover max-h-48';
                        preview.appendChild(img);
                    }
                    img.src = '../' + data.url;
                    img.classList.remove('hidden');
                    preview.classList.remove('hidden');
                    document.getElementById('btn-remove-image').classList.remove('hidden');
                } else {
                    alert(data.error || 'Chyba při uploadu.');
                }
            })
            .catch(function() {
                document.getElementById('upload-progress').classList.add('hidden');
                alert('Chyba spojení.');
            });
        }

        function removeImage() {
            document.getElementById('post-featured-image').value = '';
            document.getElementById('image-preview').classList.add('hidden');
            document.getElementById('btn-remove-image').classList.add('hidden');
            var img = document.getElementById('preview-img');
            if (img) img.src = '';
        }

        // Save post
        function savePost(status) {
            if (saving) return;
            saving = true;

            var title = document.getElementById('post-title').value.trim();
            if (!title) {
                alert('Nadpis je povinný.');
                saving = false;
                return;
            }

            // Synchronizovat TinyMCE
            if (typeof tinymce !== 'undefined' && tinymce.get('post-body')) {
                tinymce.get('post-body').save();
            }

            var fd = new FormData();
            fd.append('action', 'save');
            if (postId) fd.append('id', postId);
            fd.append('title', title);
            fd.append('slug', document.getElementById('post-slug').value.trim());
            fd.append('excerpt', document.getElementById('post-excerpt').value.trim());
            fd.append('body', document.getElementById('post-body').value);
            fd.append('status', status);
            fd.append('meta_description', document.getElementById('post-meta-description').value.trim());
            fd.append('featured_image', document.getElementById('post-featured-image').value.trim());

            var btnDraft = document.getElementById('btn-save-draft');
            var btnPublish = document.getElementById('btn-publish');
            btnDraft.disabled = true;
            btnPublish.disabled = true;

            fetch('../api/blog.php', {
                method: 'POST',
                body: fd,
                credentials: 'same-origin',
                cache: 'no-store'
            })
            .then(function(r) { return r.json(); })
            .then(function(data) {
                saving = false;
                btnDraft.disabled = false;
                btnPublish.disabled = false;

                if (data.success) {
                    var msg = document.getElementById('autosave-msg');
                    msg.textContent = 'Uloženo';
                    msg.style.opacity = '1';
                    setTimeout(function() { msg.style.opacity = '0'; }, 2000);

                    if (!postId && data.id) {
                        window.location.href = 'blog-edit.php?id=' + data.id;
                    }

                    // Update status badge
                    var badge = document.getElementById('status-badge');
                    if (status === 'published') {
                        badge.className = 'px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800';
                        badge.textContent = 'Publikováno';
                        btnPublish.textContent = 'Aktualizovat';
                    } else {
                        badge.className = 'px-2 py-1 rounded text-xs font-medium bg-amber-100 text-amber-800';
                        badge.textContent = 'Koncept';
                    }
                } else {
                    alert(data.error || 'Chyba při ukládání.');
                }
            })
            .catch(function() {
                saving = false;
                btnDraft.disabled = false;
                btnPublish.disabled = false;
                alert('Chyba spojení.');
            });
        }

        // Ctrl+S shortcut
        document.addEventListener('keydown', function(e) {
            if ((e.ctrlKey || e.metaKey) && e.key === 's') {
                e.preventDefault();
                savePost(<?= ($post && $post['status'] === 'published') ? "'published'" : "'draft'" ?>);
            }
        });
    </script>
</body>
</html>
