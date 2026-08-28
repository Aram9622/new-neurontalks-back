<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\PreparesSeo;
use App\Models\Blog;
use Illuminate\Http\Request;

class BlogController extends Controller
{
    use PreparesSeo;

    // Список всех блогов с пагинацией
    public function index()
    {
        $blogs = Blog::with('seo')->latest()->paginate(10);

        // Формируем полные ссылки на изображения
        $blogs->getCollection()->transform(function ($blog) {
            $blog->image = $blog->image ? asset('/storage/' . $blog->image) : null;
            $this->prepareSeo($blog);
            return $blog;
        });

        return response()->json($blogs);
    }

    // Один блог по slug
    public function show($slug)
    {
        $blog = Blog::with('seo')->where('slug', $slug)->firstOrFail();

        $blog->image = $blog->image ? asset('/storage/' . $blog->image) : null;
        $this->prepareSeo($blog);

        return response()->json($blog);
    }
}
