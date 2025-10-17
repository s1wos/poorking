<?php

namespace App\Services;

use App\Models\Service;
use App\Models\ServiceSchedule;

class ScheduleService
{
    public function getDayFrame(Service $service, \DateTimeInterface $dateMsk): array
    {
        $weekday = (int) $dateMsk->format('N');

        if ($weekday === 7) {
            return [
                'is_open' => false,
                'start_time' => '10:00:00',
                'end_time' => '20:00:00',
                'slot_step_minutes' => 30,
            ];
        }

        $schedule = ServiceSchedule::query()
            ->where('service_id', $service->id)
            ->where('weekday', $weekday)
            ->first();

        if (!$schedule) {
            return [
                'is_open' => true,
                'start_time' => '10:00:00',
                'end_time' => '20:00:00',
                'slot_step_minutes' => 30,
            ];
        }

        return [
            'is_open' => (bool) $schedule->is_open,
            'start_time' => $schedule->start_time,
            'end_time' => $schedule->end_time,
            'slot_step_minutes' => (int) $schedule->slot_step_minutes,
        ];
    }
}


