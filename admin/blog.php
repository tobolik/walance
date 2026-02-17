<?php
/**
 * Admin – Seznam blogových článků
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

$statusFilter = $_GET['status'] ?? '';
$search = trim($_GET['search'] ?? '');

$sql = "SELECT bp.*, au.name as author_name FROM blog_posts bp 
        LEFT JOIN admin_users au ON bp.author_id = au.id 
        WHERE bp.valid_to IS NULL";
$params = [];

if ($statusFilter && in_array($statusFilter, ['draft', 'published'])) {
    $sql .= " AND bp.status = ?";
    $params[] = $statusFilter;
}
if ($search) {
    $sql .= " AND (bp.title LIKE ? OR bp.excerpt LIKE ?)";
    $p = "%$search%";
    $params = array_merge($params, [$p, $p]);
}
$sql .= " ORDER BY bp.valid_from DESC";

$stmt = $db->prepare($sql);
$stmt->execute($params);
$posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

// AJAX smazání
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    header('Cache-Control: no-store');
    if ($_POST['action'] === 'delete') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id) {
            try {
                softDelete('blog_posts', $id);
                echo json_encode(['success' => true]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'error' => 'Chybí ID.']);
        }
    }
    exit;
}
?>
<!DOCTYPE html>
<html lang="cs">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Cache-Control" content="no-cache, no-store, must-revalidate">
    <title>WALANCE CRM - Blog</title>
    <script src="https://cdn.tailwindcss.com?v=<?= htmlspecialchars($v) ?>"></script>
    <script src="https://unpkg.com/lucide@latest?v=<?= htmlspecialchars($v) ?>"></script>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;600;700&display=swap&v=<?= htmlspecialchars($v) ?>" rel="stylesheet">
    <style>body { font-family: 'DM Sans', sans-serif; }</style>
</head>
<body class="bg-slate-100 min-h-screen">
<?php $adminCurrentPage = 'blog'; include __DIR__ . '/includes/layout.php'; ?>
    <div class="p-6 max-w-6xl">
        <div class="bg-white rounded-xl shadow-sm p-6 mb-6">
            <div class="flex flex-wrap items-center justify-between gap-4 mb-4">
                <h2 class="text-lg font-bold text-slate-800">Blog – Články</h2>
                <a href="blog-edit.php" class="inline-flex items-center gap-2 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium rounded-lg transition-colors">
                    <i data-lucide="plus" class="w-4 h-4"></i> Nový článek
                </a>
            </div>

            <div class="flex flex-wrap gap-4 items-center">
                <form method="GET" class="flex flex-wrap gap-4 flex-1">
                    <input type="text" name="search" value="<?= htmlspecialchars($search) ?>" placeholder="Hledat v článcích..."
                        class="px-4 py-2 border border-slate-300 rounded-lg flex-1 min-w-[200px]">
                    <button type="submit" class="px-4 py-2 bg-teal-600 text-white rounded-lg hover:bg-teal-700 font-medium text-sm">
                        <i data-lucide="search" class="w-4 h-4 inline mr-1"></i> Hledat
                    </button>
                </form>
            </div>

            <div class="flex flex-wrap gap-2 mt-4">
                <a href="blog.php" class="px-3 py-1.5 rounded-lg text-sm <?= !$statusFilter ? 'bg-teal-100 text-teal-700 font-medium' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">Vše (<?= count($posts) ?>)</a>
                <a href="blog.php?status=published" class="px-3 py-1.5 rounded-lg text-sm <?= $statusFilter === 'published' ? 'bg-green-100 text-green-800 font-medium' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">Publikováno</a>
                <a href="blog.php?status=draft" class="px-3 py-1.5 rounded-lg text-sm <?= $statusFilter === 'draft' ? 'bg-amber-100 text-amber-800 font-medium' : 'bg-slate-100 text-slate-600 hover:bg-slate-200' ?>">Koncepty</a>
            </div>
        </div>

        <div class="bg-white rounded-xl shadow-sm overflow-hidden">
            <div class="overflow-x-hidden">
                <table class="w-full [&>tbody>tr:nth-child(odd)]:bg-white [&>tbody>tr:nth-child(even)]:bg-slate-50/50">
                    <thead class="bg-slate-50 border-b border-slate-200">
                        <tr>
                            <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">Nadpis</th>
                            <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">Stav</th>
                            <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">Autor</th>
                            <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">Datum</th>
                            <th class="text-left py-4 px-6 text-sm font-semibold text-slate-600">Akce</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($posts as $p): ?>
                        <tr class="border-b border-slate-100 hover:!bg-slate-100" id="post-<?= $p['id'] ?>">
                            <td class="py-4 px-6">
                                <a href="blog-edit.php?id=<?= (int)$p['id'] ?>" class="font-medium text-teal-600 hover:underline">
                                    <?= htmlspecialchars($p['title']) ?>
                                </a>
                                <?php if ($p['slug']): ?>
                                <span class="block text-xs text-slate-400 mt-0.5">/blog/<?= htmlspecialchars($p['slug']) ?></span>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 px-6">
                                <?php if ($p['status'] === 'published'): ?>
                                <span class="px-2 py-1 rounded text-xs font-medium bg-green-100 text-green-800">Publikováno</span>
                                <?php else: ?>
                                <span class="px-2 py-1 rounded text-xs font-medium bg-amber-100 text-amber-800">Koncept</span>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 px-6 text-sm text-slate-600">
                                <?= htmlspecialchars($p['author_name'] ?? '—') ?>
                            </td>
                            <td class="py-4 px-6 text-sm text-slate-600">
                                <?php if ($p['published_at']): ?>
                                    <?= date('d.m.Y H:i', strtotime($p['published_at'])) ?>
                                <?php else: ?>
                                    <?= date('d.m.Y H:i', strtotime($p['valid_from'])) ?>
                                <?php endif; ?>
                            </td>
                            <td class="py-4 px-6">
                                <div class="flex gap-2">
                                    <a href="blog-edit.php?id=<?= (int)$p['id'] ?>" class="px-3 py-1.5 bg-teal-600 hover:bg-teal-700 text-white text-xs font-medium rounded-lg transition-colors">
                                        Upravit
                                    </a>
                                    <?php if ($p['status'] === 'published'): ?>
                                    <a href="../blog/<?= htmlspecialchars($p['slug']) ?>" target="_blank" class="px-3 py-1.5 bg-slate-200 hover:bg-slate-300 text-slate-700 text-xs font-medium rounded-lg transition-colors">
                                        Zobrazit
                                    </a>
                                    <?php endif; ?>
                                    <button onclick="deletePost(<?= (int)$p['id'] ?>, '<?= htmlspecialchars(addslashes($p['title'])) ?>')" class="px-3 py-1.5 bg-red-600 hover:bg-red-700 text-white text-xs font-medium rounded-lg transition-colors">
                                        Smazat
                                    </button>
                                </div>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
            <?php if (empty($posts)): ?>
            <div class="p-12 text-center text-slate-500">
                <i data-lucide="file-text" class="w-12 h-12 mx-auto mb-4 text-slate-300"></i>
                <p>Zatím žádné články.</p>
                <a href="blog-edit.php" class="inline-flex items-center gap-2 mt-4 px-4 py-2 bg-teal-600 hover:bg-teal-700 text-white text-sm font-medium rounded-lg">
                    <i data-lucide="plus" class="w-4 h-4"></i> Vytvořit první článek
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>
<?php include __DIR__ . '/includes/layout-end.php'; ?>

    <script>
        lucide.createIcons();

        function deletePost(id, title) {
            if (!confirm('Opravdu smazat článek „' + title + '"?')) return;
            fetch('blog.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: 'action=delete&id=' + id,
                cache: 'no-store'
            })
            .then(r => r.json())
            .then(data => {
                if (data.success) {
                    const row = document.getElementById('post-' + id);
                    if (row) row.remove();
                } else {
                    alert(data.error || 'Chyba při mazání.');
                }
            })
            .catch(() => alert('Chyba při mazání.'));
        }
    </script>
</body>
</html>
