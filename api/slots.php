<?php
/**
 * Vrací dostupné časové sloty pro rezervaci
 * ?month=YYYY-MM - měsíční přehled s procentuální obsazeností
 * Bez parametru - sloty na X dní dopředu (zpětná kompatibilita)
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';

$startHour = SLOT_START;
$endHour = SLOT_END;
$interval = SLOT_INTERVAL;

// Generování všech slotů pro jeden pracovní den
$allDaySlots = [];
for ($h = $startHour; $h < $endHour; $h++) {
    for ($m = 0; $m < 60; $m += $interval) {
        $allDaySlots[] = sprintf('%02d:%02d', $h, $m);
    }
}
$totalSlotsPerDay = count($allDaySlots);

// Načíst rezervace z DB (soft-update: valid_to IS NULL)
$booked = [];
try {
    $db = getDb();
    $stmt = $db->query("SELECT booking_date, booking_time, status FROM bookings WHERE valid_to IS NULL AND (status = 'confirmed' OR status = 'pending')");
    $booked = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {}

$bookedByDate = [];      // datum => [čas] (pro blokování slotů)
$pendingByDate = [];    // datum => [čas] (čekající)
$confirmedByDate = [];  // datum => [čas] (potvrzené)
foreach ($booked as $b) {
    $bookedByDate[$b['booking_date']][] = $b['booking_time'];
    if ($b['status'] === 'pending') {
        $pendingByDate[$b['booking_date']][] = $b['booking_time'];
    } else {
        $confirmedByDate[$b['booking_date']][] = $b['booking_time'];
    }
}

$monthParam = $_GET['month'] ?? null;

if ($monthParam && preg_match('/^\d{4}-\d{2}$/', $monthParam)) {
    // Měsíční režim - celý kalendář s procenty volnosti
    $firstDay = new DateTime($monthParam . '-01', new DateTimeZone('Europe/Prague'));
    $lastDay = (clone $firstDay)->modify('last day of this month');
    $daysInMonth = (int) $lastDay->format('t');
    
    $slots = [];
    $availability = []; // datum => { free, total, percent }
    
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $date = (clone $firstDay)->setDate((int)$firstDay->format('Y'), (int)$firstDay->format('m'), $day);
        $dayOfWeek = (int) $date->format('w');
        if ($dayOfWeek === 0 || $dayOfWeek === 6) continue; // víkend
        
        $dateStr = $date->format('Y-m-d');
        $daySlots = $allDaySlots;
        
        if (isset($bookedByDate[$dateStr])) {
            $bookedTimes = $bookedByDate[$dateStr];
            $daySlots = array_values(array_filter($daySlots, function($s) use ($bookedTimes) { return !in_array($s, $bookedTimes); }));
        }
        
        $free = count($daySlots);
        $percent = $totalSlotsPerDay > 0 ? round(100 * $free / $totalSlotsPerDay) : 0;
        $pending = isset($pendingByDate[$dateStr]) ? count($pendingByDate[$dateStr]) : 0;
        $confirmed = isset($confirmedByDate[$dateStr]) ? count($confirmedByDate[$dateStr]) : 0;
        
        $slots[$dateStr] = $daySlots;
        $availability[$dateStr] = ['free' => $free, 'total' => $totalSlotsPerDay, 'percent' => $percent, 'pending' => $pending, 'confirmed' => $confirmed];
    }
    
    echo json_encode(['slots' => $slots, 'availability' => $availability, 'month' => $monthParam]);
} else {
    // Původní režim - X dní dopředu
    $today = new DateTime('today', new DateTimeZone('Europe/Prague'));
    $slots = [];
    
    for ($d = 0; $d < BOOKING_DAYS_AHEAD; $d++) {
        $date = (clone $today)->modify("+$d days");
        $dayOfWeek = (int) $date->format('w');
        if ($dayOfWeek === 0 || $dayOfWeek === 6) continue;

        $dateStr = $date->format('Y-m-d');
        $daySlots = $allDaySlots;

        if (isset($bookedByDate[$dateStr])) {
            $bookedTimes = $bookedByDate[$dateStr];
            $daySlots = array_values(array_filter($daySlots, function($s) use ($bookedTimes) { return !in_array($s, $bookedTimes); }));
        }

        $slots[$dateStr] = $daySlots;
    }

    echo json_encode(['slots' => $slots]);
}
