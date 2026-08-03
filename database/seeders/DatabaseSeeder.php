<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 0. Seed Admin User
        \App\Models\User::updateOrCreate(
            ['username' => 'admin'],
            [
                'name' => 'Admin Lacete',
                'email' => 'admin@spk.coffe',
                'password' => \Illuminate\Support\Facades\Hash::make('admin123'),
            ]
        );

        // 0b. Seed Default Website Settings
        $settings = [
            'app_name' => 'Lacete Coffeeshop',
            'footer_text' => '© 2026 Lacete Coffeeshop. All rights reserved.',
            'logo_path' => '',
            'favicon_path' => '',
        ];
        foreach ($settings as $key => $value) {
            \App\Models\WebsiteSetting::updateOrCreate(['key' => $key], ['value' => $value]);
        }

        // 1. Seed Kriterias
        $kriterias = [
            ['id' => 'K1', 'nama' => 'Harga', 'jenis' => 'Cost', 'rating' => 100],
            ['id' => 'K2', 'nama' => 'Kualitas', 'jenis' => 'Benefit', 'rating' => 71],
            ['id' => 'K3', 'nama' => 'Pengiriman', 'jenis' => 'Cost', 'rating' => 57],
            ['id' => 'K4', 'nama' => 'Fleksibilitas', 'jenis' => 'Benefit', 'rating' => 34],
            ['id' => 'K5', 'nama' => 'Pelayanan', 'jenis' => 'Benefit', 'rating' => 23],
        ];

        foreach ($kriterias as $k) {
            \App\Models\Kriteria::updateOrCreate(['id' => $k['id']], $k);
        }

        // 2. Seed Alternatifs
        $alternatifs = [
            ['id' => 1, 'nama' => 'Beska'],
            ['id' => 2, 'nama' => 'Fow'],
            ['id' => 3, 'nama' => 'Gerai Hutan'],
            ['id' => 4, 'nama' => 'Pesirah'],
            ['id' => 5, 'nama' => 'Benawa'],
            ['id' => 6, 'nama' => 'Samping Roastery'],
            ['id' => 7, 'nama' => 'Koloni'],
            ['id' => 8, 'nama' => 'Agam Pisan'],
            ['id' => 9, 'nama' => 'Dialek'],
            ['id' => 10, 'nama' => 'Diego'],
        ];

        foreach ($alternatifs as $a) {
            \App\Models\Alternatif::updateOrCreate(['id' => $a['id']], $a);
        }

        // 3. Seed Penilaians
        $penilaians = [
            ['alternatif_id' => 1, 'values' => ['K1' => 200, 'K2' => 5, 'K3' => 2, 'K4' => 3, 'K5' => 3]],
            ['alternatif_id' => 2, 'values' => ['K1' => 200, 'K2' => 3, 'K3' => 3, 'K4' => 5, 'K5' => 3]],
            ['alternatif_id' => 3, 'values' => ['K1' => 204, 'K2' => 5, 'K3' => 2, 'K4' => 5, 'K5' => 5]],
            ['alternatif_id' => 4, 'values' => ['K1' => 210, 'K2' => 3, 'K3' => 4, 'K4' => 3, 'K5' => 3]],
            ['alternatif_id' => 5, 'values' => ['K1' => 212, 'K2' => 3, 'K3' => 3, 'K4' => 5, 'K5' => 3]],
            ['alternatif_id' => 6, 'values' => ['K1' => 220, 'K2' => 5, 'K3' => 3, 'K4' => 3, 'K5' => 3]],
            ['alternatif_id' => 7, 'values' => ['K1' => 195, 'K2' => 3, 'K3' => 4, 'K4' => 3, 'K5' => 3]],
            ['alternatif_id' => 8, 'values' => ['K1' => 225, 'K2' => 5, 'K3' => 2, 'K4' => 5, 'K5' => 3]],
            ['alternatif_id' => 9, 'values' => ['K1' => 221, 'K2' => 3, 'K3' => 5, 'K4' => 3, 'K5' => 3]],
            ['alternatif_id' => 10, 'values' => ['K1' => 240, 'K2' => 3, 'K3' => 5, 'K4' => 3, 'K5' => 3]],
        ];

        foreach ($penilaians as $p) {
            foreach ($p['values'] as $kriteriaId => $nilai) {
                \App\Models\Penilaian::updateOrCreate(
                    [
                        'alternatif_id' => $p['alternatif_id'],
                        'kriteria_id' => $kriteriaId
                    ],
                    ['nilai' => $nilai]
                );
            }
        }
    }
}
