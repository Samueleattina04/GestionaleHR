<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $hrDept = Department::where('code', 'HR')->first();
        $itDept = Department::where('code', 'IT')->first();
        $ammDept = Department::where('code', 'AMM')->first();
        $commDept = Department::where('code', 'COMM')->first();

        // Admin
        $admin = User::create([
            'name' => 'Marco',
            'surname' => 'Rossi',
            'email' => 'admin@gestionалehr.it',
            'password' => Hash::make('Admin1234!'),
            'role' => 'admin',
            'department_id' => $hrDept?->id,
            'job_title' => 'Amministratore di Sistema',
            'status' => 'active',
            'hire_date' => '2020-01-01',
            'salary' => 4500.00,
            'approved_at' => now(),
        ]);

        // Fix email with proper charset
        $admin->update(['email' => 'admin@gestionalehr.it']);

        // HR Manager
        $hr = User::create([
            'name' => 'Giulia',
            'surname' => 'Bianchi',
            'email' => 'hr@gestionalehr.it',
            'password' => Hash::make('Hr1234!!'),
            'role' => 'hr',
            'department_id' => $hrDept?->id,
            'job_title' => 'HR Manager',
            'status' => 'active',
            'hire_date' => '2020-03-15',
            'salary' => 3800.00,
            'approved_at' => now(),
            'approved_by' => $admin->id,
        ]);

        // IT Manager
        $itManager = User::create([
            'name' => 'Luca',
            'surname' => 'Ferrari',
            'email' => 'luca.ferrari@gestionalehr.it',
            'password' => Hash::make('Manager1234!'),
            'role' => 'manager',
            'department_id' => $itDept?->id,
            'job_title' => 'IT Manager',
            'status' => 'active',
            'hire_date' => '2020-06-01',
            'salary' => 3500.00,
            'approved_at' => now(),
            'approved_by' => $admin->id,
        ]);

        // Assign manager to IT dept
        if ($itDept) {
            $itDept->update(['manager_id' => $itManager->id]);
        }

        // Employees
        $employees = [
            ['name' => 'Anna', 'surname' => 'Verdi', 'email' => 'anna.verdi@gestionalehr.it', 'dept' => 'IT', 'job' => 'Full Stack Developer', 'salary' => 2800.00],
            ['name' => 'Paolo', 'surname' => 'Ricci', 'email' => 'paolo.ricci@gestionalehr.it', 'dept' => 'IT', 'job' => 'DevOps Engineer', 'salary' => 2900.00],
            ['name' => 'Elena', 'surname' => 'Colombo', 'email' => 'elena.colombo@gestionalehr.it', 'dept' => 'AMM', 'job' => 'Contabile', 'salary' => 2500.00],
            ['name' => 'Roberto', 'surname' => 'Conti', 'email' => 'roberto.conti@gestionalehr.it', 'dept' => 'COMM', 'job' => 'Sales Manager', 'salary' => 3000.00],
            ['name' => 'Francesca', 'surname' => 'Esposito', 'email' => 'francesca.esposito@gestionalehr.it', 'dept' => 'MKT', 'job' => 'Marketing Specialist', 'salary' => 2600.00],
        ];

        foreach ($employees as $emp) {
            $dept = Department::where('code', $emp['dept'])->first();
            User::create([
                'name' => $emp['name'],
                'surname' => $emp['surname'],
                'email' => $emp['email'],
                'password' => Hash::make('Employee1234!'),
                'role' => 'employee',
                'department_id' => $dept?->id,
                'job_title' => $emp['job'],
                'status' => 'active',
                'hire_date' => now()->subMonths(rand(6, 36))->format('Y-m-d'),
                'salary' => $emp['salary'],
                'approved_at' => now(),
                'approved_by' => $hr->id,
            ]);
        }

        $this->command->info('Utenti creati:');
        $this->command->info('  Admin:    admin@gestionalehr.it / Admin1234!');
        $this->command->info('  HR:       hr@gestionalehr.it / Hr1234!!');
        $this->command->info('  Manager:  luca.ferrari@gestionalehr.it / Manager1234!');
        $this->command->info('  Dipendenti: Employee1234!');
    }
}
