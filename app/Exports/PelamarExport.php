<?php

namespace App\Exports;

use App\Models\Pelamar;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class PelamarExport implements FromCollection, ShouldAutoSize, WithHeadings, WithMapping
{
    public function __construct(private Collection $pelamarList) {}

    public function collection(): Collection
    {
        return $this->pelamarList;
    }

    public function headings(): array
    {
        return [
            'Nama',
            'Usia',
            'Tgl Lahir',
            'Alamat Domisili',
            'No HP/WA',
            'Pendidikan Terakhir',
            'Kategori Pendidikan S1',
            'Nama Perguruan Tinggi S1',
            'Jurusan/Prodi S1',
            'IPK S1',
            'Akreditasi S1',
        ];
    }

    public function map($pelamar): array
    {
        /** @var Pelamar $pelamar */
        $akreditasiLabel = ['A' => 'Unggul', 'B' => 'Baik Sekali', 'C' => 'Baik'][$pelamar->akreditasi] ?? null;

        return [
            $pelamar->namaLengkap(),
            $pelamar->usia(),
            optional($pelamar->tanggal_lahir)->format('d/m/Y'),
            $pelamar->alamat,
            $pelamar->no_hp,
            $pelamar->pendidikanTerakhir?->pendidikan_terakhir,
            $pelamar->kategori_perguruan_tinggi_s1,
            $pelamar->institusi_s1,
            $pelamar->program_studi_s1,
            $pelamar->ipk_s1 !== null ? number_format((float) $pelamar->ipk_s1, 2) : null,
            $pelamar->akreditasi ? $pelamar->akreditasi.($akreditasiLabel ? ' ('.$akreditasiLabel.')' : '') : null,
        ];
    }
}
