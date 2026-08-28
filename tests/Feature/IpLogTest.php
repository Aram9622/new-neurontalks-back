<?php

namespace Tests\Feature;

use App\Filament\Widgets\CustomerStatsOverview;
use App\Models\IpLog;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;
use Tests\TestCase;

class IpLogTest extends TestCase
{
    use RefreshDatabase;

    public function test_api_visits_are_logged_with_request_context(): void
    {
        $this->withServerVariables(['REMOTE_ADDR' => '203.0.113.10'])
            ->withHeaders([
                'Referer' => 'https://neurontalks.com/',
                'User-Agent' => 'Neurontalks Test Browser',
            ])
            ->getJson('/api/settings')
            ->assertOk();

        $this->assertDatabaseHas('ip_logs', [
            'ip_address' => '203.0.113.10',
            'method' => 'GET',
            'referrer' => 'https://neurontalks.com/',
            'user_agent' => 'Neurontalks Test Browser',
        ]);
    }

    public function test_preflight_requests_are_not_logged(): void
    {
        $this->call('OPTIONS', '/api/settings')->assertSuccessful();

        $this->assertDatabaseCount('ip_logs', 0);
    }

    public function test_dashboard_counts_unique_visitors_instead_of_every_api_request(): void
    {
        foreach (['/api/settings', '/api/home', '/api/blogs'] as $url) {
            IpLog::create([
                'ip_address' => '203.0.113.10',
                'method' => 'GET',
                'url' => url($url),
            ]);
        }

        IpLog::create([
            'ip_address' => '198.51.100.20',
            'method' => 'GET',
            'url' => url('/api/settings'),
        ]);

        Livewire::test(CustomerStatsOverview::class)
            ->assertSee('Unique visitors')
            ->assertSee('Unique IP addresses')
            ->assertSee('2');
    }
}
