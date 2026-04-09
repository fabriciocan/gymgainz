<?php

namespace Database\Seeders;

use App\Models\Exercise;
use Illuminate\Database\Seeder;

class ExerciseSeeder extends Seeder
{
    public function run(): void
    {
        $exercises = [
            ['name' => 'Supino Reto',          'muscle_group' => 'chest'],
            ['name' => 'Supino Inclinado',      'muscle_group' => 'chest'],
            ['name' => 'Crucifixo',             'muscle_group' => 'chest'],
            ['name' => 'Peck Deck',             'muscle_group' => 'chest'],
            ['name' => 'Flexão de Braço',       'muscle_group' => 'chest'],
            ['name' => 'Pull-down',             'muscle_group' => 'back'],
            ['name' => 'Remada Curvada',        'muscle_group' => 'back'],
            ['name' => 'Remada Unilateral',     'muscle_group' => 'back'],
            ['name' => 'Remada Serrote',        'muscle_group' => 'back'],
            ['name' => 'Puxada Frontal',        'muscle_group' => 'back'],
            ['name' => 'Barra Fixa',            'muscle_group' => 'back'],
            ['name' => 'Deadlift',              'muscle_group' => 'back'],
            ['name' => 'Desenvolvimento',       'muscle_group' => 'shoulders'],
            ['name' => 'Elevação Lateral',      'muscle_group' => 'shoulders'],
            ['name' => 'Rosca Direta',          'muscle_group' => 'biceps'],
            ['name' => 'Rosca Alternada',       'muscle_group' => 'biceps'],
            ['name' => 'Tríceps Corda',         'muscle_group' => 'triceps'],
            ['name' => 'Tríceps Testa',         'muscle_group' => 'triceps'],
            ['name' => 'Agachamento',           'muscle_group' => 'quadriceps'],
            ['name' => 'Leg Press',             'muscle_group' => 'quadriceps'],
            ['name' => 'Cadeira Extensora',     'muscle_group' => 'quadriceps'],
            ['name' => 'Avanço',                'muscle_group' => 'quadriceps'],
            ['name' => 'Afundo',                'muscle_group' => 'quadriceps'],
            ['name' => 'Cadeira Flexora',       'muscle_group' => 'hamstrings'],
            ['name' => 'Stiff',                 'muscle_group' => 'hamstrings'],
            ['name' => 'Hip Thrust',            'muscle_group' => 'glutes'],
            ['name' => 'Panturrilha em Pé',     'muscle_group' => 'calves'],
            ['name' => 'Panturrilha Sentado',   'muscle_group' => 'calves'],
            ['name' => 'Abdominal Crunch',      'muscle_group' => 'core'],
            ['name' => 'Prancha',               'muscle_group' => 'core'],
        ];

        foreach ($exercises as $exercise) {
            Exercise::firstOrCreate(
                ['name' => $exercise['name'], 'user_id' => null],
                ['muscle_group' => $exercise['muscle_group']]
            );
        }
    }
}
