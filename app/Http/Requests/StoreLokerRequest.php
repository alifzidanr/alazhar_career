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
            'wilayah' => ['nullable', 'string', 'max:150'],
            'id_pendidikan_terakhir' => ['required', 'exists:pendidikan_terakhir,id_pendidikan_terakhir'],
            'id_jenjang' => ['required', 'exists:jenjang,id_jenjang'],
            'status_loker' => ['required', 'in:dibuka,ditutup'],
            'start_time' => ['nullable', 'date'],
            'end_time' => ['nullable', 'date', 'after_or_equal:start_time'],
        ];
    }
}
