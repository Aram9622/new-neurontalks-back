<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use Illuminate\Support\Facades\Storage;

class BlogController extends Controller
{
    // Список всех блогов с пагинацией
    public function index()
    {
        $blogs = Blog::latest()->paginate(10);

        // Формируем полные ссылки на изображения
        $blogs->getCollection()->transform(function ($blog) {
            $blog->image = $blog->image ? Storage::disk('public')->url($blog->image) : null;
            return $blog;
        });

        return response()->json($blogs);
    }

    // Один блог по slug
    public function show($slug)
    {
        $blog = Blog::where('slug', $slug)->firstOrFail();

        $blog->image = $blog->image ? Storage::disk('public')->url($blog->image) : null;

        return response()->json($blog);
    }
}
