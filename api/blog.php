<?php
/**
 * Blog API – CRUD operace pro blog články
 * Vyžaduje admin session
 */
session_start();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/crud.php';

header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

if (!isset($_SESSION['walance_admin'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'error' => 'Nepřihlášen']);
    exit;
}

$action = $_POST['action'] ?? $_GET['action'] ?? '';

try {
    switch ($action) {

        case 'save':
            $id = (int)($_POST['id'] ?? 0);
            $title = trim($_POST['title'] ?? '');
            $slug = trim($_POST['slug'] ?? '');
            $excerpt = trim($_POST['excerpt'] ?? '');
            $body = $_POST['body'] ?? '';
            $status = in_array($_POST['status'] ?? '', ['draft', 'published']) ? $_POST['status'] : 'draft';
            $metaDescription = trim($_POST['meta_description'] ?? '');
            $featuredImage = trim($_POST['featured_image'] ?? '');

            if (!$title) {
                echo json_encode(['success' => false, 'error' => 'Nadpis je povinný.']);
                exit;
            }

            if (!$slug) {
                $slug = generateSlug($title);
            }
            $slug = sanitizeSlug($slug);

            // Kontrola unikátnosti slugu
            $db = getDb();
            $slugCheck = $db->prepare("SELECT id FROM blog_posts WHERE slug = ? AND valid_to IS NULL" . ($id ? " AND id != ?" : ""));
            $slugParams = [$slug];
            if ($id) $slugParams[] = $id;
            $slugCheck->execute($slugParams);
            if ($slugCheck->fetch()) {
                $slug .= '-' . time();
            }

            $data = [
                'title' => $title,
                'slug' => $slug,
                'excerpt' => $excerpt,
                'body' => $body,
                'status' => $status,
                'meta_description' => $metaDescription,
                'featured_image' => $featuredImage,
                'author_id' => getCurrentUserId(),
            ];

            if ($status === 'published') {
                if ($id) {
                    $existing = findActive('blog_posts', $id);
                    if ($existing && empty($existing['published_at'])) {
                        $data['published_at'] = date('Y-m-d H:i:s');
                    }
                } else {
                    $data['published_at'] = date('Y-m-d H:i:s');
                }
            }

            if ($id) {
                softUpdate('blog_posts', $id, $data);
                echo json_encode(['success' => true, 'message' => 'Článek uložen.']);
            } else {
                $newId = softInsert('blog_posts', $data);
                echo json_encode(['success' => true, 'id' => $newId, 'message' => 'Článek vytvořen.']);
            }
            break;

        case 'delete':
            $id = (int)($_POST['id'] ?? 0);
            if (!$id) {
                echo json_encode(['success' => false, 'error' => 'Chybí ID.']);
                exit;
            }
            softDelete('blog_posts', $id);
            echo json_encode(['success' => true, 'message' => 'Článek smazán.']);
            break;

        case 'get':
            $id = (int)($_GET['id'] ?? 0);
            if (!$id) {
                echo json_encode(['success' => false, 'error' => 'Chybí ID.']);
                exit;
            }
            $post = findActive('blog_posts', $id);
            if (!$post) {
                echo json_encode(['success' => false, 'error' => 'Článek nenalezen.']);
                exit;
            }
            echo json_encode(['success' => true, 'post' => $post]);
            break;

        case 'list':
            $db = getDb();
            $status = $_GET['status'] ?? '';
            $sql = "SELECT bp.*, au.name as author_name FROM blog_posts bp 
                    LEFT JOIN admin_users au ON bp.author_id = au.id 
                    WHERE bp.valid_to IS NULL";
            $params = [];
            if ($status && in_array($status, ['draft', 'published'])) {
                $sql .= " AND bp.status = ?";
                $params[] = $status;
            }
            $sql .= " ORDER BY bp.valid_from DESC";
            $stmt = $db->prepare($sql);
            $stmt->execute($params);
            $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);
            echo json_encode(['success' => true, 'posts' => $posts]);
            break;

        case 'upload_image':
            if (!isset($_FILES['image']) || $_FILES['image']['error'] !== UPLOAD_ERR_OK) {
                echo json_encode(['success' => false, 'error' => 'Chyba při uploadu.']);
                exit;
            }

            $file = $_FILES['image'];
            $allowed = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            if (!in_array($file['type'], $allowed)) {
                echo json_encode(['success' => false, 'error' => 'Nepodporovaný formát.']);
                exit;
            }

            $uploadDir = __DIR__ . '/../assets/blog/';
            if (!is_dir($uploadDir)) {
                mkdir($uploadDir, 0755, true);
            }

            $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid('blog_') . '.' . $ext;
            $filepath = $uploadDir . $filename;

            if (move_uploaded_file($file['tmp_name'], $filepath)) {
                echo json_encode(['success' => true, 'url' => 'assets/blog/' . $filename]);
            } else {
                echo json_encode(['success' => false, 'error' => 'Nelze uložit soubor.']);
            }
            break;

        default:
            echo json_encode(['success' => false, 'error' => 'Neznámá akce.']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'error' => $e->getMessage()]);
}

/**
 * Generování URL slugu z titulku
 */
function generateSlug(string $title): string {
    $slug = mb_strtolower($title, 'UTF-8');
    $translitMap = [
        'á'=>'a','č'=>'c','ď'=>'d','é'=>'e','ě'=>'e','í'=>'i','ň'=>'n',
        'ó'=>'o','ř'=>'r','š'=>'s','ť'=>'t','ú'=>'u','ů'=>'u','ý'=>'y','ž'=>'z',
        'Á'=>'A','Č'=>'C','Ď'=>'D','É'=>'E','Ě'=>'E','Í'=>'I','Ň'=>'N',
        'Ó'=>'O','Ř'=>'R','Š'=>'S','Ť'=>'T','Ú'=>'U','Ů'=>'U','Ý'=>'Y','Ž'=>'Z',
    ];
    $slug = strtr($slug, $translitMap);
    $slug = preg_replace('/[^a-z0-9]+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug;
}

/**
 * Sanitizace slugu (uživatel mohl zadat vlastní)
 */
function sanitizeSlug(string $slug): string {
    $slug = mb_strtolower($slug, 'UTF-8');
    $translitMap = [
        'á'=>'a','č'=>'c','ď'=>'d','é'=>'e','ě'=>'e','í'=>'i','ň'=>'n',
        'ó'=>'o','ř'=>'r','š'=>'s','ť'=>'t','ú'=>'u','ů'=>'u','ý'=>'y','ž'=>'z',
    ];
    $slug = strtr($slug, $translitMap);
    $slug = preg_replace('/[^a-z0-9-]+/', '-', $slug);
    $slug = trim($slug, '-');
    return $slug;
}
