<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    // List all services
    public function index()
    {
        $services = Service::latest()->paginate(10);

        // Map full image URLs
        $services->getCollection()->transform(function ($service) {
            $service->image = $service->image ? asset('storage/' . $service->image) : null;
            return $service;
        });

        return response()->json($services);
    }

    // Single service by slug
    public function show($slug)
    {
        $service = Service::where('slug', $slug)->firstOrFail();

        $service->image = $service->image ? asset('storage/' . $service->image) : null;

        return response()->json($service);
    }
}
