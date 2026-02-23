<?php

namespace Database\Seeders;

use App\Models\Communication;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommunicationSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('role', 'admin')->first();
        if (!$admin) return;

        $communications = [
            [
                'title' => 'Benvenuti in GestionaleHR',
                'body' => "Cari colleghi,\n\nBenvenuti nel nuovo sistema gestionale HR aziendale. Da oggi potrete gestire le presenze, richiedere ferie e permessi, consultare i cedolini e molto altro direttamente da questa piattaforma.\n\nPer qualsiasi dubbio non esitate a contattare l'ufficio HR.\n\nBuon lavoro a tutti!",
                'target' => 'all',
                'priority' => 'normal',
                'author_id' => $admin->id,
                'is_published' => true,
                'published_at' => now(),
            ],
            [
                'title' => 'Procedura di Timbratura Digitale',
                'body' => "A partire da oggi, la timbratura delle presenze avviene esclusivamente attraverso il portale HR.\n\nRicordatevi di:\n- Registrare l'entrata al mattino\n- Registrare l'inizio e la fine della pausa pranzo\n- Registrare l'uscita a fine giornata\n\nIn caso di dimenticanza, contattare l'HR entro la giornata stessa.",
                'target' => 'all',
                'priority' => 'high',
                'author_id' => $admin->id,
                'is_published' => true,
                'published_at' => now()->subDays(1),
            ],
            [
                'title' => 'Aggiornamento Policy Ferie 2026',
                'body' => "Si comunica che la policy aziendale per le ferie 2026 è stata aggiornata.\n\nLe principali novità:\n- Le ferie devono essere richieste con almeno 5 giorni lavorativi di anticipo\n- Il residuo ferie 2025 deve essere smaltito entro il 31 marzo 2026\n- Le ferie estive (luglio-agosto) devono essere pianificate entro il 30 aprile\n\nPer il documento completo consultare la sezione Documenti.",
                'target' => 'all',
                'priority' => 'normal',
                'author_id' => $admin->id,
                'is_published' => true,
                'published_at' => now()->subDays(3),
            ],
        ];

        foreach ($communications as $comm) {
            Communication::create($comm);
        }
    }
}
