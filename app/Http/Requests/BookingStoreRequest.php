<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class BookingStoreRequest extends FormRequest
{
    public function prepareForValidation(): void
    {
        $name = trim((string) $this->input('customer_name', ''));
        $phoneRaw = (string) $this->input('customer_phone', '');
        $phone = preg_replace('/\D+/', '', $phoneRaw);

        $this->merge([
            'customer_name' => $name,
            'customer_phone' => $phone,
        ]);
    }

    public function rules(): array
    {
        return [
            'service_id' => ['required', 'integer', 'exists:services,id'],
            'service_option_id' => ['required', 'integer', 'exists:service_options,id'],
            'customer_name' => ['required', 'string', 'max:100', 'regex:/^[A-Za-zА-Яа-яЁё\s-]+$/u'],
            'customer_phone' => ['required', 'string', 'digits_between:10,15'],
            'date' => ['required', 'date_format:Y-m-d'],
            'time' => ['required', 'date_format:H:i'],
        ];
    }

    public function messages(): array
    {
        return [
            'service_id.required' => 'Не указана услуга.',
            'service_id.exists' => 'Выбранная услуга не найдена.',

            'service_option_id.required' => 'Не указан вариант длительности.',
            'service_option_id.exists' => 'Выбранный вариант недоступен.',

            'customer_name.required' => 'Укажите имя.',
            'customer_name.max' => 'Имя слишком длинное (макс. 100).',
            'customer_name.regex' => 'Неверное имя: допускаются буквы A–Z, a–z, А–Я, а–я, пробел и дефис.',

            'customer_phone.required' => 'Укажите номер телефона.',
            'customer_phone.digits_between' => 'Неверный номер телефона. Введите от 10 до 15 цифр (например, +71234566454).',

            'date.required' => 'Не указана дата.',
            'date.date_format' => 'Дата должна быть в формате YYYY-MM-DD.',

            'time.required' => 'Не указано время.',
            'time.date_format' => 'Время должно быть в формате HH:MM.',
        ];
    }
}


