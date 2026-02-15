<?php
/**
 * Vrací dostupné časové sloty pro rezervaci
 * ?month=YYYY-MM - měsíční přehled s procentuální obsazeností
 * Zohledňuje: DB rezervace, Google Calendar, admin nastavení dostupnosti
 */
header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

require_once __DIR__ . '/config.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/availability.php';

$settings = getAvailabilitySettings();
$startHour = (int)($settings['slot_start'] ?? SLOT_START);
$endHour = (int)($settings['slot_end'] ?? SLOT_END);
$interval = (int)($settings['slot_interval'] ?? SLOT_INTERVAL);
$workDays = $settings['work_days'] ?? [1, 2, 3, 4, 5];
$excludedDates = array_flip($settings['excluded_dates'] ?? []);

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

$bookedByDate = [];
$pendingByDate = [];
$confirmedByDate = [];
foreach ($booked as $b) {
    $bookedByDate[$b['booking_date']][] = $b['booking_time'];
    if ($b['status'] === 'pending') {
        $pendingByDate[$b['booking_date']][] = $b['booking_time'];
    } else {
        $confirmedByDate[$b['booking_date']][] = $b['booking_time'];
    }
}

// Google Calendar – obsazené časy
$gc = null;
if (GOOGLE_CALENDAR_ENABLED && file_exists(__DIR__ . '/GoogleCalendar.php')) {
    try {
        require_once __DIR__ . '/GoogleCalendar.php';
        $gc = new GoogleCalendar();
    } catch (Exception $e) {}
}

$monthParam = $_GET['month'] ?? null;

if ($monthParam && preg_match('/^\d{4}-\d{2}$/', $monthParam)) {
    $firstDay = new DateTime($monthParam . '-01', new DateTimeZone('Europe/Prague'));
    $lastDay = (clone $firstDay)->modify('last day of this month');
    $daysInMonth = (int) $lastDay->format('t');
    
    $slots = [];
    $availability = [];
    $slotsDetailByDate = [];
    $gcBusyByDate = [];
    
    for ($day = 1; $day <= $daysInMonth; $day++) {
        $date = (clone $firstDay)->setDate((int)$firstDay->format('Y'), (int)$firstDay->format('m'), $day);
        $dayOfWeek = (int) $date->format('w');
        $dateStr = $date->format('Y-m-d');
        
        if (!in_array($dayOfWeek, $workDays) || isset($excludedDates[$dateStr])) {
            continue;
        }
        
        $daySlots = $allDaySlots;
        
        // GC obsazenost
        if (isset($gc)) {
            try {
                $gcBusy = $gc->getBusySlots($dateStr, $interval, $startHour, $endHour);
                if (!empty($gcBusy)) {
                    $gcBusyByDate[$dateStr] = $gcBusy;
                    $daySlots = array_values(array_filter($daySlots, function($s) use ($gcBusy) { return !in_array($s, $gcBusy); }));
                }
            } catch (Exception $e) {}
        }
        
        // DB rezervace
        if (isset($bookedByDate[$dateStr])) {
            $bookedTimes = $bookedByDate[$dateStr];
            $daySlots = array_values(array_filter($daySlots, function($s) use ($bookedTimes) { return !in_array($s, $bookedTimes); }));
        }
        
        $free = count($daySlots);
        $percent = $totalSlotsPerDay > 0 ? round(100 * $free / $totalSlotsPerDay) : 0;
        $pending = isset($pendingByDate[$dateStr]) ? count($pendingByDate[$dateStr]) : 0;
        $confirmed = isset($confirmedByDate[$dateStr]) ? count($confirmedByDate[$dateStr]) : 0;
        
        $slotsDetail = [];
        foreach ($allDaySlots as $t) {
            if (isset($confirmedByDate[$dateStr]) && in_array($t, $confirmedByDate[$dateStr])) {
                $slotsDetail[$t] = 'confirmed';
            } elseif (isset($pendingByDate[$dateStr]) && in_array($t, $pendingByDate[$dateStr])) {
                $slotsDetail[$t] = 'pending';
            } elseif (isset($gcBusyByDate[$dateStr]) && in_array($t, $gcBusyByDate[$dateStr])) {
                $slotsDetail[$t] = 'confirmed';
            } else {
                $slotsDetail[$t] = 'free';
            }
        }
        
        $slots[$dateStr] = $daySlots;
        $availability[$dateStr] = ['free' => $free, 'total' => $totalSlotsPerDay, 'percent' => $percent, 'pending' => $pending, 'confirmed' => $confirmed];
        $slotsDetailByDate[$dateStr] = $slotsDetail;
    }
    
    echo json_encode(['slots' => $slots, 'availability' => $availability, 'slots_detail' => $slotsDetailByDate, 'month' => $monthParam]);
} else {
    $today = new DateTime('today', new DateTimeZone('Europe/Prague'));
    $slots = [];
    
    for ($d = 0; $d < BOOKING_DAYS_AHEAD; $d++) {
        $date = (clone $today)->modify("+$d days");
        $dayOfWeek = (int) $date->format('w');
        $dateStr = $date->format('Y-m-d');
        
        if (!in_array($dayOfWeek, $workDays) || isset($excludedDates[$dateStr])) {
            continue;
        }
        
        $daySlots = $allDaySlots;
        
        if (isset($gc)) {
            try {
                $gcBusy = $gc->getBusySlots($dateStr, $interval, $startHour, $endHour);
                if (!empty($gcBusy)) {
                    $daySlots = array_values(array_filter($daySlots, function($s) use ($gcBusy) { return !in_array($s, $gcBusy); }));
                }
            } catch (Exception $e) {}
        }
        
        if (isset($bookedByDate[$dateStr])) {
            $bookedTimes = $bookedByDate[$dateStr];
            $daySlots = array_values(array_filter($daySlots, function($s) use ($bookedTimes) { return !in_array($s, $bookedTimes); }));
        }
        
        $slots[$dateStr] = $daySlots;
    }
    
    echo json_encode(['slots' => $slots]);
}
