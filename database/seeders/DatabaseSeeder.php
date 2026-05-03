<?php

namespace Database\Seeders;

use App\Models\Section;
use App\Models\Banner;
use App\Models\Blog;
use App\Models\Project;
use App\Models\Service;
use App\Models\Partner;
use App\Models\Conference;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // 1. Создаем секции для главной страницы
        $sections = [
            [
                'title' => 'Главный баннер',
                'slug' => 'home-banners',
                'model_type' => 'Banner',
                'order' => 1
            ],
            [
                'title' => 'Наши услуги',
                'slug' => 'home-services',
                'model_type' => 'Service',
                'order' => 2
            ],
            [
                'title' => 'Последние проекты',
                'slug' => 'home-projects',
                'model_type' => 'Project',
                'order' => 3
            ],
            [
                'title' => 'Наш блог',
                'slug' => 'home-blogs',
                'model_type' => 'Blog',
                'order' => 4
            ],
            [
                'title' => 'Партнеры',
                'slug' => 'home-partners',
                'model_type' => 'Partner',
                'order' => 5
            ],
            [
                'title' => 'Конференции',
                'slug' => 'home-conferences',
                'model_type' => 'Conference',
                'order' => 6
            ],
        ];

        foreach ($sections as $section) {
            Section::create($section);
        }

        // 2. Добавляем тестовые данные (по 1 примеру для каждой модели)
        Banner::create([
            'title' => 'Добро пожаловать в Neuron',
            'subtitle' => 'Инновационные решения для вашего бизнеса',
        ]);

        Service::create([
            'title' => 'Веб-разработка',
            'slug' => 'web-development',
            'description' => 'Создание современных веб-приложений на Laravel и React.',
        ]);

        Project::create([
            'title' => 'Проект Альфа',
            'slug' => 'project-alpha',
            'description' => 'Краткое описание успешного проекта.',
        ]);

        Blog::create([
            'title' => 'Первая статья',
            'slug' => 'first-post',
            'content' => 'Это содержание вашей первой статьи в блоге.',
        ]);

        Partner::create([
            'name' => 'Google',
            'link' => 'https://google.com',
        ]);

        Conference::create([
            'title' => 'Tech Summit 2026',
            'location' => 'Ереван',
            'description' => 'Главная техническая конференция года.',
            'date' => now(),
        ]);
    }
}
