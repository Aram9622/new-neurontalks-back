<?php

namespace Database\Seeders;

use App\Models\Project;
use App\Models\Technology;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Создаем базовые технологии
        $technologies = ['Laravel', 'React', 'Vue.js', 'Tailwind CSS', 'Next.js', 'PostgreSQL', 'Docker', 'Redis', 'TypeScript', 'Flutter'];
        
        $techModels = collect();
        foreach ($technologies as $tech) {
            $techModels->push(Technology::firstOrCreate(['name' => $tech]));
        }

        // Создаем 10 проектов
        Project::factory()
            ->count(10)
            ->create()
            ->each(function ($project) use ($techModels) {
                // Привязываем от 2 до 5 случайных технологий к каждому проекту
                $project->technologies()->attach(
                    $techModels->random(rand(2, 5))->pluck('id')->toArray()
                );

                // Можно добавить фейковую галерею (просто пути)
                $project->update([
                    'gallery' => [
                        'projects/1.jpg',
                        'projects/2.jpg',
                        'projects/3.jpg',
                    ]
                ]);
            });
    }
}
