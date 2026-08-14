<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use Illuminate\Support\Facades\Storage;

class SettingController extends Controller
{
    public function index()
    {
        $settings = Setting::all();

        $data = $settings->mapWithKeys(function ($setting) {
            // Выбираем значение из нужной колонки
            $value = ($setting->type === 'image')
                ? ($setting->image_value ? Storage::disk('public')->url($setting->image_value) : null)
                : $setting->text_value;

            return [$setting->key => $value];
        });

        return response()->json($data);
    }
}
