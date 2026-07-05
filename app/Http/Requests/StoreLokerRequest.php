<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLokerRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'judul_loker' => ['required', 'string', 'max:150'],
            'deskripsi_loker' => ['nullable', 'string'],
            'lokasi' => ['nullable', 'string', 'max:150'],
            'status_loker' => ['required', 'in:dibuka,ditutup'],
            'start_time' => ['nullable', 'date'],
            'end_time' => ['nullable', 'date', 'after_or_equal:start_time'],
        ];
    }
}
