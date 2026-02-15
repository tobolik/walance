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

    /** Seznam kalendářů, ke kterým má Service Account přístup */
    public function getCalendarList(): array {
        try {
            $list = $this->service->calendarList->listCalendarList();
            $items = [];
            foreach ($list->getItems() as $cal) {
                $items[] = [
                    'id' => $cal->getId(),
                    'summary' => $cal->getSummary() ?: $cal->getId(),
                    'primary' => (bool) $cal->getPrimary(),
                ];
            }
            return $items;
        } catch (Exception $e) {
            return ['error' => $e->getMessage()];
        }
    }

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

        $created = $this->service->events->insert($this->calendarId, $event);
        return $created->getId();
    }

    /**
     * Vrací obsazené časy (HH:MM) pro daný den z Google Calendar
     * Události se mapují na sloty podle intervalu – blokuje sloty, které se s událostí překrývají
     */
    public function getBusySlots(string $date, int $intervalMinutes = 30, int $dayStart = 0, int $dayEnd = 24): array {
        $start = $date . 'T00:00:00+01:00';
        $end = $date . 'T23:59:59+01:00';
        try {
            $events = $this->service->events->listEvents($this->calendarId, [
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
     * Vrací obsazené sloty pro celý měsíc v jednom API volání – rychlejší než getBusySlots pro každý den
     * @return array ['YYYY-MM-DD' => ['09:00', '10:30', ...], ...]
     */
    public function getBusySlotsForMonth(string $month, int $intervalMinutes = 30, int $dayStart = 0, int $dayEnd = 24): array {
        $start = $month . '-01T00:00:00+01:00';
        $end = date('Y-m-t', strtotime($month . '-01')) . 'T23:59:59+01:00';
        try {
            $events = $this->service->events->listEvents($this->calendarId, [
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
            $date = date('Y-m-d', $eventStart);
            if (!isset($byDate[$date])) $byDate[$date] = [];
            for ($h = $dayStart; $h < $dayEnd; $h++) {
                for ($m = 0; $m < 60; $m += $intervalMinutes) {
                    $slotStart = strtotime($date . sprintf(' %02d:%02d:00', $h, $m));
                    $slotEnd = $slotStart + $intervalMinutes * 60;
                    if ($eventStart < $slotEnd && $eventEnd > $slotStart) {
                        $byDate[$date][] = sprintf('%02d:%02d', $h, $m);
                    }
                }
            }
        }
        foreach ($byDate as $d => $slots) {
            $byDate[$d] = array_unique($slots);
        }
        return $byDate;
    }

    /**
     * Vrací události pro kontrolu – datum, čas, název (pro debug/admin)
     * S podporou stránkování (Google vrací max 250 událostí na požadavek)
     */
    public function getEventsForDisplay(string $fromDate, int $days = 14): array {
        $start = $fromDate . 'T00:00:00+01:00';
        $endDate = date('Y-m-d', strtotime($fromDate . " +$days days"));
        $end = $endDate . 'T23:59:59+01:00';
        $items = [];
        $pageToken = null;
        do {
            try {
                $params = [
                    'timeMin' => $start,
                    'timeMax' => $end,
                    'singleEvents' => true,
                    'orderBy' => 'startTime',
                ];
                if ($pageToken) $params['pageToken'] = $pageToken;
                $events = $this->service->events->listEvents($this->calendarId, $params);
            } catch (Exception $e) {
                return ['error' => $e->getMessage(), 'items' => []];
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
}
