<?php
/**
 * CRUD s soft-update logikou (cursor-rules: soft-update-and-versioning.mdc)
 * Pouze softInsert, softUpdate, softDelete – nikdy přímý INSERT/UPDATE/DELETE
 */
require_once __DIR__ . '/db.php';

function getCurrentUserId(): ?int {
    return isset($_SESSION['walance_admin_user_id']) ? (int)$_SESSION['walance_admin_user_id'] : null;
}

/**
 * Vložení nového záznamu – doplní valid_from, valid_to, valid_user_from, {tabulka}_id
 */
function softInsert(string $table, array $data): int {
    $db = getDb();
    $userId = getCurrentUserId();

    $cols = array_keys($data);
    $cols[] = 'valid_from';
    $cols[] = 'valid_to';
    $cols[] = 'valid_user_from';
    $cols[] = 'valid_user_to';

    $placeholders = array_fill(0, count($data), '?');
    $placeholders[] = 'CURRENT_TIMESTAMP';
    $placeholders[] = 'NULL';
    $placeholders[] = $userId ?: 'NULL';
    $placeholders[] = 'NULL';

    $sql = sprintf(
        'INSERT INTO %s (%s) VALUES (%s)',
        $table,
        implode(', ', $cols),
        implode(', ', $placeholders)
    );
    $stmt = $db->prepare($sql);
    $stmt->execute(array_values($data));

    $id = (int)$db->lastInsertId();

    // Nastavit entity_id = id pro nový záznam
    $entityCol = $table === 'contacts' ? 'contacts_id' : ($table === 'bookings' ? 'bookings_id' : ($table === 'activities' ? 'activities_id' : null));
    if ($entityCol) {
        $db->prepare("UPDATE $table SET $entityCol = ? WHERE id = ?")->execute([$id, $id]);
    }

    return $id;
}

/**
 * Úprava – uzavře starou verzi, vloží novou se stejným entity_id
 * $data = pole polí k aktualizaci (zbytek se zkopíruje ze staré verze)
 */
function softUpdate(string $table, int $id, array $data): void {
    $db = getDb();
    $userId = getCurrentUserId();
    $entityCol = $table === 'contacts' ? 'contacts_id' : ($table === 'bookings' ? 'bookings_id' : ($table === 'activities' ? 'activities_id' : null));

    $row = findActive($table, $id);
    if (!$row) {
        throw new RuntimeException("Záznam id=$id nenalezen nebo již uzavřen.");
    }

    $entityId = $entityCol ? ($row[$entityCol] ?? $id) : $id;

    // Sloučit starý řádek s novými daty (bez id, valid_*)
    $skip = ['id', 'valid_from', 'valid_to', 'valid_user_from', 'valid_user_to', $entityCol];
    $merged = array_merge($row, $data);
    foreach ($skip as $k) unset($merged[$k]);

    $merged[$entityCol] = $entityId;
    $merged['valid_from'] = date('Y-m-d H:i:s');
    $merged['valid_to'] = null;
    $merged['valid_user_from'] = $userId;
    $merged['valid_user_to'] = null;

    // Uzavřít starou verzi
    $db->prepare("UPDATE $table SET valid_to = CURRENT_TIMESTAMP, valid_user_to = ? WHERE id = ?")
        ->execute([$userId, $id]);

    // Vložit novou verzi
    $cols = array_keys($merged);
    $placeholders = implode(', ', array_fill(0, count($cols), '?'));
    $sql = sprintf('INSERT INTO %s (%s) VALUES (%s)', $table, implode(', ', $cols), $placeholders);
    $db->prepare($sql)->execute(array_values($merged));
}

/**
 * Soft delete – nastaví valid_to, valid_user_to
 */
function softDelete(string $table, int $id): void {
    $db = getDb();
    $userId = getCurrentUserId();
    $db->prepare("UPDATE $table SET valid_to = CURRENT_TIMESTAMP, valid_user_to = ? WHERE id = ? AND valid_to IS NULL")
        ->execute([$userId, $id]);
}

/**
 * Načtení aktivního záznamu podle fyzického id
 */
function findActive(string $table, int $id): ?array {
    $db = getDb();
    $stmt = $db->prepare("SELECT * FROM $table WHERE id = ? AND valid_to IS NULL");
    $stmt->execute([$id]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

/**
 * Načtení aktivního záznamu podle entity_id
 */
function findActiveByEntityId(string $table, int $entityId): ?array {
    $entityCol = $table === 'contacts' ? 'contacts_id' : ($table === 'bookings' ? 'bookings_id' : ($table === 'activities' ? 'activities_id' : null));
    if (!$entityCol) return null;

    $db = getDb();
    $stmt = $db->prepare("SELECT * FROM $table WHERE $entityCol = ? AND valid_to IS NULL");
    $stmt->execute([$entityId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ?: null;
}

/**
 * Načtení všech aktivních záznamů
 */
function findAllActive(string $table, string $order = 'id DESC'): array {
    $db = getDb();
    $stmt = $db->query("SELECT * FROM $table WHERE valid_to IS NULL ORDER BY $order");
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
