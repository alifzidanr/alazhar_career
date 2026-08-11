<?php

namespace App\Http\Controllers\Public;

use App\Http\Controllers\Controller;
use App\Http\Requests\StorePelamarRequest;
use App\Models\Loker;
use App\Models\Pelamar;
use App\Models\RiwayatTahapPelamar;
use App\Models\StatusPelamar;
use App\Models\TahapRekrutmen;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\DB;

class LamaranController extends Controller
{
    public function store(StorePelamarRequest $request, Loker $loker): RedirectResponse
    {
        if ($loker->status_loker !== 'dibuka') {
            return back()->withErrors(['loker' => 'Lowongan ini sudah ditutup dan tidak menerima lamaran baru.']);
        }

        $data = $request->validated();

        // The "Kapan" month picker submits a single "YYYY-MM" value; split it
        // into the bulan/tahun columns the rest of the app already relies on.
        [$tahunKerjaAlAzhar, $bulanKerjaAlAzhar] = isset($data['kerja_al_azhar_periode'])
            ? array_map('intval', explode('-', $data['kerja_al_azhar_periode']))
            : [null, null];

        $pelamar = DB::transaction(function () use ($data, $loker, $request, $bulanKerjaAlAzhar, $tahunKerjaAlAzhar) {
            $pelamar = Pelamar::create([
                'id_loker' => $loker->id_loker,
                'nama' => $data['nama'],
                'nik' => $data['nik'],
                'tanggal_lahir' => $data['tanggal_lahir'],
                'jenis_kelamin' => $data['jenis_kelamin'],
                'gelar' => $data['gelar'] ?? null,
                'no_hp' => $data['no_hp'],
                'email' => $data['email'],
                'alamat' => $data['alamat'],
                'pernah_rekrutmen_sebelumnya' => $data['pernah_rekrutmen_sebelumnya'],
                'bulan_rekrutmen_sebelumnya' => $data['bulan_rekrutmen_sebelumnya'] ?? null,
                'tahun_rekrutmen_sebelumnya' => $data['tahun_rekrutmen_sebelumnya'] ?? null,
                'id_tahap_rekrutmen_sebelumnya' => $data['id_tahap_rekrutmen_sebelumnya'] ?? null,
                'pernah_bekerja_di_al_azhar' => $data['pernah_bekerja_di_al_azhar'],
                'lokasi_kerja_al_azhar_sebelumnya' => $data['lokasi_kerja_al_azhar_sebelumnya'] ?? null,
                'bulan_kerja_al_azhar_sebelumnya' => $bulanKerjaAlAzhar,
                'tahun_kerja_al_azhar_sebelumnya' => $tahunKerjaAlAzhar,
                'jenis_kepegawaian_al_azhar_sebelumnya' => $data['jenis_kepegawaian_al_azhar_sebelumnya'] ?? null,
                'jenis_kepegawaian_al_azhar_lainnya' => $data['jenis_kepegawaian_al_azhar_lainnya'] ?? null,
                'id_pendidikan_terakhir' => $data['id_pendidikan_terakhir'],
                'institusi' => $data['institusi'],
                'program_studi' => $data['program_studi'] ?? null,
                'kategori_perguruan_tinggi' => $data['kategori_perguruan_tinggi'] ?? null,
                'akreditasi' => $data['akreditasi'] ?? null,
                'tahun_lulus' => $data['tahun_lulus'],
                'ipk_s1' => $data['ipk_s1'] ?? null,
                'ipk_s2' => $data['ipk_s2'] ?? null,
                'ipk_s3' => $data['ipk_s3'] ?? null,
                'ipk_d3' => $data['ipk_d3'] ?? null,
                'id_status_pelamar' => StatusPelamar::SCREENING,
                'id_tahap_rekrutmen' => TahapRekrutmen::SELEKSI_BERKAS,
                'tanggal_apply' => now()->toDateString(),
                'cv_upload' => $request->file('cv_upload')->store('pelamar/cv', 'public'),
                'ijazah_upload' => $request->file('ijazah_upload')->store('pelamar/ijazah', 'public'),
                'ktp_upload' => $request->file('ktp_upload')->store('pelamar/ktp', 'public'),
                'transkrip_nilai_upload' => $request->file('transkrip_nilai_upload')->store('pelamar/transkrip', 'public'),
                'pas_foto_upload' => $request->file('pas_foto_upload')->store('pelamar/pas_foto', 'public'),
                'surat_lamaran_upload' => $request->file('surat_lamaran_upload')->store('pelamar/surat_lamaran', 'public'),
                'sim_upload' => $request->hasFile('sim_upload')
                    ? $request->file('sim_upload')->store('pelamar/sim', 'public')
                    : null,
                'sertifikat_gada_pratama_upload' => $request->hasFile('sertifikat_gada_pratama_upload')
                    ? $request->file('sertifikat_gada_pratama_upload')->store('pelamar/sertifikat_gada_pratama', 'public')
                    : null,
                'sertifikat_tambahan_upload' => $request->hasFile('sertifikat_tambahan_upload')
                    ? $request->file('sertifikat_tambahan_upload')->store('pelamar/sertifikat_tambahan', 'public')
                    : null,
            ]);

            RiwayatTahapPelamar::create([
                'id_pelamar' => $pelamar->id_pelamar,
                'id_tahap_rekrutmen' => TahapRekrutmen::SELEKSI_BERKAS,
                'id_status_pelamar' => StatusPelamar::SCREENING,
                'catatan' => 'Lamaran diajukan oleh pelamar.',
                'created_by' => null,
            ]);

            return $pelamar;
        });

        return redirect()
            ->route('status.index')
            ->with('lamaran_sukses', 'Lamaran Anda untuk "'.$loker->judul_loker.'" berhasil dikirim. Tim HR akan menghubungi Anda melalui WhatsApp/email di '.$pelamar->no_hp.' / '.$pelamar->email.' untuk info selanjutnya.');
    }
}
