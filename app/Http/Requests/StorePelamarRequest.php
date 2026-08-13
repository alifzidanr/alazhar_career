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
            'nik' => [
                'required',
                'digits:16',
                Rule::unique('pelamar', 'nik')
                    ->where(fn ($query) => $query->where('id_loker', $this->route('loker')?->id_loker)),
            ],
            'tanggal_lahir' => ['required', 'date', 'before:today', function ($attribute, $value, $fail) {
                $age = \Carbon\Carbon::parse($value)->age;
                if ($age < 18) {
                    $fail('Maaf, pelamar dengan usia kurang dari 18 tahun tidak dapat melanjutkan proses pendaftaran ini.');
                } elseif ($age > 35) {
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
            'kerja_al_azhar_periode' => ['nullable', 'date_format:Y-m', Rule::requiredIf(fn () => $this->input('pernah_bekerja_di_al_azhar') === 'Ya')],
            'jenis_kepegawaian_al_azhar_sebelumnya' => [
                'nullable',
                'in:Pegawai Honor,Pegawai Tetap,Pegawai Inval,Pegawai Ekskul,Lain-lain',
                Rule::requiredIf(fn () => $this->input('pernah_bekerja_di_al_azhar') === 'Ya'),
            ],
            'jenis_kepegawaian_al_azhar_lainnya' => [
                'nullable', 'string', 'max:255',
                Rule::requiredIf(fn () => $this->input('pernah_bekerja_di_al_azhar') === 'Ya' && $this->input('jenis_kepegawaian_al_azhar_sebelumnya') === 'Lain-lain'),
            ],

            // Step 2: Pendidikan
            'id_pendidikan_terakhir' => ['required', 'exists:pendidikan_terakhir,id_pendidikan_terakhir'],
            'institusi' => ['required', 'string', 'max:150'],
            // Prodi/kategori PT/akreditasi only apply to tertiary education (D3/S1/S2/S3), not SMP/SMA.
            'program_studi' => ['nullable', 'string', 'max:150', Rule::requiredIf(fn () => ! $this->isSekolahMenengah())],
            'kategori_perguruan_tinggi' => ['nullable', 'in:Perguruan Tinggi Negeri,Perguruan Tinggi Swasta,Lain-lain', Rule::requiredIf(fn () => ! $this->isSekolahMenengah())],
            'akreditasi' => ['nullable', 'in:A,B,C', Rule::requiredIf(fn () => ! $this->isSekolahMenengah())],
            'tahun_lulus' => ['required', 'integer', 'between:2012,2026'],
            // IPK S1 is also required for S2/S3 applicants (an S2/S3 always has a prior S1 IPK on record).
            'ipk_s1' => ['nullable', 'numeric', 'between:0,4', Rule::requiredIf(fn () => in_array($this->pendidikanLabel(), ['S1', 'S2', 'S3'], true))],
            // IPK S2 is also required for S3 applicants (an S3 always has a prior S2 IPK on record).
            'ipk_s2' => ['nullable', 'numeric', 'between:0,4', Rule::requiredIf(fn () => in_array($this->pendidikanLabel(), ['S2', 'S3'], true))],
            'ipk_s3' => ['nullable', 'numeric', 'between:0,4', Rule::requiredIf(fn () => $this->pendidikanLabel() === 'S3')],
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
            'bersedia_ditempatkan' => ['required', 'accepted'],
        ];
    }

    public function messages(): array
    {
        return [
            'nik.unique' => 'NIK ini sudah pernah digunakan untuk melamar lowongan ini.',
            'bersedia_ditempatkan.required' => 'Anda harus menyetujui pernyataan kesediaan penempatan sebelum mengirim lamaran.',
            'bersedia_ditempatkan.accepted' => 'Anda harus menyetujui pernyataan kesediaan penempatan sebelum mengirim lamaran.',
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

    private function isSekolahMenengah(): bool
    {
        return in_array($this->pendidikanLabel(), ['SMP', 'SMA'], true);
    }

    private function lokerJenjangIs(string $nama): bool
    {
        $loker = $this->route('loker');

        return $loker?->jenjang?->nama_jenjang === $nama;
    }
}
