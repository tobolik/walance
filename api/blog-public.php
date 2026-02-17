<?php
/**
 * Veřejné blog API – čtení publikovaných článků (bez autentizace)
 */
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: public, max-age=300');

$action = $_GET['action'] ?? 'list';

try {
    $db = getDb();

    switch ($action) {

        case 'list':
            $limit = min(max((int)($_GET['limit'] ?? 10), 1), 50);
            $offset = max((int)($_GET['offset'] ?? 0), 0);

            $sql = "SELECT bp.id, bp.blog_posts_id, bp.title, bp.slug, bp.excerpt, bp.featured_image, 
                           bp.published_at, bp.meta_description, au.name as author_name
                    FROM blog_posts bp
                    LEFT JOIN admin_users au ON bp.author_id = au.id
                    WHERE bp.valid_to IS NULL AND bp.status = 'published' AND bp.published_at IS NOT NULL
                    ORDER BY bp.published_at DESC
                    LIMIT ? OFFSET ?";
            $stmt = $db->prepare($sql);
            $stmt->execute([$limit, $offset]);
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $countStmt = $db->query("SELECT COUNT(*) FROM blog_posts WHERE valid_to IS NULL AND status = 'published' AND published_at IS NOT NULL");
            $total = (int)$countStmt->fetchColumn();

            echo json_encode(['success' => true, 'posts' => $posts, 'total' => $total]);
            break;

        case 'detail':
            $slug = trim($_GET['slug'] ?? '');
            if (!$slug) {
                echo json_encode(['success' => false, 'error' => 'Chybí slug.']);
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
                echo json_encode(['success' => false, 'error' => 'Článek nenalezen.']);
                exit;
            }

            echo json_encode(['success' => true, 'post' => $post]);
            break;

        case 'latest':
            $limit = min(max((int)($_GET['limit'] ?? 3), 1), 10);
            $stmt = $db->prepare("SELECT bp.id, bp.blog_posts_id, bp.title, bp.slug, bp.excerpt, bp.featured_image, 
                           bp.published_at, au.name as author_name
                    FROM blog_posts bp
                    LEFT JOIN admin_users au ON bp.author_id = au.id
                    WHERE bp.valid_to IS NULL AND bp.status = 'published' AND bp.published_at IS NOT NULL
                    ORDER BY bp.published_at DESC
                    LIMIT ?");
            $stmt->execute([$limit]);
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'posts' => $posts]);
            break;

        default:
            echo json_encode(['success' => false, 'error' => 'Neznámá akce.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => 'Chyba serveru.']);
}
