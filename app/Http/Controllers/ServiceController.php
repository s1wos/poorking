<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceOption;
use Illuminate\Http\Request;
use Inertia\Inertia;

class ServiceController extends Controller
{
    public function index()
    {
        $services = Service::query()->where('is_active', true)->with(['options' => function ($q) {
            $q->where('is_active', true)->orderBy('duration_minutes');
        }])->get();
        return Inertia::render('ServiceListPage', [
            'services' => $services,
        ]);
    }

    public function show(Service $service)
    {
        $service->load(['options' => function ($q) {
            $q->where('is_active', true)->orderBy('duration_minutes');
        }]);
        $defaultOption = $service->options->first();
        return Inertia::render('ServiceBookingPage', [
            'service' => $service,
            'defaultOptionId' => $defaultOption?->id,
        ]);
    }
}


