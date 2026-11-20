<?php

namespace Tests\Feature;

use App\Exports\PelamarExport;
use App\Http\Requests\StorePelamarRequest;
use App\Models\Pelamar;
use App\Models\StatusPelamar;
use App\Models\TahapRekrutmen;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class PelamarInputRulesTest extends TestCase
{
    /** @return array<string, array{0: string, 1: string, 2: bool}> */
    public static function samples(): array
    {
        return [
            'nama biasa' => [StorePelamarRequest::POLA_NAMA, 'Siti Aminah', true],
            'nama dengan apostrof' => [StorePelamarRequest::POLA_NAMA, "Muhamad Ma'ruf", true],
            'nama dengan angka' => [StorePelamarRequest::POLA_NAMA, 'Budi 123', false],
            'nama dengan gelar/koma' => [StorePelamarRequest::POLA_NAMA, 'Siti, S.Pd', false],
            'nama dengan simbol' => [StorePelamarRequest::POLA_NAMA, 'Andi@', false],
            'gelar dengan titik dan koma' => [StorePelamarRequest::POLA_GELAR, 'S.Pd., M.Kom', true],
            'gelar dengan angka' => [StorePelamarRequest::POLA_GELAR, 'S1', false],
            'gelar dengan kurung' => [StorePelamarRequest::POLA_GELAR, 'S.Pd (UNJ)', false],
            'prodi dengan tanda hubung dan apostrof' => [StorePelamarRequest::POLA_PROGRAM_STUDI, "Ilmu Al-Qur'an dan Tafsir", true],
            'prodi dengan angka' => [StorePelamarRequest::POLA_PROGRAM_STUDI, 'S1 PGSD', false],
            'prodi dengan simbol' => [StorePelamarRequest::POLA_PROGRAM_STUDI, 'Bahasa & Sastra', false],
            'no hp 12 digit' => [StorePelamarRequest::POLA_NO_HP, '085759967549', true],
            'no hp 13 digit' => [StorePelamarRequest::POLA_NO_HP, '0857599675491', false],
            'no hp dengan +' => [StorePelamarRequest::POLA_NO_HP, '+6285759967549', false],
            'no hp dengan huruf' => [StorePelamarRequest::POLA_NO_HP, '08abc', false],
        ];
    }

    #[DataProvider('samples')]
    public function test_field_patterns(string $pattern, string $value, bool $allowed): void
    {
        $this->assertSame($allowed, (bool) preg_match($pattern, $value));
    }

    public function test_export_includes_tahap_and_status_columns(): void
    {
        $pelamar = new Pelamar(['nama' => 'Siti Aminah']);
        $pelamar->setRelation('tahapRekrutmen', new TahapRekrutmen(['tahap_rekrutmen' => 'Wawancara']));
        $pelamar->setRelation('statusPelamar', new StatusPelamar(['status_pelamar' => 'Lolos']));
        $pelamar->setRelation('pendidikanTerakhir', null);

        $export = new PelamarExport(collect([$pelamar]));

        $this->assertSame(['Nama', 'Tahap', 'Status'], array_slice($export->headings(), 0, 3));
        $this->assertSame(['Wawancara', 'Lolos'], array_slice($export->map($pelamar), 1, 2));
    }

    public function test_no_hp_variants_count_as_the_same_number(): void
    {
        $this->assertSame('81234567890', Pelamar::intiNoHp('081234567890'));
        $this->assertSame('81234567890', Pelamar::intiNoHp('6281234567890'));
        $this->assertSame('81234567890', Pelamar::intiNoHp('81234567890'));
        $this->assertSame('', Pelamar::intiNoHp(null));
        $this->assertSame([], Pelamar::variasiNoHp(''));
        $this->assertSame(['81234567890', '081234567890', '6281234567890'], Pelamar::variasiNoHp('081234567890'));
    }

    public function test_kecocokan_lists_every_shared_identifier(): void
    {
        $a = new Pelamar(['nik' => '3201010101010001', 'no_hp' => '081234567890', 'email' => 'Siti@Mail.com ']);

        $this->assertSame(['NIK', 'No. WA', 'Email'], $a->kecocokanDengan(new Pelamar(['nik' => '3201010101010001', 'no_hp' => '6281234567890', 'email' => 'siti@mail.com'])));
        $this->assertSame(['No. WA'], $a->kecocokanDengan(new Pelamar(['nik' => '3201010101010009', 'no_hp' => '81234567890', 'email' => 'lain@mail.com'])));
        $this->assertSame(['Email'], $a->kecocokanDengan(new Pelamar(['nik' => '3201010101010009', 'no_hp' => '089999999999', 'email' => 'siti@mail.com'])));
        $this->assertSame([], $a->kecocokanDengan(new Pelamar(['nik' => null, 'no_hp' => '', 'email' => ''])));
        $this->assertSame([], (new Pelamar(['nik' => null, 'no_hp' => '', 'email' => '']))->kecocokanDengan(new Pelamar(['nik' => null, 'no_hp' => '', 'email' => ''])));
    }
}
