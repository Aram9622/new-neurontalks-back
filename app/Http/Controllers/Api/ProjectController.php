<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Support\Facades\Storage;

class ProjectController extends Controller
{
    // Список всех проектов
    public function index()
    {
        $projects = Project::with('technologies')->latest()->paginate(12);

        // Преобразуем изображения в полные URL
        $projects->getCollection()->transform(function ($project) {
            $project->image = $project->image ? Storage::disk('public')->url($project->image) : null;

            // Преобразуем галерею в массив полных URL
            if ($project->gallery) {
                $project->gallery = array_map(function($path) {
                    return Storage::disk('public')->url($path);
                }, $project->gallery);
            }

            return $project;
        });

        return response()->json($projects);
    }

    // Один проект по slug
    public function show($slug)
    {
        $project = Project::with('technologies')->where('slug', $slug)->firstOrFail();

        $project->image = $project->image ? Storage::disk('public')->url($project->image) : null;

        // Преобразуем галерею в массив полных URL
        if ($project->gallery) {
            $project->gallery = array_map(function($path) {
                return Storage::disk('public')->url($path);
            }, $project->gallery);
        }

        return response()->json($project);
    }
}
