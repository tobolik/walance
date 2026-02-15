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
        'excluded_slots' => [], // ['date' => ['09:00', '09:30'], ...] – ručně blokované časy
        'google_calendar_id' => '', // prázdné = GOOGLE_CALENDAR_ID z config
    ];
    if (file_exists($path)) {
        $json = @file_get_contents($path);
        if ($json) {
            $data = json_decode($json, true);
            if ($data) {
                $merged = array_merge($default, $data);
                if (empty($merged['excluded_slots'])) $merged['excluded_slots'] = [];
                return $merged;
            }
        }
    }
    return $default;
}

/** Přepne blokovaný slot (přidá/odebere) – vrací nový stav true=blokováno, false=volné */
function toggleExcludedSlot(string $date, string $time): bool {
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $date) || !preg_match('/^\d{2}:\d{2}$/', $time)) {
        return false;
    }
    $settings = getAvailabilitySettings();
    $excluded = $settings['excluded_slots'] ?? [];
    $dateSlots = $excluded[$date] ?? [];
    $idx = array_search($time, $dateSlots);
    if ($idx !== false) {
        array_splice($dateSlots, $idx, 1);
        if (empty($dateSlots)) {
            unset($excluded[$date]);
        } else {
            $excluded[$date] = array_values($dateSlots);
        }
    } else {
        $dateSlots[] = $time;
        sort($dateSlots);
        $excluded[$date] = $dateSlots;
    }
    return saveExcludedSlots($excluded) ? ($idx === false) : false;
}

function saveExcludedSlots(array $excluded): bool {
    $path = dirname(DB_PATH) . '/availability.json';
    $data = [];
    if (file_exists($path)) {
        $json = @file_get_contents($path);
        if ($json) $data = json_decode($json, true) ?: [];
    }
    $data['excluded_slots'] = $excluded;
    $dir = dirname($path);
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    return file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
}

function saveAvailabilitySettings(array $data): bool {
    $dir = dirname(DB_PATH);
    if (!is_dir($dir)) mkdir($dir, 0755, true);
    $path = $dir . '/availability.json';
    $existing = [];
    if (file_exists($path)) {
        $json = @file_get_contents($path);
        if ($json) $existing = json_decode($json, true) ?: [];
    }
    $data = array_intersect_key($data, array_flip(['slot_start', 'slot_end', 'slot_interval', 'work_days', 'excluded_dates', 'google_calendar_id']));
    if (isset($data['work_days']) && is_array($data['work_days'])) {
        $data['work_days'] = array_map('intval', $data['work_days']);
    }
    if (isset($data['excluded_dates']) && is_string($data['excluded_dates'])) {
        $data['excluded_dates'] = array_filter(array_map('trim', explode("\n", $data['excluded_dates'])));
    }
    $data['excluded_slots'] = $existing['excluded_slots'] ?? [];
    return file_put_contents($path, json_encode($data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE)) !== false;
}
