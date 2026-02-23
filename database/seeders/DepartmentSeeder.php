<?php

namespace Database\Seeders;

use App\Models\Department;
use Illuminate\Database\Seeder;

class DepartmentSeeder extends Seeder
{
    public function run(): void
    {
        $departments = [
            ['name' => 'Risorse Umane', 'code' => 'HR', 'description' => 'Gestione del personale e delle risorse umane'],
            ['name' => 'Information Technology', 'code' => 'IT', 'description' => 'Infrastruttura tecnologica e sviluppo software'],
            ['name' => 'Amministrazione', 'code' => 'AMM', 'description' => 'Gestione amministrativa e contabile'],
            ['name' => 'Commerciale', 'code' => 'COMM', 'description' => 'Vendite e sviluppo commerciale'],
            ['name' => 'Marketing', 'code' => 'MKT', 'description' => 'Marketing e comunicazione aziendale'],
            ['name' => 'Operazioni', 'code' => 'OPS', 'description' => 'Gestione operativa e logistica'],
        ];

        foreach ($departments as $dept) {
            Department::create($dept);
        }
    }
}
