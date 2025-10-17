<?php

namespace App\Http\Controllers;

use App\Http\Requests\SlotsRequest;
use App\Models\Service;
use App\Models\ServiceOption;
use App\Services\SlotService;
use Illuminate\Http\JsonResponse;

class SlotController extends Controller
{
    public function __construct(private readonly SlotService $slotService)
    {
    }

    public function index(SlotsRequest $request, Service $service): JsonResponse
    {
        $option = ServiceOption::query()
            ->where('id', $request->integer('option_id'))
            ->where('service_id', $service->id)
            ->where('is_active', true)
            ->firstOrFail();

        $date = new \DateTime($request->string('date'), new \DateTimeZone('Europe/Moscow'));
        $slots = $this->slotService->getAvailableSlots($service, $option, $date);
        return response()->json(['slots' => $slots]);
    }
}


