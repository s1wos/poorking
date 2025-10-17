<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ServiceSchedule extends Model
{
    use HasFactory;

    protected $fillable = [
        'service_id',
        'weekday',
        'start_time',
        'end_time',
        'slot_step_minutes',
        'is_open',
    ];

    protected $casts = [
        'weekday' => 'integer',
        'slot_step_minutes' => 'integer',
        'is_open' => 'boolean',
    ];

    public function service(): BelongsTo
    {
        return $this->belongsTo(Service::class);
    }
}


