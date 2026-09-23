<?php

namespace Tests\Feature;

use App\Models\Service;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ServiceApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_service_color_is_included_in_service_api_responses(): void
    {
        $service = Service::create([
            'title' => 'Web development',
            'slug' => 'web-development',
            'service_color' => '#ff9900',
        ]);

        $this->getJson('/api/services')
            ->assertOk()
            ->assertJsonPath('data.0.id', $service->id)
            ->assertJsonPath('data.0.service_color', '#ff9900');

        $this->getJson('/api/services/web-development')
            ->assertOk()
            ->assertJsonPath('id', $service->id)
            ->assertJsonPath('service_color', '#ff9900');
    }
}
