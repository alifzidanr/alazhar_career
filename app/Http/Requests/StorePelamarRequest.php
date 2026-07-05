<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePelamarRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            // Step 1: Data Pelamar
            'nama' => ['required', 'string', 'max:150'],
            'tanggal_lahir' => ['required', 'date', 'before:today'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'gelar' => ['nullable', 'string', 'max:50'],
            'no_hp' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:150'],
            'alamat' => ['required', 'string', 'max:1000'],

            // Step 2: Pendidikan
            'id_pendidikan_terakhir' => ['required', 'exists:pendidikan_terakhir,id_pendidikan_terakhir'],
            'institusi' => ['required', 'string', 'max:150'],
            'program_studi' => ['required', 'string', 'max:150'],
            'akreditasi' => ['required', 'string', 'max:20'],
            'tahun_lulus' => ['required', 'integer', 'min:1950', 'max:'.(date('Y') + 1)],
            'ipk' => ['required', 'numeric', 'between:0,4'],

            // Step 3: Unggah Dokumen
            'cv_upload' => ['required', 'file', 'mimes:pdf,doc,docx', 'max:5120'],
            'ijazah_upload' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'ktp_upload' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'transkrip_nilai_upload' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ];
    }
}
