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

    public function __construct() {
        if (!file_exists(__DIR__ . '/../vendor/autoload.php')) {
            throw new Exception('Spusťte: composer require google/apiclient v kořenovém adresáři projektu.');
        }
        require_once __DIR__ . '/../vendor/autoload.php';

        $this->client = new Google_Client();
        $this->client->setAuthConfig(GOOGLE_CALENDAR_CREDENTIALS);
        $this->client->addScope(Google_Service_Calendar::CALENDAR);
        $this->client->setAccessType('offline');
        $this->calendarId = GOOGLE_CALENDAR_ID;

        $this->service = new Google_Service_Calendar($this->client);
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
     * Vrací události pro kontrolu – datum, čas, název (pro debug/admin)
     */
    public function getEventsForDisplay(string $fromDate, int $days = 14): array {
        $start = $fromDate . 'T00:00:00+01:00';
        $endDate = date('Y-m-d', strtotime($fromDate . " +$days days"));
        $end = $endDate . 'T23:59:59+01:00';
        try {
            $events = $this->service->events->listEvents($this->calendarId, [
                'timeMin' => $start,
                'timeMax' => $end,
                'singleEvents' => true,
                'orderBy' => 'startTime',
            ]);
        } catch (Exception $e) {
            return ['error' => $e->getMessage(), 'items' => []];
        }
        $items = [];
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
        return ['items' => $items];
    }
}
