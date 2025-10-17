<?php

namespace App\Http\Controllers;

use App\Http\Requests\BookingStoreRequest;
use App\Models\Service;
use App\Models\ServiceOption;
use App\Services\BookingService;
use Illuminate\Http\JsonResponse;
use RuntimeException;

class BookingController extends Controller
{
    public function __construct(private readonly BookingService $bookingService)
    {
    }

    public function store(BookingStoreRequest $request): JsonResponse
    {
        $service = Service::query()->where('id', $request->integer('service_id'))->where('is_active', true)->firstOrFail();
        $option = ServiceOption::query()->where('id', $request->integer('service_option_id'))->where('service_id', $service->id)->where('is_active', true)->firstOrFail();

        try {
            $booking = $this->bookingService->create(
                $service,
                $option,
                $request->string('date')->toString(),
                $request->string('time')->toString(),
                trim($request->string('customer_name')->toString()),
                preg_replace('/\D+/', '', $request->string('customer_phone')->toString()),
            );
        } catch (RuntimeException $e) {
            if ($e->getMessage() === 'slot_conflict') {
                return response()->json(['message' => 'Слот занят'], 409);
            }
            throw $e;
        }

        return response()->json(['booking' => $booking], 201);
    }
}


