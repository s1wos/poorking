<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Service;
use App\Models\ServiceOption;

class SlotService
{
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

        $startMsk = new \DateTime("$day $startTime", new \DateTimeZone('Europe/Moscow'));
        $endMsk = new \DateTime("$day $endTime", new \DateTimeZone('Europe/Moscow'));

        // последний возможный старт = end - effective
        $lastStartMsk = (clone $endMsk)->modify("-$effective minutes");

        $slots = [];
        for ($t = clone $startMsk; $t <= $lastStartMsk; $t->modify("+$step minutes")) {
            $startUtc = (clone $t)->setTimezone(new \DateTimeZone('UTC'));
            $endUtc = (clone $startUtc)->modify("+$effective minutes");

            $overlap = Booking::query()
                ->where('service_id', $service->id)
                ->where('status', 'booked')
                ->where('starts_at', '<', $endUtc->format('Y-m-d H:i:s.u'))
                ->where('ends_at', '>', $startUtc->format('Y-m-d H:i:s.u'))
                ->exists();

            if (!$overlap) {
                $slots[] = $t->format('H:i');
            }
        }

        return $slots;
    }
}


