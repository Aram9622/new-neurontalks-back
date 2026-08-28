<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Api\Concerns\PreparesSeo;
use App\Models\Service;
use Illuminate\Http\Request;

class ServiceController extends Controller
{
    use PreparesSeo;

    // List all services
    public function index()
    {
        $services = Service::with('seo')->latest()->paginate(10);

        // Map full image URLs
        $services->getCollection()->transform(function ($service) {
            $service->image = $service->image ? asset('/storage/' . $service->image) : null;
            $this->prepareSeo($service);
            return $service;
        });

        return response()->json($services);
    }

    // Single service by slug
    public function show($slug)
    {
        $service = Service::with('seo')->where('slug', $slug)->firstOrFail();

        $service->image = $service->image ? asset('/storage/' . $service->image) : null;
        $this->prepareSeo($service);

        return response()->json($service);
    }
}
