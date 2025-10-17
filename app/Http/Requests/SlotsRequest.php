<?php

namespace App\Http\Requests;

use App\Models\Service;
use App\Models\ServiceOption;
use Illuminate\Foundation\Http\FormRequest;

class SlotsRequest extends FormRequest
{
    public function rules(): array
    {
        return [
            'date' => ['required', 'date_format:Y-m-d'],
            'option_id' => ['required', 'integer', 'exists:service_options,id'],
        ];
    }

    public function prepareForValidation(): void
    {
        if ($this->has('date')) {
            $this->merge(['date' => (string) $this->input('date')]);
        }
    }
}


