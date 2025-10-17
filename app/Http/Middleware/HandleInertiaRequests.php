<?php

namespace App\Http\Middleware;

use Inertia\Middleware;

class HandleInertiaRequests extends Middleware
{
    protected $rootView = 'app';

    public function version($request): ?string
    {
        return parent::version($request);
    }

    public function share($request): array
    {
        return array_merge(parent::share($request), [
            'appName' => config('app.name'),
            'appTimezone' => config('app.timezone'),
        ]);
    }
}


