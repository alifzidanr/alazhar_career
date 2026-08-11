<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Loker;
use App\Models\Pelamar;
use App\Models\StatusPelamar;
use App\Models\TahapRekrutmen;
use Illuminate\Support\Carbon;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $totalPelamar = Pelamar::count();
        $totalLokerAktif = Loker::dibuka()->count();
        $totalLoker = Loker::count();

        $sejakTanggal = Carbon::today()->subDays(29);
        $pelamarBaru30Hari = Pelamar::where('tanggal_apply', '>=', $sejakTanggal)->count();

        $diterimaCount = Pelamar::whereIn('id_status_pelamar', [StatusPelamar::DITERIMA, StatusPelamar::MIGRATED])->count();
        $rasioDiterima = $totalPelamar > 0 ? round($diterimaCount / $totalPelamar * 100, 1) : 0;

        $statusCounts = Pelamar::selectRaw('id_status_pelamar, count(*) as total')
            ->groupBy('id_status_pelamar')
            ->pluck('total', 'id_status_pelamar');

        $statusBreakdown = StatusPelamar::orderBy('id_status_pelamar')->get()
            ->map(fn ($s) => [
                'label' => ucfirst($s->status_pelamar),
                'total' => $statusCounts[$s->id_status_pelamar] ?? 0,
                'variant' => match ($s->id_status_pelamar) {
                    StatusPelamar::LOLOS, StatusPelamar::DITERIMA, StatusPelamar::MIGRATED => 'success',
                    StatusPelamar::DICADANGKAN => 'warning',
                    StatusPelamar::TIDAK_LOLOS, StatusPelamar::DITOLAK => 'destructive',
                    StatusPelamar::SCREENING, StatusPelamar::ONGOING => 'info',
                    default => 'muted',
                },
            ])
            ->filter(fn ($row) => $row['total'] > 0)
            ->values();

        $tahapCounts = Pelamar::selectRaw('id_tahap_rekrutmen, count(*) as total')
            ->groupBy('id_tahap_rekrutmen')
            ->pluck('total', 'id_tahap_rekrutmen');

        $tahapBreakdown = TahapRekrutmen::orderBy('id_tahap_rekrutmen')->get()
            ->map(fn ($t) => [
                'label' => $t->tahap_rekrutmen,
                'total' => $tahapCounts[$t->id_tahap_rekrutmen] ?? 0,
            ]);

        $topLoker = Loker::withCount('pelamar')
            ->orderByDesc('pelamar_count')
            ->take(5)
            ->get();

        $hitunganPerTanggal = Pelamar::selectRaw('tanggal_apply, count(*) as total')
            ->where('tanggal_apply', '>=', $sejakTanggal)
            ->groupBy('tanggal_apply')
            ->get()
            ->mapWithKeys(fn ($row) => [Carbon::parse($row->tanggal_apply)->toDateString() => (int) $row->total]);

        $tren = collect(range(0, 29))
            ->map(function (int $i) use ($sejakTanggal, $hitunganPerTanggal) {
                $tanggal = $sejakTanggal->copy()->addDays($i);

                return ['tanggal' => $tanggal, 'total' => $hitunganPerTanggal[$tanggal->toDateString()] ?? 0];
            })
            ->keyBy(fn ($row) => $row['tanggal']->toDateString());

        $trenMax = max(1, $tren->max('total'));

        return view('admin.dashboard', compact(
            'totalPelamar',
            'totalLokerAktif',
            'totalLoker',
            'pelamarBaru30Hari',
            'rasioDiterima',
            'statusBreakdown',
            'tahapBreakdown',
            'topLoker',
            'tren',
            'trenMax',
        ));
    }
}
