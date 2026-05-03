<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Banner;
use App\Models\Blog;
use App\Models\Conference;
use App\Models\Execution;
use App\Models\Partner;
use App\Models\Project;
use App\Models\Section;
use App\Models\Service;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        // Получаем все секции
        $sections = Section::orderBy('order')->get();

        // Преобразуем коллекцию в ассоциативный массив, где ключом будет slug
        $data = $sections->mapWithKeys(function ($section) {
            $relatedData = [];

            // Подтягиваем связанные данные (4 случайных записи)
            switch ($section->model_type) {
                case 'Banner':
                    $relatedData = Banner::latest()->get();
                    break;
                case 'Project':
                    $relatedData = Project::inRandomOrder()->limit(4)->get();
                    break;
                case 'Service':
                    $relatedData = Service::inRandomOrder()->limit(4)->get();
                    break;
                case 'Blog':
                    $relatedData = Blog::inRandomOrder()->limit(4)->get();
                    break;
                case 'Partner':
                    $relatedData = Partner::inRandomOrder()->limit(4)->get();
                    break;
                case 'Conference':
                    $relatedData = Conference::inRandomOrder()->limit(4)->get();
                    break;
                case 'Execution':
                    $relatedData = Execution::inRandomOrder()->limit(4)->get();
                    break;
                default:
                    $relatedData = null;
            }

            // Обработка ссылок на изображения для связанных данных
            if ($relatedData && ($relatedData instanceof \Illuminate\Support\Collection)) {
                $relatedData = $relatedData->map(function($item) {
                    if (isset($item->image)) {
                        $item->image = $item->image ? asset('storage/' . $item->image) : null;
                    }
                    if (isset($item->logo)) {
                        $item->logo = $item->logo ? asset('storage/' . $item->logo) : null;
                    }
                    return $item;
                });
            }

            return [
                $section->slug => [
                    'title' => $section->title,
                    'subtitle' => $section->subtitle,
                    'description' => $section->description,
                    'image' => $section->image ? asset('storage/' . $section->image) : null,
                    'button_title' => $section->button_title,
                    'button_link' => $section->button_link,
                    'model_type' => $section->model_type,
                    'data' => $relatedData
                ]
            ];
        });

        return response()->json($data);
    }
}
