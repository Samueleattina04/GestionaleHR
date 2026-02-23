<?php

namespace Database\Seeders;

use App\Models\TrainingCourse;
use Illuminate\Database\Seeder;

class TrainingCourseSeeder extends Seeder
{
    public function run(): void
    {
        $courses = [
            [
                'title' => 'Sicurezza sul Lavoro D.Lgs 81/08',
                'description' => 'Corso obbligatorio sulla sicurezza nei luoghi di lavoro per tutti i dipendenti.',
                'provider' => 'Ente Formazione Sicurezza',
                'start_date' => now()->addDays(15)->format('Y-m-d'),
                'end_date' => now()->addDays(16)->format('Y-m-d'),
                'max_participants' => 20,
                'is_mandatory' => true,
                'is_active' => true,
            ],
            [
                'title' => 'Privacy e GDPR',
                'description' => 'Formazione sulla normativa GDPR e protezione dei dati personali.',
                'provider' => 'Studio Legale Partner',
                'start_date' => now()->addDays(30)->format('Y-m-d'),
                'end_date' => now()->addDays(30)->format('Y-m-d'),
                'max_participants' => null,
                'is_mandatory' => true,
                'is_active' => true,
            ],
            [
                'title' => 'Leadership e Management',
                'description' => 'Corso per sviluppare competenze di leadership per manager e futuri responsabili.',
                'provider' => 'Business School Italia',
                'start_date' => now()->addDays(45)->format('Y-m-d'),
                'end_date' => now()->addDays(47)->format('Y-m-d'),
                'max_participants' => 10,
                'is_mandatory' => false,
                'is_active' => true,
            ],
            [
                'title' => 'Excel Avanzato',
                'description' => 'Utilizzo avanzato di Microsoft Excel per analisi dati e reportistica.',
                'provider' => 'Microsoft Partner',
                'start_date' => now()->addDays(20)->format('Y-m-d'),
                'end_date' => now()->addDays(21)->format('Y-m-d'),
                'max_participants' => 12,
                'is_mandatory' => false,
                'is_active' => true,
            ],
        ];

        foreach ($courses as $course) {
            TrainingCourse::create($course);
        }
    }
}
