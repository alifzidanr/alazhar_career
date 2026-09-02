<?php

namespace App\Http\Controllers\Admin;

use App\Exports\PelamarExport;
use App\Http\Controllers\Controller;
use App\Models\Loker;
use App\Models\Orientasi;
use App\Models\Pelamar;
use App\Models\RiwayatTahapPelamar;
use App\Models\StatusPelamar;
use App\Models\TahapRekrutmen;
use App\Models\TesTulis;
use App\Models\TugasSementara;
use App\Models\UnitKerja;
use App\Models\Wawancara;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\View\View;
use Maatwebsite\Excel\Facades\Excel;
use setasign\Fpdi\Fpdi;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class PelamarController extends Controller
{
    public function index(Request $request): View
    {
        $tahapAktif = $request->integer('tahap', 0);
        $lokerAktif = $request->integer('loker', 0);
        $kategoriAktif = (string) $request->query('kategori', '');

        $query = $this->filterPelamarQuery(
            Pelamar::with(['loker', 'statusPelamar', 'tahapRekrutmen', 'tesTulis', 'wawancara']),
            $tahapAktif,
            $lokerAktif,
            $kategoriAktif
        );

        $pelamarList = $query->orderByDesc('tanggal_apply')->get();

        $tahapOptions = TahapRekrutmen::orderBy('id_tahap_rekrutmen')->get();

        $countsQuery = $this->filterPelamarQuery(
            Pelamar::selectRaw('id_tahap_rekrutmen, count(*) as total')->groupBy('id_tahap_rekrutmen'),
            0,
            $lokerAktif,
            $kategoriAktif
        );

        $counts = $countsQuery->pluck('total', 'id_tahap_rekrutmen');

        $totalSemua = $counts->sum();

        $lokerAktifModel = $lokerAktif !== 0 ? Loker::find($lokerAktif) : null;

        $tesTulisAktif = $tahapAktif === TahapRekrutmen::TES_TULIS;
        $wawancaraAktif = $tahapAktif === TahapRekrutmen::WAWANCARA;

        return view('admin.pelamar.index', compact('pelamarList', 'tahapOptions', 'tahapAktif', 'lokerAktif', 'lokerAktifModel', 'kategoriAktif', 'counts', 'totalSemua', 'tesTulisAktif', 'wawancaraAktif'));
    }

    /** Applies the same tahap/loker/kategori filters used by the pelamar list to any base query. */
    private function filterPelamarQuery(Builder $query, int $tahapAktif, int $lokerAktif, string $kategoriAktif): Builder
    {
        if ($tahapAktif !== 0) {
            $query->where('id_tahap_rekrutmen', $tahapAktif);
        }

        if ($lokerAktif !== 0) {
            $query->where('id_loker', $lokerAktif);
        }

        if ($kategoriAktif !== '') {
            $query->where(function ($q) use ($kategoriAktif) {
                $q->where('kategori_perguruan_tinggi_d3', $kategoriAktif)
                    ->orWhere('kategori_perguruan_tinggi_s1', $kategoriAktif)
                    ->orWhere('kategori_perguruan_tinggi_s2', $kategoriAktif)
                    ->orWhere('kategori_perguruan_tinggi_s3', $kategoriAktif);
            });
        }

        return $query;
    }

    public function export(Request $request): BinaryFileResponse
    {
        $tahapAktif = $request->integer('tahap', 0);
        $lokerAktif = $request->integer('loker', 0);
        $kategoriAktif = (string) $request->query('kategori', '');

        $pelamarList = $this->filterPelamarQuery(
            Pelamar::with(['pendidikanTerakhir', 'loker']),
            $tahapAktif,
            $lokerAktif,
            $kategoriAktif
        )->orderByDesc('tanggal_apply')->get();

        $lokerAktifModel = $lokerAktif !== 0 ? Loker::find($lokerAktif) : null;
        $filenameSuffix = $lokerAktifModel ? Str::slug($lokerAktifModel->judul_loker) : 'semua-loker';

        return Excel::download(
            new PelamarExport($pelamarList),
            "pelamar-{$filenameSuffix}-".now()->format('Ymd_His').'.xlsx'
        );
    }

    public function show(Pelamar $pelamar): View
    {
        $pelamar->load([
            'loker',
            'pendidikanTerakhir',
            'statusPelamar',
            'tahapRekrutmen',
            'tahapRekrutmenSebelumnya',
            'riwayat.tahapRekrutmen',
            'riwayat.statusPelamar',
            'logNotifikasi',
            'cadanganDari',
            'kandidatCadangan',
            'tesTulis',
            'wawancara',
            'orientasi.unitKerja',
            'tugasSementara',
        ]);

        $statusOptions = StatusPelamar::orderBy('id_status_pelamar')->get();
        $unitKerjaList = UnitKerja::orderBy('nama_unit')->get();

        // "Sampai tahap apa" only offers the first 4 stages (Tugas Sementara, Terima SK,
        // and Migrasi Data are excluded since a past applicant wouldn't self-report those).
        $tahapList = TahapRekrutmen::whereIn('id_tahap_rekrutmen', [
            TahapRekrutmen::SELEKSI_BERKAS,
            TahapRekrutmen::TES_TULIS,
            TahapRekrutmen::WAWANCARA,
            TahapRekrutmen::ORIENTASI,
        ])->orderBy('id_tahap_rekrutmen')->get();

        // Same ordering as the "Manajemen Pelamar" list, scoped to this pelamar's loker,
        // so Sebelumnya/Berikutnya steps through applicants in the order they're listed there.
        // Tidak Lolos applicants are kept in their own separate navigation lane so they
        // don't get mixed into the active-applicant flow (and vice versa).
        $isTidakLolos = $pelamar->id_status_pelamar === StatusPelamar::TIDAK_LOLOS;

        $lokerPelamarIds = Pelamar::where('id_loker', $pelamar->id_loker)
            ->where('id_status_pelamar', $isTidakLolos ? '=' : '!=', StatusPelamar::TIDAK_LOLOS)
            ->orderByDesc('tanggal_apply')
            ->orderByDesc('id_pelamar')
            ->pluck('id_pelamar');

        $currentIndex = $lokerPelamarIds->search($pelamar->id_pelamar);

        $prevPelamarId = $currentIndex !== false ? $lokerPelamarIds->get($currentIndex - 1) : null;
        $nextPelamarId = $currentIndex !== false ? $lokerPelamarIds->get($currentIndex + 1) : null;

        return view('admin.pelamar.show', compact('pelamar', 'statusOptions', 'unitKerjaList', 'tahapList', 'prevPelamarId', 'nextPelamarId'));
    }

    public function updateData(Request $request, Pelamar $pelamar): RedirectResponse
    {
        $data = $request->validate([
            'nama' => ['required', 'string', 'max:150'],
            'nik' => [
                'required',
                'digits:16',
                Rule::unique('pelamar', 'nik')
                    ->where(fn ($query) => $query->where('id_loker', $pelamar->id_loker))
                    ->ignore($pelamar->id_pelamar, 'id_pelamar'),
            ],
            'tanggal_lahir' => ['required', 'date', 'before:today'],
            'jenis_kelamin' => ['required', 'in:L,P'],
            'gelar' => ['nullable', 'string', 'max:50'],
            'no_hp' => ['required', 'string', 'max:20'],
            'email' => ['required', 'email', 'max:150'],
            'alamat' => ['required', 'string', 'max:1000'],
            'pernah_rekrutmen_sebelumnya' => ['required', 'in:Ya,Tidak'],
            'bulan_rekrutmen_sebelumnya' => ['nullable', 'integer', 'between:1,12', Rule::requiredIf(fn () => $request->input('pernah_rekrutmen_sebelumnya') === 'Ya')],
            'tahun_rekrutmen_sebelumnya' => ['nullable', 'integer', 'between:2020,2030', Rule::requiredIf(fn () => $request->input('pernah_rekrutmen_sebelumnya') === 'Ya')],
            'id_tahap_rekrutmen_sebelumnya' => [
                'nullable',
                Rule::in([TahapRekrutmen::SELEKSI_BERKAS, TahapRekrutmen::TES_TULIS, TahapRekrutmen::WAWANCARA, TahapRekrutmen::ORIENTASI]),
                Rule::requiredIf(fn () => $request->input('pernah_rekrutmen_sebelumnya') === 'Ya'),
            ],
            'pernah_bekerja_di_al_azhar' => ['required', 'in:Ya,Tidak'],
            'lokasi_kerja_al_azhar_sebelumnya' => ['nullable', 'string', 'max:255', Rule::requiredIf(fn () => $request->input('pernah_bekerja_di_al_azhar') === 'Ya')],
            'bulan_kerja_al_azhar_sebelumnya' => ['nullable', 'integer', 'between:1,12', Rule::requiredIf(fn () => $request->input('pernah_bekerja_di_al_azhar') === 'Ya')],
            'tahun_kerja_al_azhar_sebelumnya' => ['nullable', 'integer', 'between:2000,2030', Rule::requiredIf(fn () => $request->input('pernah_bekerja_di_al_azhar') === 'Ya')],
            'jenis_kepegawaian_al_azhar_sebelumnya' => [
                'nullable',
                'in:Pegawai Honor,Pegawai Tetap,Pegawai Inval,Pegawai Ekskul,Lain-lain',
                Rule::requiredIf(fn () => $request->input('pernah_bekerja_di_al_azhar') === 'Ya'),
            ],
            'jenis_kepegawaian_al_azhar_lainnya' => [
                'nullable', 'string', 'max:255',
                Rule::requiredIf(fn () => $request->input('pernah_bekerja_di_al_azhar') === 'Ya' && $request->input('jenis_kepegawaian_al_azhar_sebelumnya') === 'Lain-lain'),
            ],
        ]);

        if ($data['pernah_rekrutmen_sebelumnya'] === 'Tidak') {
            $data['bulan_rekrutmen_sebelumnya'] = null;
            $data['tahun_rekrutmen_sebelumnya'] = null;
            $data['id_tahap_rekrutmen_sebelumnya'] = null;
        }

        if ($data['pernah_bekerja_di_al_azhar'] === 'Tidak') {
            $data['lokasi_kerja_al_azhar_sebelumnya'] = null;
            $data['bulan_kerja_al_azhar_sebelumnya'] = null;
            $data['tahun_kerja_al_azhar_sebelumnya'] = null;
            $data['jenis_kepegawaian_al_azhar_sebelumnya'] = null;
            $data['jenis_kepegawaian_al_azhar_lainnya'] = null;
        } elseif ($data['jenis_kepegawaian_al_azhar_sebelumnya'] !== 'Lain-lain') {
            $data['jenis_kepegawaian_al_azhar_lainnya'] = null;
        }

        $pelamar->update($data);

        return back()->with('status', 'Data Pelamar berhasil disimpan.');
    }

    /** Merge every uploaded berkas (PDFs and images) into a single downloadable PDF. */
    public function downloadBerkas(Pelamar $pelamar): Response
    {
        $berkasList = $pelamar->berkasList();

        if ($berkasList->isEmpty()) {
            return back()->withErrors(['berkas' => 'Pelamar ini tidak memiliki berkas yang bisa diunduh.']);
        }

        $pdf = new Fpdi();
        $pdf->SetTitle('Berkas '.$pelamar->namaLengkap());
        $pdf->SetAutoPageBreak(false);

        foreach ($berkasList as $berkas) {
            $path = Storage::disk('public')->path($berkas['path']);

            if (! is_file($path)) {
                continue;
            }

            $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));

            try {
                if ($ext === 'pdf') {
                    $pageCount = $pdf->setSourceFile($path);

                    for ($i = 1; $i <= $pageCount; $i++) {
                        $templateId = $pdf->importPage($i);
                        $size = $pdf->getTemplateSize($templateId);
                        $pdf->AddPage($size['orientation'], [$size['width'], $size['height']]);
                        $pdf->useTemplate($templateId);
                        $this->stampBerkasLabel($pdf, $berkas['label']);
                    }
                } else {
                    $dimensions = getimagesize($path);

                    if (! $dimensions) {
                        throw new \RuntimeException('Berkas gambar tidak dapat dibaca.');
                    }

                    [$width, $height] = $dimensions;
                    $pdf->AddPage($width > $height ? 'L' : 'P', 'A4');

                    $margin = 10;
                    $maxWidth = $pdf->GetPageWidth() - $margin * 2;
                    $maxHeight = $pdf->GetPageHeight() - $margin * 2;
                    $ratio = min($maxWidth / $width, $maxHeight / $height);
                    $w = $width * $ratio;
                    $h = $height * $ratio;

                    $pdf->Image($path, ($pdf->GetPageWidth() - $w) / 2, ($pdf->GetPageHeight() - $h) / 2, $w, $h);
                    $this->stampBerkasLabel($pdf, $berkas['label']);
                }
            } catch (\Throwable $e) {
                $this->addUnreadableBerkasNotice($pdf, $berkas['label']);
            }
        }

        $filename = 'Berkas-'.Str::slug($pelamar->namaLengkap()).'.pdf';

        return response($pdf->Output('S'), 200, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'attachment; filename="'.$filename.'"',
        ]);
    }

    /** Adds a placeholder page for a berkas that couldn't be merged (e.g. a PDF with a corrupt/non-standard structure). */
    private function addUnreadableBerkasNotice(Fpdi $pdf, string $label): void
    {
        $pdf->AddPage('P', 'A4');
        $pdf->SetFont('Helvetica', 'B', 12);
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetXY(15, 20);
        $pdf->Cell(0, 8, $label);
        $pdf->SetFont('Helvetica', '', 10);
        $pdf->SetXY(15, 32);
        $pdf->MultiCell(180, 6, 'Berkas ini tidak dapat digabungkan secara otomatis karena format PDF/gambarnya rusak atau tidak standar. Unduh berkas ini secara terpisah dari halaman detail pelamar.');
    }

    private function stampBerkasLabel(Fpdi $pdf, string $label): void
    {
        $pdf->SetFont('Helvetica', '', 8);
        $pdf->SetTextColor(120, 120, 120);
        $pdf->SetXY(5, $pdf->GetPageHeight() - 8);
        $pdf->Cell(0, 5, $label);
    }

    public function updateStatus(Request $request, Pelamar $pelamar): RedirectResponse
    {
        $data = $request->validate([
            'id_status_pelamar' => ['required', 'exists:status_pelamar,id_status_pelamar'],
            'catatan' => ['nullable', 'string', 'max:1000'],
            'id_pelamar_cadangan_dari' => ['nullable', 'exists:pelamar,id_pelamar'],
        ]);

        DB::transaction(function () use ($data, $pelamar, $request) {
            $pelamar->id_status_pelamar = $data['id_status_pelamar'];

            if ((int) $data['id_status_pelamar'] === StatusPelamar::DICADANGKAN) {
                $pelamar->id_pelamar_cadangan_dari = $data['id_pelamar_cadangan_dari'] ?? null;
            }

            if ($request->filled('catatan')) {
                $pelamar->catatan = $data['catatan'];
            }

            $pelamar->save();

            RiwayatTahapPelamar::create([
                'id_pelamar' => $pelamar->id_pelamar,
                'id_tahap_rekrutmen' => $pelamar->id_tahap_rekrutmen,
                'id_status_pelamar' => $pelamar->id_status_pelamar,
                'catatan' => $data['catatan'] ?? null,
                'created_by' => auth()->user()->name,
            ]);
        });

        return back()->with('status', 'Status pelamar berhasil diperbarui.');
    }

    public function bulkUpdateStatus(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:pelamar,id_pelamar'],
            'id_status_pelamar' => ['required', 'exists:status_pelamar,id_status_pelamar'],
        ]);

        $count = DB::transaction(function () use ($data) {
            $pelamarList = Pelamar::whereIn('id_pelamar', $data['ids'])->get();

            foreach ($pelamarList as $pelamar) {
                $pelamar->id_status_pelamar = $data['id_status_pelamar'];
                $pelamar->save();

                RiwayatTahapPelamar::create([
                    'id_pelamar' => $pelamar->id_pelamar,
                    'id_tahap_rekrutmen' => $pelamar->id_tahap_rekrutmen,
                    'id_status_pelamar' => $pelamar->id_status_pelamar,
                    'catatan' => 'Status diperbarui secara massal.',
                    'created_by' => auth()->user()->name,
                ]);
            }

            return $pelamarList->count();
        });

        return back()->with('status', $count.' pelamar berhasil diperbarui statusnya.');
    }

    public function bulkAdvanceTahap(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:pelamar,id_pelamar'],
        ]);

        [$count, $skipped] = DB::transaction(function () use ($data) {
            $pelamarList = Pelamar::whereIn('id_pelamar', $data['ids'])->get();
            $count = 0;
            $skipped = 0;

            foreach ($pelamarList as $pelamar) {
                if (in_array($pelamar->id_status_pelamar, [StatusPelamar::MUNDUR, StatusPelamar::DICADANGKAN], true)
                    || $pelamar->id_tahap_rekrutmen >= TahapRekrutmen::MIGRASI_DATA) {
                    $skipped++;

                    continue;
                }

                $pelamar->id_tahap_rekrutmen += 1;
                $pelamar->id_status_pelamar = TahapRekrutmen::statusAwalUntuk($pelamar->id_tahap_rekrutmen);
                $pelamar->save();

                RiwayatTahapPelamar::create([
                    'id_pelamar' => $pelamar->id_pelamar,
                    'id_tahap_rekrutmen' => $pelamar->id_tahap_rekrutmen,
                    'id_status_pelamar' => $pelamar->id_status_pelamar,
                    'catatan' => 'Dilanjutkan ke tahap "'.$pelamar->tahapRekrutmen()->first()->tahap_rekrutmen.'" secara massal.',
                    'created_by' => auth()->user()->name,
                ]);

                $count++;
            }

            return [$count, $skipped];
        });

        $message = $count.' pelamar berhasil dilanjutkan ke tahap berikutnya.';
        if ($skipped > 0) {
            $message .= ' '.$skipped.' pelamar dilewati karena sudah di tahap akhir atau berstatus mundur/dicadangkan.';
        }

        return back()->with('status', $message);
    }

    public function bulkRegressTahap(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'ids' => ['required', 'array', 'min:1'],
            'ids.*' => ['integer', 'exists:pelamar,id_pelamar'],
        ]);

        [$count, $skipped] = DB::transaction(function () use ($data) {
            $pelamarList = Pelamar::whereIn('id_pelamar', $data['ids'])->get();
            $count = 0;
            $skipped = 0;

            foreach ($pelamarList as $pelamar) {
                if ($pelamar->id_tahap_rekrutmen <= TahapRekrutmen::SELEKSI_BERKAS) {
                    $skipped++;

                    continue;
                }

                $pelamar->id_tahap_rekrutmen -= 1;
                $pelamar->id_status_pelamar = TahapRekrutmen::statusAwalUntuk($pelamar->id_tahap_rekrutmen);
                $pelamar->save();

                RiwayatTahapPelamar::create([
                    'id_pelamar' => $pelamar->id_pelamar,
                    'id_tahap_rekrutmen' => $pelamar->id_tahap_rekrutmen,
                    'id_status_pelamar' => $pelamar->id_status_pelamar,
                    'catatan' => 'Dimundurkan ke tahap "'.$pelamar->tahapRekrutmen()->first()->tahap_rekrutmen.'" secara massal.',
                    'created_by' => auth()->user()->name,
                ]);

                $count++;
            }

            return [$count, $skipped];
        });

        $message = $count.' pelamar berhasil dimundurkan ke tahap sebelumnya.';
        if ($skipped > 0) {
            $message .= ' '.$skipped.' pelamar dilewati karena sudah di tahap paling awal.';
        }

        return back()->with('status', $message);
    }

    public function advanceTahap(Pelamar $pelamar): RedirectResponse
    {
        if (in_array($pelamar->id_status_pelamar, [StatusPelamar::MUNDUR, StatusPelamar::DICADANGKAN], true)) {
            return back()->withErrors(['tahap' => 'Pelamar berstatus "mundur" atau "dicadangkan" tidak dapat dilanjutkan ke tahap berikutnya.']);
        }

        if ($pelamar->id_tahap_rekrutmen >= TahapRekrutmen::MIGRASI_DATA) {
            return back()->withErrors(['tahap' => 'Pelamar sudah berada di tahap akhir (Migrasi Data).']);
        }

        DB::transaction(function () use ($pelamar) {
            $pelamar->id_tahap_rekrutmen += 1;
            $pelamar->id_status_pelamar = TahapRekrutmen::statusAwalUntuk($pelamar->id_tahap_rekrutmen);
            $pelamar->save();

            RiwayatTahapPelamar::create([
                'id_pelamar' => $pelamar->id_pelamar,
                'id_tahap_rekrutmen' => $pelamar->id_tahap_rekrutmen,
                'id_status_pelamar' => $pelamar->id_status_pelamar,
                'catatan' => 'Dilanjutkan ke tahap "'.$pelamar->tahapRekrutmen()->first()->tahap_rekrutmen.'".',
                'created_by' => auth()->user()->name,
            ]);
        });

        return back()->with('status', 'Pelamar berhasil dilanjutkan ke tahap berikutnya.');
    }

    public function regressTahap(Pelamar $pelamar): RedirectResponse
    {
        if ($pelamar->id_tahap_rekrutmen <= TahapRekrutmen::SELEKSI_BERKAS) {
            return back()->withErrors(['tahap' => 'Pelamar sudah berada di tahap paling awal (Seleksi Berkas).']);
        }

        DB::transaction(function () use ($pelamar) {
            $pelamar->id_tahap_rekrutmen -= 1;
            $pelamar->id_status_pelamar = TahapRekrutmen::statusAwalUntuk($pelamar->id_tahap_rekrutmen);
            $pelamar->save();

            RiwayatTahapPelamar::create([
                'id_pelamar' => $pelamar->id_pelamar,
                'id_tahap_rekrutmen' => $pelamar->id_tahap_rekrutmen,
                'id_status_pelamar' => $pelamar->id_status_pelamar,
                'catatan' => 'Dimundurkan ke tahap "'.$pelamar->tahapRekrutmen()->first()->tahap_rekrutmen.'".',
                'created_by' => auth()->user()->name,
            ]);
        });

        return back()->with('status', 'Pelamar berhasil dimundurkan ke tahap sebelumnya.');
    }

    public function updateCatatan(Request $request, Pelamar $pelamar): RedirectResponse
    {
        $data = $request->validate([
            'catatan' => ['nullable', 'string', 'max:1000'],
        ]);

        DB::transaction(function () use ($data, $pelamar) {
            $pelamar->catatan = $data['catatan'] ?? null;
            $pelamar->save();

            RiwayatTahapPelamar::create([
                'id_pelamar' => $pelamar->id_pelamar,
                'id_tahap_rekrutmen' => $pelamar->id_tahap_rekrutmen,
                'id_status_pelamar' => $pelamar->id_status_pelamar,
                'catatan' => $data['catatan'] ?? '(catatan dikosongkan)',
                'created_by' => auth()->user()->name,
            ]);
        });

        return back()->with('status', 'Catatan berhasil disimpan.');
    }

    public function updateTesTulis(Request $request, Pelamar $pelamar): RedirectResponse
    {
        $data = $request->validate([
            'nilai_tes_agama_umum' => ['nullable', 'numeric', 'between:0,100'],
            'nilai_tes_bidang_studi' => ['nullable', 'numeric', 'between:0,100'],
            'nilai_tes_inggris_umum' => ['nullable', 'numeric', 'between:0,100'],
            'tanggal_pelaksanaan' => ['nullable', 'date'],
        ]);

        TesTulis::updateOrCreate(['id_pelamar' => $pelamar->id_pelamar], $data);

        return back()->with('status', 'Data Tes Tulis berhasil disimpan.');
    }

    public function updateWawancara(Request $request, Pelamar $pelamar): RedirectResponse
    {
        $data = $request->validate([
            'nilai_wawancara_agama' => ['nullable', 'numeric', 'between:0,100'],
            'rekomendasi_wawancara_agama' => ['nullable', 'string', 'max:1000'],
            'nilai_praktik_micro_teaching' => ['nullable', 'numeric', 'between:0,100'],
            'rekomendasi_praktik_micro_teaching' => ['nullable', 'string', 'max:1000'],
            'nilai_wawancara_umum' => ['nullable', 'numeric', 'between:0,100'],
            'rekomendasi_wawancara_umum' => ['nullable', 'string', 'max:1000'],
            'tanggal_pelaksanaan' => ['nullable', 'date'],
        ]);

        Wawancara::updateOrCreate(['id_pelamar' => $pelamar->id_pelamar], $data);

        return back()->with('status', 'Data Wawancara berhasil disimpan.');
    }

    public function updateOrientasi(Request $request, Pelamar $pelamar): RedirectResponse
    {
        $data = $request->validate([
            'id_unit_kerja' => ['nullable', 'exists:unit_kerja,id_unit_kerja'],
            'uang_makan' => ['nullable', 'integer', 'min:0'],
            'uang_transport' => ['nullable', 'integer', 'min:0'],
            'tanggal_mulai' => ['nullable', 'date'],
            'tanggal_selesai' => ['nullable', 'date', 'after_or_equal:tanggal_mulai'],
            'sk_orientasi_upload' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);

        $orientasi = Orientasi::firstOrNew(['id_pelamar' => $pelamar->id_pelamar]);
        $orientasi->fill(collect($data)->except('sk_orientasi_upload')->all());

        if ($request->hasFile('sk_orientasi_upload')) {
            $orientasi->sk_orientasi_upload = $request->file('sk_orientasi_upload')->store('pelamar/sk_orientasi', 'public');
        }

        $orientasi->save();

        return back()->with('status', 'Data Orientasi berhasil disimpan.');
    }

    public function updateTugasSementara(Request $request, Pelamar $pelamar): RedirectResponse
    {
        $request->validate([
            'sk_tugas_sementara_upload' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
            'hasil_tes_kesehatan_upload' => ['nullable', 'file', 'mimes:pdf', 'max:5120'],
        ]);

        $tugasSementara = TugasSementara::firstOrNew(['id_pelamar' => $pelamar->id_pelamar]);

        if ($request->hasFile('sk_tugas_sementara_upload')) {
            $tugasSementara->sk_tugas_sementara_upload = $request->file('sk_tugas_sementara_upload')->store('pelamar/sk_tugas_sementara', 'public');
        }

        if ($request->hasFile('hasil_tes_kesehatan_upload')) {
            $tugasSementara->hasil_tes_kesehatan_upload = $request->file('hasil_tes_kesehatan_upload')->store('pelamar/hasil_tes_kesehatan', 'public');
        }

        $tugasSementara->save();

        return back()->with('status', 'Data Tugas Sementara berhasil disimpan.');
    }
}
