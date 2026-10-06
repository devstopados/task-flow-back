<?php

namespace Database\Seeders;

use App\Models\StatusTask;
use Illuminate\Database\Seeder;

class StatusTaskSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $statuses = [
            [
                'name' => 'Não Iniciada',
                'slug' => 'nao-iniciada',
                'active' => true,
            ],
            [
                'name' => 'Em Andamento',
                'slug' => 'em-andamento',
                'active' => true,
            ],
            [
                'name' => 'Pausada',
                'slug' => 'pausada',
                'active' => true,
            ],
            [
                'name' => 'Concluída',
                'slug' => 'concluida',
                'active' => true,
            ],
        ];

        foreach ($statuses as $status) {
            StatusTask::query()->updateOrCreate(
                ['slug' => $status['slug']],
                [
                    'name' => $status['name'],
                    'active' => $status['active'],
                ]
            );
        }
    }
}
