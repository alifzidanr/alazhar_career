<?php

namespace App\Http\Requests;

use App\Models\PendidikanTerakhir;
use App\Models\TahapRekrutmen;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

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
            'nik' => ['required', 'digits:16'],
            'tanggal_lahir' => ['required', 'date', 'before:today', function ($attribute, $value, $fail) {
                if (\Carbon\Carbon::parse($value)->age > 35) {
                    $fail('Maaf, pelamar dengan usia lebih dari 35 tahun tidak dapat melanjutkan proses pendaftaran ini.');
                }
            }],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'gelar' => ['nullable', 'string', 'max:50'],
            'no_hp' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:150'],
            'alamat' => ['required', 'string', 'max:1000'],
            'pernah_rekrutmen_sebelumnya' => ['required', 'in:Ya,Tidak'],
            'bulan_rekrutmen_sebelumnya' => ['nullable', 'integer', 'between:1,12', Rule::requiredIf(fn () => $this->input('pernah_rekrutmen_sebelumnya') === 'Ya')],
            'tahun_rekrutmen_sebelumnya' => ['nullable', 'integer', 'between:2020,2030', Rule::requiredIf(fn () => $this->input('pernah_rekrutmen_sebelumnya') === 'Ya')],
            // Only the first 4 stages are offered as "sampai tahap apa" options.
            'id_tahap_rekrutmen_sebelumnya' => [
                'nullable',
                Rule::in([TahapRekrutmen::SELEKSI_BERKAS, TahapRekrutmen::TES_TULIS, TahapRekrutmen::WAWANCARA, TahapRekrutmen::ORIENTASI]),
                Rule::requiredIf(fn () => $this->input('pernah_rekrutmen_sebelumnya') === 'Ya'),
            ],
            'pernah_bekerja_di_al_azhar' => ['required', 'in:Ya,Tidak'],
            'lokasi_kerja_al_azhar_sebelumnya' => ['nullable', 'string', 'max:255', Rule::requiredIf(fn () => $this->input('pernah_bekerja_di_al_azhar') === 'Ya')],

            // Step 2: Pendidikan
            'id_pendidikan_terakhir' => ['required', 'exists:pendidikan_terakhir,id_pendidikan_terakhir'],
            'institusi' => ['required', 'string', 'max:150'],
            'program_studi' => ['required', 'string', 'max:150'],
            'kategori_perguruan_tinggi' => ['required', 'in:Perguruan Tinggi Negeri,Perguruan Tinggi Swasta,Lain-lain'],
            'akreditasi' => ['required', 'in:A,B,C'],
            'tahun_lulus' => ['required', 'integer', 'between:2012,2026'],
            // IPK S1 is also required for S2 applicants (an S2 always has a prior S1 IPK on record).
            'ipk_s1' => ['nullable', 'numeric', 'between:0,4', Rule::requiredIf(fn () => in_array($this->pendidikanLabel(), ['S1', 'S2'], true))],
            'ipk_s2' => ['nullable', 'numeric', 'between:0,4', Rule::requiredIf(fn () => $this->pendidikanLabel() === 'S2')],
            'ipk_d3' => ['nullable', 'numeric', 'between:0,4', Rule::requiredIf(fn () => $this->pendidikanLabel() === 'D3')],

            // Step 3: Unggah Dokumen
            'cv_upload' => ['required', 'file', 'mimes:pdf', 'max:5120'],
            'ijazah_upload' => ['required', 'file', 'mimes:pdf', 'max:5120'],
            'ktp_upload' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
            'transkrip_nilai_upload' => ['required', 'file', 'mimes:pdf', 'max:5120'],
            'pas_foto_upload' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
            'surat_lamaran_upload' => ['required', 'file', 'mimes:pdf', 'max:5120'],
            // Only required when the loker itself calls for that jenjang (SIM for Driver, Gada Pratama for Satpam).
            'sim_upload' => [$this->lokerJenjangIs('Driver') ? 'required' : 'nullable', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
            'sertifikat_gada_pratama_upload' => [$this->lokerJenjangIs('Satpam') ? 'required' : 'nullable', 'file', 'mimes:pdf', 'max:5120'],
            'sertifikat_tambahan_upload' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ];
    }

    private ?string $cachedPendidikanLabel = null;

    private bool $pendidikanLabelResolved = false;

    private function pendidikanLabel(): ?string
    {
        if (! $this->pendidikanLabelResolved) {
            $this->cachedPendidikanLabel = PendidikanTerakhir::find($this->input('id_pendidikan_terakhir'))?->pendidikan_terakhir;
            $this->pendidikanLabelResolved = true;
        }

        return $this->cachedPendidikanLabel;
    }

    private function lokerJenjangIs(string $nama): bool
    {
        $loker = $this->route('loker');

        return $loker?->jenjang?->nama_jenjang === $nama;
    }
}
