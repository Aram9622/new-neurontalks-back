<?php

namespace Tests\Feature;

use App\Models\Section;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_home_sections_include_the_button_color(): void
    {
        Section::create([
            'title' => 'Banner',
            'slug' => 'banner',
            'button_title' => 'Learn more',
            'button_link' => '/about',
            'button_color' => '#ff9900',
            'model_type' => 'None',
        ]);

        $this->getJson('/api/home')
            ->assertOk()
            ->assertJsonPath('banner.button_color', '#ff9900');
    }
}
