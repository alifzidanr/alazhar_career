<?php

namespace App\Exports;

use App\Models\Pelamar;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;

class PelamarExport extends DefaultValueBinder implements FromCollection, ShouldAutoSize, WithCustomValueBinder, WithHeadings, WithMapping
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

    /**
     * Force any cell value starting with =, +, -, or @ to plain text instead
     * of letting Excel/PhpSpreadsheet interpret it as a formula — several
     * exported columns (alamat, institusi, no_hp, etc.) are free-text an
     * applicant fills in themselves, so this is the standard CSV/Excel
     * formula-injection mitigation (OWASP), not just phone numbers like
     * "+62..." being (harmlessly) coerced to text instead of a sum.
     */
    public function bindValue(Cell $cell, mixed $value): bool
    {
        if (is_string($value) && $value !== '' && in_array($value[0], ['=', '+', '-', '@', "\t", "\r"], true)) {
            $cell->setValueExplicit($value, DataType::TYPE_STRING);

            return true;
        }

        return parent::bindValue($cell, $value);
    }
}
