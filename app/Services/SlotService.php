<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Service;
use App\Models\ServiceOption;
use Carbon\CarbonImmutable;
use DateTimeZone;

class SlotService
{
    private const MOSCOW_TZ = 'Europe/Moscow';
    private const UTC_TZ = 'UTC';
    
    public function __construct(private readonly ScheduleService $scheduleService)
    {
    }

    public function getAvailableSlots(Service $service, ServiceOption $option, \DateTimeInterface $dateMsk): array
    {
        $frame = $this->scheduleService->getDayFrame($service, $dateMsk);
        if (!$frame['is_open']) {
            return [];
        }

        $effective = $option->duration_minutes + 30;
        $step = $frame['slot_step_minutes'];

        $startTime = $frame['start_time'];
        $endTime = $frame['end_time'];

        $day = $dateMsk->format('Y-m-d');

        $tzMoscow = new DateTimeZone(self::MOSCOW_TZ);
        $tzUtc = new DateTimeZone(self::UTC_TZ);

        $startMsk = CarbonImmutable::parse("$day $startTime", $tzMoscow);
        $endMsk = CarbonImmutable::parse("$day $endTime", $tzMoscow);

        // последний возможный старт = end - effective
        $lastStartMsk = $endMsk->subMinutes($effective);

        $rangeStartUtc = $startMsk->setTimezone($tzUtc);
        $rangeEndUtc = $endMsk->setTimezone($tzUtc);

        $bookedIntervals = Booking::query()
            ->where('service_id', $service->id)
            ->where('status', 'booked')
            ->where('starts_at', '<', $rangeEndUtc->format('Y-m-d H:i:s.u'))
            ->where('ends_at', '>', $rangeStartUtc->format('Y-m-d H:i:s.u'))
            ->get(['starts_at', 'ends_at'])
            ->map(static function (Booking $booking) use ($tzUtc): array {
                $start = CarbonImmutable::parse($booking->starts_at, $tzUtc)->getTimestamp();
                $end = CarbonImmutable::parse($booking->ends_at, $tzUtc)->getTimestamp();

                return ['start' => $start, 'end' => $end];
            })
            ->all();

        $slots = [];
        for ($cursor = $startMsk; $cursor->lte($lastStartMsk); $cursor = $cursor->addMinutes($step)) {
            $slotStartUtc = $cursor->setTimezone($tzUtc);
            $slotEndUtc = $slotStartUtc->addMinutes($effective);

            $slotStartTs = $slotStartUtc->getTimestamp();
            $slotEndTs = $slotEndUtc->getTimestamp();

            $overlap = false;
            foreach ($bookedIntervals as $interval) {
                if ($slotStartTs < $interval['end'] && $slotEndTs > $interval['start']) {
                    $overlap = true;
                    break;
                }
            }
            
            if (!$overlap) {
                $slots[] = $cursor->format('H:i');
            }
        }

        return $slots;
    }
}


