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
                'alamat' => 'Jl. Mr. CHR Soplanit, Negeri Rumahtiga, Kec. Teluk Ambon',
                'no_telp' => '0911-3687937',
                'email' => 'info@rsup-leimena.co.id',
                'website' => 'https://rsup-leimena.co.id',
                'waktu_buka' => '00:00:00',
                'waktu_tutup' => '23:59:00',
                'type' => 'Rumah Sakit',
                'latitude' => '-3.6923', // Estimasi lokasi Rumahtiga
                'longitude' => '128.1725', // Estimasi lokasi Rumahtiga
                'is_active' => true,
                'layanan' => ['Gawat Darurat 24 Jam', 'Rawat Inap', 'Spesialis', 'Subspesialis'],
            ],
            [
                'nama' => 'RSUD Dr. M. Haulussy Ambon',
                'alamat' => 'Jl. Dr. Kayadoe No. 16, Kelurahan Benteng, Kec. Nusaniwe',
                'no_telp' => '0911-344871',
                'email' => 'rsudhaulussy@health.gov',
                'website' => null,
                'waktu_buka' => '00:00:00',
                'waktu_tutup' => '23:59:00',
                'type' => 'Rumah Sakit',
                'latitude' => '-3.307650', // Ditemukan dari sumber
                'longitude' => '128.950699', // Ditemukan dari sumber
                'is_active' => true,
                'layanan' => ['Gawat Darurat 24 Jam', 'Rawat Inap', 'Spesialis'],
            ],
            [
                'nama' => 'RS Siloam Ambon',
                'alamat' => 'Jl. Sultan Hasanuddin No. 10, Kel. Hative Kecil, Kec. Sirimau',
                'no_telp' => '0911-3811900',
                'email' => 'info@siloamhospitals.com',
                'website' => 'https://www.siloamhospitals.com/our-hospitals/siloam-hospitals-ambon',
                'waktu_buka' => '00:00:00',
                'waktu_tutup' => '23:59:00',
                'type' => 'Rumah Sakit',
                'latitude' => '-3.6558', // Estimasi
                'longitude' => '128.1738', // Estimasi
                'is_active' => true,
                'layanan' => ['Gawat Darurat 24 Jam', 'Rawat Inap', 'Spesialis'],
            ],

            // --- PUSKESMAS ---
            [
                'nama' => 'Puskesmas Rijali',
                'alamat' => 'Jl. Rijali, Kelurahan Batu Merah, Kec. Sirimau',
                'no_telp' => null,
                'email' => null,
                'website' => null,
                'waktu_buka' => '08:00:00',
                'waktu_tutup' => '16:00:00',
                'type' => 'Puskesmas',
                'latitude' => '-3.6700', // Estimasi lokasi Rijali
                'longitude' => '128.1780', // Estimasi lokasi Rijali
                'is_active' => true,
                'layanan' => ['Kesehatan Umum', 'KIA', 'Imunisasi'],
            ],
            [
                'nama' => 'Puskesmas Poka/Rumah Tiga',
                'alamat' => 'Jln. Chr. Soplanit, Desa Rumah Tiga, Kec. Teluk Ambon',
                'no_telp' => '0911-3682004',
                'email' => 'pkm.pokarumahtiga@gmail.com',
                'website' => 'https://pkmpokarumahtiga.ambon.go.id/',
                'waktu_buka' => '08:00:00',
                'waktu_tutup' => '16:00:00',
                'type' => 'Puskesmas',
                'latitude' => '-3.6944261', // Ditemukan dari sumber (Catatan: koordinat lain juga muncul, ini yang dipilih)
                'longitude' => '128.1813049', // Ditemukan dari sumber
                'is_active' => true,
                'layanan' => ['Kesehatan Umum', 'KIA', 'Gigi'],
            ],
            [
                'nama' => 'Puskesmas Air Besar',
                'alamat' => 'Jl. Kh Ahmad Bantam, Kec. Sirimau',
                'no_telp' => null,
                'email' => null,
                'website' => null,
                'waktu_buka' => '08:00:00',
                'waktu_tutup' => '16:00:00',
                'type' => 'Puskesmas',
                'latitude' => '-3.6820', // Estimasi
                'longitude' => '128.1800', // Estimasi
                'is_active' => true,
                'layanan' => ['Kesehatan Umum', 'KIA', 'Gizi'],
            ],

            // --- APOTEK ---
            [
                'nama' => 'Apotek Kimia Farma Ambon',
                'alamat' => 'Jl. A.Y. Patty No. 15, Ambon (Salah satu cabang)',
                'no_telp' => null,
                'email' => null,
                'website' => 'https://kimiafarma.co.id',
                'waktu_buka' => '08:00:00',
                'waktu_tutup' => '22:00:00',
                'type' => 'Apotek',
                'latitude' => '-3.6610', // Estimasi
                'longitude' => '128.1740', // Estimasi
                'is_active' => true,
                'layanan' => ['Obat Bebas', 'Obat Resep', 'Alat Kesehatan'],
            ],
            [
                'nama' => 'Apotek Cahaya Farma',
                'alamat' => 'Jl. A. Y. Patty No. 85-86, Ambon',
                'no_telp' => '0911-342014',
                'email' => null,
                'website' => null,
                'waktu_buka' => '08:00:00',
                'waktu_tutup' => '21:00:00',
                'type' => 'Apotek',
                'latitude' => '-3.6605', // Estimasi
                'longitude' => '128.1745', // Estimasi
                'is_active' => true,
                'layanan' => ['Obat Bebas', 'Obat Resep', 'Layanan Dokter (terkadang)'],
            ],
            [
                'nama' => 'Apotek Natsepa Farma',
                'alamat' => 'Jln. Said Perintah, No. 36, Kel Honipopu, Sirimau, Kota Ambon',
                'no_telp' => '0911-344400',
                'email' => null,
                'website' => null,
                'waktu_buka' => '08:00:00',
                'waktu_tutup' => '21:00:00',
                'type' => 'Apotek',
                'latitude' => '-3.6600', // Estimasi
                'longitude' => '128.1750', // Estimasi
                'is_active' => true,
                'layanan' => ['Obat Bebas', 'Obat Resep', 'Alat Kesehatan'],
            ],
        ];

        foreach ($faskesDataAmbonLengkap as $data) {
            Faskes::create($data);
        }
    }
}
