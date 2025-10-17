<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use App\Models\{Venue, Service, ServiceOption, ServiceSchedule, Booking};

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::transaction(function () {
            $venue = Venue::query()->firstOrCreate(['name' => 'Основная площадка']);

            $quad = Service::query()->firstOrCreate(
                ['venue_id' => $venue->id, 'name' => 'Поездка на квадроцикле'],
                ['image_path' => 'images/cuadro.png', 'is_active' => true]
            );
            if ($quad->image_path !== 'images/cuadro.png') {
                $quad->update(['image_path' => 'images/cuadro.png']);
            }

            $enduro = Service::query()->firstOrCreate(
                ['venue_id' => $venue->id, 'name' => 'Тур на эндуро'],
                ['image_path' => 'images/moto.png', 'is_active' => true]
            );
            if ($enduro->image_path !== 'images/moto.png') {
                $enduro->update(['image_path' => 'images/moto.png']);
            }

            $quad30 = ServiceOption::query()->firstOrCreate(['service_id' => $quad->id, 'duration_minutes' => 30], ['is_active' => true]);
            $quad60 = ServiceOption::query()->firstOrCreate(['service_id' => $quad->id, 'duration_minutes' => 60], ['is_active' => true]);
            $enduro60 = ServiceOption::query()->firstOrCreate(['service_id' => $enduro->id, 'duration_minutes' => 60], ['is_active' => true]);
            $enduro120 = ServiceOption::query()->firstOrCreate(['service_id' => $enduro->id, 'duration_minutes' => 120], ['is_active' => true]);

            foreach (range(1, 6) as $w) {
                ServiceSchedule::updateOrCreate(
                    ['service_id' => $quad->id, 'weekday' => $w],
                    ['start_time' => '10:00:00', 'end_time' => '20:00:00', 'slot_step_minutes' => 30, 'is_open' => true]
                );
                ServiceSchedule::updateOrCreate(
                    ['service_id' => $enduro->id, 'weekday' => $w],
                    ['start_time' => '10:00:00', 'end_time' => '20:00:00', 'slot_step_minutes' => 30, 'is_open' => true]
                );
            }
            ServiceSchedule::updateOrCreate(
                ['service_id' => $quad->id, 'weekday' => 7],
                ['start_time' => '10:00:00', 'end_time' => '20:00:00', 'slot_step_minutes' => 30, 'is_open' => false]
            );
            ServiceSchedule::updateOrCreate(
                ['service_id' => $enduro->id, 'weekday' => 7],
                ['start_time' => '10:00:00', 'end_time' => '20:00:00', 'slot_step_minutes' => 30, 'is_open' => false]
            );

            $book = function (Service $service, ServiceOption $option, string $dateMsk, string $timeMsk) {
                $effective = $option->duration_minutes + 30;
                $startMsk = new \DateTime("{$dateMsk} {$timeMsk}", new \DateTimeZone('Europe/Moscow'));
                $startUtc = (clone $startMsk)->setTimezone(new \DateTimeZone('UTC'));
                $endUtc = (clone $startUtc)->modify("+{$effective} minutes");
                Booking::query()->firstOrCreate(
                    [
                        'service_id' => $service->id,
                        'service_option_id' => $option->id,
                        'starts_at' => $startUtc->format('Y-m-d H:i:s.u'),
                        'ends_at' => $endUtc->format('Y-m-d H:i:s.u'),
                    ],
                    [
                        'customer_name' => 'Seed',
                        'customer_phone' => '0000000000',
                        'status' => 'booked',
                    ]
                );
            };

            $year = (int) date('Y');
            $d16 = sprintf('%04d-10-16', $year);
            $d17 = sprintf('%04d-10-17', $year);

            foreach ([['date' => $d16, 'time' => '13:00'], ['date' => $d16, 'time' => '16:00'],
                      ['date' => $d17, 'time' => '10:00'], ['date' => $d17, 'time' => '11:00'],
                      ['date' => $d17, 'time' => '13:00'], ['date' => $d17, 'time' => '18:00']] as $s) {
                $book($quad, $quad30, $s['date'], $s['time']);
            }
            $book($quad, $quad60, $d16, '10:00');

            foreach ([['date' => $d16, 'time' => '10:00'], ['date' => $d16, 'time' => '11:30'], ['date' => $d16, 'time' => '18:30']] as $s) {
                $book($enduro, $enduro60, $s['date'], $s['time']);
            }
            $book($enduro, $enduro120, $d17, '14:00');
        });
    }
}
