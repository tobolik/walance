<?php
/**
 * Integrace s Google Calendar API
 * Vyžaduje: composer require google/apiclient
 * Nastavení: api/credentials/google-calendar.json (Service Account)
 */
require_once __DIR__ . '/config.php';

if (!GOOGLE_CALENDAR_ENABLED) {
    throw new Exception('Google Calendar není nakonfigurován.');
}

class GoogleCalendar {
    private $client;
    private $service;
    private $calendarId;

    /** @param string|null $calendarId Přepíše GOOGLE_CALENDAR_ID (např. z admin nastavení) */
    public function __construct(?string $calendarId = null) {
        if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
            throw new Exception('Spusťte v kořenovém adresáři projektu: composer install');
        }
        require_once __DIR__ . '/../vendor/autoload.php';

        $this->client = new Google_Client();
        $this->client->setAuthConfig(GOOGLE_CALENDAR_CREDENTIALS);
        $this->client->addScope(Google_Service_Calendar::CALENDAR);
        $this->client->setAccessType('offline');
        $this->calendarId = $calendarId ?: GOOGLE_CALENDAR_ID;

        $this->service = new Google_Service_Calendar($this->client);
    }

    /** Seznam kalendářů, ke kterým má Service Account přístup (včetně stránkování) */
    public function getCalendarList(): array {
        try {
            $items = [];
            $pageToken = null;
            do {
                $params = [];
                if ($pageToken) $params['pageToken'] = $pageToken;
                $list = $this->service->calendarList->listCalendarList($params);
                foreach ($list->getItems() as $cal) {
                    $items[] = [
                        'id' => $cal->getId(),
                        'summary' => $cal->getSummary() ?: $cal->getId(),
                        'primary' => (bool) $cal->getPrimary(),
                    ];
                }
                $pageToken = $list->getNextPageToken();
            } while ($pageToken);
            return $items;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

    /** Přidá sdílený kalendář do seznamu – sdílené kalendáře se v list() neobjevují automaticky */
    public function addCalendarToList(string $calendarId): bool {
        if (empty($calendarId)) return false;
        try {
            $entry = new \Google_Service_Calendar_CalendarListEntry();
            $entry->setId($calendarId);
            $this->service->calendarList->insert($entry);
            return true;
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * Vytvoří událost v kalendáři. S Domain-Wide Delegation (GOOGLE_CALENDAR_IMPERSONATION)
     * zapisuje do kalendáře jiného uživatele (např. kolegy) a pozve klienta jako účastníka.
     */
    public function createEvent(string $date, string $time, string $name, string $email, string $description = ''): string {
        $start = $date . 'T' . $time . ':00';
        $endTime = date('H:i', strtotime($time . ' +1 hour'));
        $end = $date . 'T' . $endTime . ':00';

        $event = new Google_Service_Calendar_Event([
            'summary' => 'WALANCE: ' . $name,
            'description' => "Rezervace z webu\nE-mail: $email\n\n$description",
            'start' => ['dateTime' => $start, 'timeZone' => 'Europe/Prague'],
            'end' => ['dateTime' => $end, 'timeZone' => 'Europe/Prague'],
            'attendees' => [['email' => $email], ['email' => CONTACT_EMAIL]],
        ]);

        $service = $this->getServiceForCalendar($this->calendarId);
        $created = $service->events->insert($this->calendarId, $event);
        return $created->getId();
    }

    /**
     * Aktualizuje datum a čas události v kalendáři.
     * @param string $eventId ID události z Google Calendar
     * @param string $date YYYY-MM-DD
     * @param string $time HH:MM
     */
    public function updateEvent(string $eventId, string $date, string $time): void {
        $service = $this->getServiceForCalendar($this->calendarId);
        $event = $service->events->get($this->calendarId, $eventId);
        $start = $date . 'T' . $time . ':00';
        $endTime = date('H:i', strtotime($time . ' +1 hour'));
        $end = $date . 'T' . $endTime . ':00';
        $event->setStart(new \Google_Service_Calendar_EventDateTime([
            'dateTime' => $start,
            'timeZone' => 'Europe/Prague',
        ]));
        $event->setEnd(new \Google_Service_Calendar_EventDateTime([
            'dateTime' => $end,
            'timeZone' => 'Europe/Prague',
        ]));
        $service->events->update($this->calendarId, $eventId, $event);
    }

    /**
     * Vrací obsazené časy (HH:MM) pro daný den z Google Calendar
     * Události se mapují na sloty podle intervalu – blokuje sloty, které se s událostí překrývají
     * @param array|null $calendarIds Více kalendářů – sjednocení obsazených slotů; null = použít $this->calendarId
     */
    public function getBusySlots(string $date, int $intervalMinutes = 30, int $dayStart = 0, int $dayEnd = 24, ?array $calendarIds = null): array {
        $ids = $calendarIds && !empty($calendarIds) ? $calendarIds : [$this->calendarId];
        $allBusy = [];
        foreach ($ids as $calId) {
            if (empty($calId)) continue;
            $allBusy = array_merge($allBusy, $this->getBusySlotsForCalendar($calId, $date, $intervalMinutes, $dayStart, $dayEnd));
        }
        return array_values(array_unique($allBusy));
    }

    private function getBusySlotsForCalendar(string $calId, string $date, int $intervalMinutes, int $dayStart, int $dayEnd): array {
        $start = $date . 'T00:00:00+01:00';
        $end = $date . 'T23:59:59+01:00';
        $service = $this->getServiceForCalendar($calId);
        try {
            $events = $service->events->listEvents($calId, [
                'timeMin' => $start,
                'timeMax' => $end,
                'singleEvents' => true,
                'orderBy' => 'startTime',
            ]);
        } catch (Exception $e) {
            return [];
        }
        $busy = [];
        foreach ($events->getItems() as $event) {
            $startDt = $event->getStart();
            $endDt = $event->getEnd();
            if (!$startDt || !$endDt) continue;
            $startTime = $startDt->getDateTime() ?: $startDt->getDate();
            $endTime = $endDt->getDateTime() ?: $endDt->getDate();
            if (!$startTime || !$endTime) continue;
            $eventStart = strtotime($startTime);
            $eventEnd = strtotime($endTime);
            if (date('Y-m-d', $eventStart) !== $date) continue;
            for ($h = $dayStart; $h < $dayEnd; $h++) {
                for ($m = 0; $m < 60; $m += $intervalMinutes) {
                    $slotStart = strtotime($date . sprintf(' %02d:%02d:00', $h, $m));
                    $slotEnd = $slotStart + $intervalMinutes * 60;
                    if ($eventStart < $slotEnd && $eventEnd > $slotStart) {
                        $busy[] = sprintf('%02d:%02d', $h, $m);
                    }
                }
            }
        }
        return array_unique($busy);
    }

    /**
     * Vrací obsazené sloty pro celý měsíc – sjednocení ze všech kalendářů
     * @param array|null $calendarIds Více kalendářů; null = použít $this->calendarId
     * @return array ['YYYY-MM-DD' => ['09:00', '10:30', ...], ...]
     */
    public function getBusySlotsForMonth(string $month, int $intervalMinutes = 30, int $dayStart = 0, int $dayEnd = 24, ?array $calendarIds = null): array {
        $ids = $calendarIds && !empty($calendarIds) ? $calendarIds : [$this->calendarId];
        $merged = [];
        foreach ($ids as $calId) {
            if (empty($calId)) continue;
            $byDate = $this->getBusySlotsForMonthSingle($calId, $month, $intervalMinutes, $dayStart, $dayEnd);
            foreach ($byDate as $d => $slots) {
                if (!isset($merged[$d])) $merged[$d] = [];
                $merged[$d] = array_merge($merged[$d], $slots);
            }
        }
        foreach ($merged as $d => $slots) {
            $merged[$d] = array_values(array_unique($slots));
        }
        return $merged;
    }

    private function getBusySlotsForMonthSingle(string $calId, string $month, int $intervalMinutes, int $dayStart, int $dayEnd): array {
        $result = $this->getBusySlotsForMonthSingleWithDetails($calId, $month, $intervalMinutes, $dayStart, $dayEnd);
        $byDate = [];
        foreach ($result as $d => $times) {
            $byDate[$d] = array_keys($times);
        }
        return $byDate;
    }

    /**
     * Vrací obsazené sloty s detaily (kalendář, název události) pro jeden kalendář
     * @return array ['YYYY-MM-DD' => ['09:00' => ['calendar' => calId, 'summary' => '...'], ...], ...]
     */
    private function getBusySlotsForMonthSingleWithDetails(string $calId, string $month, int $intervalMinutes, int $dayStart, int $dayEnd): array {
        $start = $month . '-01T00:00:00+01:00';
        $end = date('Y-m-t', strtotime($month . '-01')) . 'T23:59:59+01:00';
        $service = $this->getServiceForCalendar($calId);
        try {
            $events = $service->events->listEvents($calId, [
                'timeMin' => $start,
                'timeMax' => $end,
                'singleEvents' => true,
                'orderBy' => 'startTime',
            ]);
        } catch (Exception $e) {
            return [];
        }
        $byDate = [];
        foreach ($events->getItems() as $event) {
            $startDt = $event->getStart();
            $endDt = $event->getEnd();
            if (!$startDt || !$endDt) continue;
            $startTime = $startDt->getDateTime() ?: $startDt->getDate();
            $endTime = $endDt->getDateTime() ?: $endDt->getDate();
            if (!$startTime || !$endTime) continue;
            $eventStart = strtotime($startTime);
            $eventEnd = strtotime($endTime);
            $summary = $event->getSummary();
            $summary = $summary !== null && $summary !== '' ? $summary : '(Bez názvu)';
            $date = date('Y-m-d', $eventStart);
            if (!isset($byDate[$date])) $byDate[$date] = [];
            for ($h = $dayStart; $h < $dayEnd; $h++) {
                for ($m = 0; $m < 60; $m += $intervalMinutes) {
                    $slotStart = strtotime($date . sprintf(' %02d:%02d:00', $h, $m));
                    $slotEnd = $slotStart + $intervalMinutes * 60;
                    if ($eventStart < $slotEnd && $eventEnd > $slotStart) {
                        $t = sprintf('%02d:%02d', $h, $m);
                        if (!isset($byDate[$date][$t])) {
                            $byDate[$date][$t] = ['calendar' => $calId, 'summary' => $summary];
                        }
                    }
                }
            }
        }
        return $byDate;
    }

    /**
     * Vrací obsazené sloty s detaily – kalendář a název události (pro admin tooltip)
     * @param array|null $calendarIds Více kalendářů; null = použít $this->calendarId
     * @return array ['YYYY-MM-DD' => ['09:00' => ['calendar' => calId, 'summary' => '...'], ...], ...]
     */
    public function getBusySlotsDetailsForMonth(string $month, int $intervalMinutes = 30, int $dayStart = 0, int $dayEnd = 24, ?array $calendarIds = null): array {
        $ids = $calendarIds && !empty($calendarIds) ? $calendarIds : [$this->calendarId];
        $merged = [];
        foreach ($ids as $calId) {
            if (empty($calId)) continue;
            $byDate = $this->getBusySlotsForMonthSingleWithDetails($calId, $month, $intervalMinutes, $dayStart, $dayEnd);
            foreach ($byDate as $d => $times) {
                if (!isset($merged[$d])) $merged[$d] = [];
                foreach ($times as $t => $info) {
                    if (!isset($merged[$d][$t])) {
                        $merged[$d][$t] = $info;
                    }
                }
            }
        }
        return $merged;
    }

    /**
     * Vrací události pro kontrolu – datum, čas, název (pro debug/admin)
     * @param array|null $calendarIds Více kalendářů; null = použít $this->calendarId
     */
    public function getEventsForDisplay(string $fromDate, int $days = 14, ?array $calendarIds = null): array {
        $ids = $calendarIds && !empty($calendarIds) ? $calendarIds : [$this->calendarId];
        $allItems = [];
        foreach ($ids as $calId) {
            if (empty($calId)) continue;
            $result = $this->getEventsForDisplaySingle($calId, $fromDate, $days);
            if (isset($result['error'])) {
                $result['failed_calendar_id'] = $calId;
                return $result;
            }
            $allItems = array_merge($allItems, $result['items']);
        }
        usort($allItems, fn($a, $b) => strcmp($a['date'] . $a['start'], $b['date'] . $b['start']));
        return ['items' => $allItems];
    }

    private function getEventsForDisplaySingle(string $calId, string $fromDate, int $days): array {
        $start = $fromDate . 'T00:00:00+01:00';
        $endDate = date('Y-m-d', strtotime($fromDate . " +$days days"));
        $end = $endDate . 'T23:59:59+01:00';
        $items = [];
        $pageToken = null;
        $service = $this->getServiceForCalendar($calId);
        do {
            try {
                $params = [
                    'timeMin' => $start,
                    'timeMax' => $end,
                    'singleEvents' => true,
                    'orderBy' => 'startTime',
                ];
                if ($pageToken) $params['pageToken'] = $pageToken;
                $events = $service->events->listEvents($calId, $params);
            } catch (Exception $e) {
                $msg = $e->getMessage();
                if (strpos($msg, '404') !== false || strpos($msg, 'notFound') !== false || strpos($msg, 'Not Found') !== false) {
                    $msg = 'Kalendář nebyl nalezen (404). Zkontrolujte: 1) ID je správně (e-mail), 2) Uživatel je v Google Workspace doméně, 3) Domain-Wide Delegation je nastavené, 4) GOOGLE_CALENDAR_IMPERSONATION je v config.local.php.';
                }
                return ['error' => $msg, 'items' => []];
            }
            foreach ($events->getItems() as $event) {
                $startDt = $event->getStart();
                $endDt = $event->getEnd();
                if (!$startDt || !$endDt) continue;
                $startTime = $startDt->getDateTime() ?: $startDt->getDate();
                $endTime = $endDt->getDateTime() ?: $endDt->getDate();
                if (!$startTime || !$endTime) continue;
                $items[] = [
                    'date' => date('Y-m-d', strtotime($startTime)),
                    'start' => date('H:i', strtotime($startTime)),
                    'end' => date('H:i', strtotime($endTime)),
                    'summary' => $event->getSummary() ?: '(bez názvu)',
                ];
            }
            $pageToken = $events->getNextPageToken();
        } while ($pageToken);
        return ['items' => $items];
    }

    /** Vrací ID kalendáře, který se používá */
    public function getCalendarId(): string {
        return $this->calendarId;
    }

    /** Pro kalendář s e-mailovým ID a impersonation – vrací service s setSubject (čtení i zápis) */
    private function getServiceForCalendar(string $calId): \Google_Service_Calendar {
        if (defined('GOOGLE_CALENDAR_IMPERSONATION') && GOOGLE_CALENDAR_IMPERSONATION
            && $calId && strpos($calId, '@') !== false) {
            $client = new \Google_Client();
            $client->setAuthConfig(GOOGLE_CALENDAR_CREDENTIALS);
            $client->addScope(Google_Service_Calendar::CALENDAR);
            $client->setAccessType('offline');
            $client->setSubject($calId);
            return new Google_Service_Calendar($client);
        }
        return $this->service;
    }
}
