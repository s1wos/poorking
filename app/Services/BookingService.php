<?php

namespace App\Services;

use App\Models\Booking;
use App\Models\Service;
use App\Models\ServiceOption;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class BookingService
{
    public function create(Service $service, ServiceOption $option, string $dateMsk, string $timeMsk, string $name, string $phone): Booking
    {
        $effective = $option->duration_minutes + 30;

        $startMsk = new \DateTime("$dateMsk $timeMsk", new \DateTimeZone('Europe/Moscow'));
        $startUtc = (clone $startMsk)->setTimezone(new \DateTimeZone('UTC'));
        $endUtc = (clone $startUtc)->modify("+$effective minutes");

        return DB::transaction(function () use ($service, $option, $startUtc, $endUtc, $name, $phone) {
            $conflict = Booking::query()
                ->where('service_id', $service->id)
                ->where('status', 'booked')
                ->where('starts_at', '<', $endUtc->format('Y-m-d H:i:s.u'))
                ->where('ends_at', '>', $startUtc->format('Y-m-d H:i:s.u'))
                ->lockForUpdate()
                ->exists();

            if ($conflict) {
                throw new RuntimeException('slot_conflict');
            }

            return Booking::query()->create([
                'service_id' => $service->id,
                'service_option_id' => $option->id,
                'customer_name' => $name,
                'customer_phone' => $phone,
                'starts_at' => $startUtc->format('Y-m-d H:i:s.u'),
                'ends_at' => $endUtc->format('Y-m-d H:i:s.u'),
                'status' => 'booked',
            ]);
        });
    }
}


