<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Equipment;
use App\Models\Loan;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // 1. Utilisateurs
        $luc = User::create([
            'name' => 'Luc Vandamme',
            'email' => 'luc.vandamme@ifosup.be',
            'phone' => '010/22.33.44',
            'password' => Hash::make('password'),
            'role' => 'manager',
            'borrower_type' => 'staff',
            'section_or_service' => 'Réserve matériel',
        ]);

        $manon = User::create([
            'name' => 'Manon Lejeune',
            'email' => 'manon.lejeune@ifosup.be',
            'phone' => '0470/11.22.33',
            'password' => Hash::make('password'),
            'role' => 'borrower',
            'borrower_type' => 'student',
            'section_or_service' => 'BES Webdev 2e',
        ]);

        $mamadou = User::create([
            'name' => 'Mamadou Sow',
            'email' => 'mamadou.sow@ifosup.be',
            'phone' => '0471/22.33.44',
            'password' => Hash::make('password'),
            'role' => 'borrower',
            'borrower_type' => 'student',
            'section_or_service' => 'BES Webdev 2e',
        ]);

        $jonas = User::create([
            'name' => 'Jonas Mertens',
            'email' => 'jonas.mertens@ifosup.be',
            'phone' => '0472/33.44.55',
            'password' => Hash::make('password'),
            'role' => 'borrower',
            'borrower_type' => 'staff',
            'section_or_service' => 'Enseignant',
        ]);

        // 2. Catégories (6 catégories propres)
        $cats = collect(['Portable', 'Caméra', 'Trépied', 'Arduino', 'Micro', 'Projecteur'])
            ->mapWithKeys(fn ($name) => [$name => Category::create(['name' => $name])->id]);

        // 3. Inventaire (extrait propre du tableur Excel)
        $items = [
            ['PC-01', 'Portable', 'Lenovo ThinkPad E14 Gen 5', 'good', 'A.04', null],
            ['PC-03', 'Portable', 'Lenovo ThinkPad E14 Gen 5', 'worn', 'A.04', 'touche F manque'],
            ['PC-06', 'Portable', 'Dell Latitude 5440', 'out_of_service', 'Atelier', 'écran qui clignote'],
            ['PC-07', 'Portable', 'Dell Latitude 5440', 'good', 'A.04', null],
            ['CAM-01', 'Caméra', 'Sony HDR-CX405', 'good', 'A.04', null],
            ['CAM-03', 'Caméra', 'Sony HDR-CX405 + sacoche', 'good', 'A.04', '2 batteries, chargeur, carte SD 32 Go'],
            ['CAM-04', 'Caméra', 'GoPro Hero 11', 'good', 'A.04', 'carte SD 64 Go dedans'],
            ['TRE-01', 'Trépied', 'Manfrotto Compact Action', 'good', 'A.04', null],
            ['TRE-03', 'Trépied', 'K&F Concept 160 cm', 'damaged', 'A.04', 'une jambe ne se bloque plus'],
            ['ARD-01', 'Arduino', 'Kit Arduino Uno R4 + breadboard', 'good', 'A.04', null],
            ['MIC-01', 'Micro', 'Rode Wireless GO II', 'good', 'A.04', null],
            ['PROJ-01', 'Projecteur', 'Epson EB-W49', 'good', 'A.04', 'lampe changée en sept.'],
        ];

        foreach ($items as [$code, $cat, $name, $condition, $location, $notes]) {
            Equipment::create([
                'inventory_code' => $code,
                'category_id' => $cats[$cat],
                'name' => $name,
                'condition' => $condition,
                'location' => $location,
                'notes' => $notes,
            ]);
        }

        // 4. Prêt ouvert de Mamadou sur PC-07 (pour tester immédiatement la règle R1)
        Loan::create([
            'reference' => '2026-088',
            'equipment_id' => Equipment::where('inventory_code', 'PC-07')->first()->id,
            'user_id' => $mamadou->id,
            'status' => 'active',
            'purpose' => 'Projet web',
            'handed_over_at' => now()->subDays(2),
            'due_at' => '2026-12-07',
        ]);
    }
}
