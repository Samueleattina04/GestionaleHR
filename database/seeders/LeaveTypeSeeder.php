<?php

namespace Database\Seeders;

use App\Models\LeaveType;
use Illuminate\Database\Seeder;

class LeaveTypeSeeder extends Seeder
{
    public function run(): void
    {
        $types = [
            ['name' => 'Ferie', 'code' => 'FERIE', 'max_days_per_year' => 26, 'is_paid' => true, 'requires_document' => false, 'is_active' => true],
            ['name' => 'Permesso ROL', 'code' => 'ROL', 'max_days_per_year' => 10, 'is_paid' => true, 'requires_document' => false, 'is_active' => true],
            ['name' => 'Malattia', 'code' => 'MAL', 'max_days_per_year' => 180, 'is_paid' => true, 'requires_document' => true, 'description' => 'Richiede certificato medico', 'is_active' => true],
            ['name' => 'Maternita/Paternita', 'code' => 'MAT', 'max_days_per_year' => 150, 'is_paid' => true, 'requires_document' => true, 'is_active' => true],
            ['name' => 'Permesso non retribuito', 'code' => 'NPR', 'max_days_per_year' => 30, 'is_paid' => false, 'requires_document' => false, 'is_active' => true],
            ['name' => 'Formazione', 'code' => 'FORM', 'max_days_per_year' => 10, 'is_paid' => true, 'requires_document' => false, 'is_active' => true],
        ];

        foreach ($types as $type) {
            LeaveType::create($type);
        }
    }
}
