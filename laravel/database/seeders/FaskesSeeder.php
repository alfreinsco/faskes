<?php

namespace Database\Seeders;

use App\Models\Faskes;
use Illuminate\Database\Seeder;

class FaskesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $faskesDataAmbonLengkap = [
            // --- RUMAH SAKIT ---
            [
                'nama' => 'RS Umum Pusat Dr. Johannes Leimena Ambon',
                'alamat' => 'Jl. R. Suprapto No.123, Rumah Tiga, Kec. Tlk. Ambon, Kota Ambon, Maluku',
                'no_telp' => '0911-3687930',
                'email' => 'info@rsup-leimena.co.id',
                'website' => 'http://www.rsupleimena.co.id/',
                'waktu_buka' => '00:00:00',
                'waktu_tutup' => '23:59:00',
                'type' => 'Rumah Sakit',
                'latitude' => '-3.661521', // Estimasi lokasi Rumahtiga
                'longitude' => '128.186272', // Estimasi lokasi Rumahtiga
                'is_active' => true,
                'layanan' => ['Gawat Darurat 24 Jam', 'Rawat Inap', 'Spesialis', 'Subspesialis'],
            ],
            [
                'nama' => 'RSUD Dr. M. Haulussy Ambon',
                'alamat' => 'Jl. DR. Kayadoe, Kel Benteng, Kec. Nusaniwe, Kota Ambon, Maluku 97116',
                'no_telp' => '0911-344871',
                'email' => 'rsudhaulussy@health.gov',
                'website' => null,
                'waktu_buka' => '00:00:00',
                'waktu_tutup' => '23:59:00',
                'type' => 'Rumah Sakit',
                'latitude' => '-3.7078333', // Ditemukan dari sumber
                'longitude' => '128.1631194', // Ditemukan dari sumber
                'is_active' => true,
                'layanan' => ['Gawat Darurat 24 Jam', 'Rawat Inap', 'Spesialis'],
            ],
            [
                'nama' => 'RS Siloam Ambon',
                'alamat' => 'Jl. Sultan Hasanudin, Hative Kecil, Kec. Sirimau, Kota Ambon, Maluku',
                'no_telp' => '0911-3811900',
                'email' => 'info@siloamhospitals.com',
                'website' => 'http://www.siloamhospitals.com/',
                'waktu_buka' => '00:00:00',
                'waktu_tutup' => '23:59:00',
                'type' => 'Rumah Sakit',
                'latitude' => '-3.6693287', // Estimasi
                'longitude' => '128.1934232', // Estimasi
                'is_active' => true,
                'layanan' => ['Gawat Darurat 24 Jam', 'Rawat Inap', 'Spesialis'],
            ],

            // --- PUSKESMAS ---
            [
                'nama' => 'Puskesmas Rijali',
                'alamat' => 'Batu Merah, Kec. Sirimau, Kota Ambon, Maluku',
                'no_telp' => null,
                'email' => null,
                'website' => null,
                'waktu_buka' => '08:00:00',
                'waktu_tutup' => '18:00:00',
                'type' => 'Puskesmas',
                'latitude' => '-3.6867342', // Estimasi lokasi Rijali
                'longitude' => '128.1829717', // Estimasi lokasi Rijali
                'is_active' => true,
                'layanan' => ['Kesehatan Umum', 'KIA', 'Imunisasi'],
            ],
            [
                'nama' => 'Puskesmas Poka/Rumah Tiga',
                'alamat' => 'Chr. Soplanit, Rumah Tiga, Kec. Tlk. Ambon, Kota Ambon, Maluku',
                'no_telp' => '0911-3682004',
                'email' => 'pkm.pokarumahtiga@gmail.com',
                'website' => 'https://pkmpokarumahtiga.ambon.go.id/',
                'waktu_buka' => '08:30:00',
                'waktu_tutup' => '12:00:00',
                'type' => 'Puskesmas',
                'latitude' => '-3.658041', // Ditemukan dari sumber (Catatan: koordinat lain juga muncul, ini yang dipilih)
                'longitude' => '128.192167', // Ditemukan dari sumber
                'is_active' => true,
                'layanan' => ['Kesehatan Umum', 'KIA', 'Gigi'],
            ],
            [
                'nama' => 'Puskesmas Air Besar',
                'alamat' => 'Batu Merah, Kec. Sirimau, Kota Ambon, Maluku',
                'no_telp' => null,
                'email' => null,
                'website' => null,
                'waktu_buka' => '08:00:00',
                'waktu_tutup' => '16:00:00',
                'type' => 'Puskesmas',
                'latitude' => '-3.6879684', // Estimasi
                'longitude' => '128.2180976', // Estimasi
                'is_active' => true,
                'layanan' => ['Kesehatan Umum', 'KIA', 'Gizi'],
            ],

            // --- APOTEK ---
            [
                'nama' => 'Kimia Farma Apotik',
                'alamat' => 'Jl. Diponegoro No.66, Kel Ahusen, Kec. Sirimau, Kota Ambon, Maluku 96127',
                'no_telp' => "082197621522",
                'email' => null,
                'website' => 'http://kimiafarmaapotek.co.id/',
                'waktu_buka' => '07:00:00',
                'waktu_tutup' => '23:00:00',
                'type' => 'Apotek',
                'latitude' => '-3.700845', // Estimasi
                'longitude' => '128.1061396', // Estimasi
                'is_active' => true,
                'layanan' => ['Obat Bebas', 'Obat Resep', 'Alat Kesehatan'],
            ],
            [
                'nama' => 'Apotek Alaka Farma',
                'alamat' => 'Jl. Raya Air Kuning, Batu Merah, Kec. Sirimau, Kota Ambon, Maluku 97128',
                'no_telp' => '082198130530',
                'email' => null,
                'website' => null,
                'waktu_buka' => '09:00:00',
                'waktu_tutup' => '22:00:00',
                'type' => 'Apotek',
                'latitude' => '-3.6812553', // Estimasi
                'longitude' => '128.2065639', // Estimasi
                'is_active' => true,
                'layanan' => ['Obat Bebas', 'Obat Resep', 'Layanan Dokter (terkadang)'],
            ],
            [
                'nama' => 'Apotek Natsepa Farma',
                'alamat' => 'Jln Said Perintah, Kel Honipopu, Kec. Sirimau, Kota Ambon, Maluku',
                'no_telp' => '0911-344400',
                'email' => null,
                'website' => null,
                'waktu_buka' => '08:00:00',
                'waktu_tutup' => '22:00:00',
                'type' => 'Apotek',
                'latitude' => '-3.6972212', // Estimasi
                'longitude' => '128.178875', // Estimasi
                'is_active' => true,
                'layanan' => ['Obat Bebas', 'Obat Resep', 'Alat Kesehatan'],
            ],
        ];

        foreach ($faskesDataAmbonLengkap as $data) {
            Faskes::create($data);
        }
    }
}
