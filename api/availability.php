<?php
/**
 * Načtení nastavení dostupnosti (slot_start, slot_end, interval, work_days, excluded_dates)
 * Zdroj: data/availability.json, fallback na config.php
 */
require_once __DIR__ . '/config.php';

function getAvailabilitySettings(): array {
    $path = dirname(DB_PATH) . '/availability.json';
    $default = [
        'slot_start' => SLOT_START,
        'slot_end' => SLOT_END,
        'slot_interval' => SLOT_INTERVAL,
        'work_days' => [1, 2, 3, 4, 5], // Po-Pá (0=Ne, 1=Po, ..., 6=So)
        'excluded_dates' => [],
    ];
    if (file_exists($path)) {
        $json = @file_get_contents($path);
        if ($json) {
            $data = json_decode($json, true);
            if ($data) {
                return array_merge($default, $data);
            }
        }
    }
    return $default;
}

function saveAvailabilitySettings(array $data): bool {
    $dir = dirname(DB_PATH);
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $path = $dir . '/availability.json';
    $data = array_intersect_key($data, array_flip(['slot_start', 'slot_end', 'slot_interval', 'work_days', 'excluded_dates']));
    if (isset($data['work_days']) && is_array($data['work_days'])) {
        $data['work_days'] = array_map('intval', $data['work_days']);
    }
    if (isset($data['excluded_dates']) && is_string($data['excluded_dates'])) {
        $data['excluded_dates'] = array_filter(array_map('trim', explode("\n", $data['excluded_dates'])));
    }
    return file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
}
