<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Conference;
use Illuminate\Support\Facades\Storage;

class ConferenceController extends Controller
{
    // Список всех конференций
    public function index()
    {
        $conferences = Conference::latest()->paginate(10);

        $conferences->getCollection()->transform(function($conf) {
            $conf->main_image = $conf->main_image ? Storage::disk('public')->url($conf->main_image) : null;
            return $conf;
        });

        return response()->json($conferences);
    }

    // Одна конференция со всеми деталями по slug
    public function show($slug)
    {
        $conference = Conference::with(['speakers', 'partners', 'agendas', 'sections'])
            ->where('slug', $slug)
            ->firstOrFail();

        // Преобразуем изображения в полные URL
        $conference->main_image = $conference->main_image ? Storage::disk('public')->url($conference->main_image) : null;

        $conference->speakers->transform(function($speaker) {
            $speaker->image = $speaker->image ? Storage::disk('public')->url($speaker->image) : null;
            return $speaker;
        });

        $conference->sections->transform(function($section) {
            $section->image = $section->image ? Storage::disk('public')->url($section->image) : null;
            return $section;
        });

        $conference->partners->transform(function($partner) {
            $partner->logo = $partner->logo ? Storage::disk('public')->url($partner->logo) : null;
            return $partner;
        });

        return response()->json($conference);
    }
}
