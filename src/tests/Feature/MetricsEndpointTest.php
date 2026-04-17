<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class MetricsEndpointTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_access_metrics(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson('/api/metrics');

        $response->assertOk()
            ->assertJsonStructure([
                'total_orders',
                'approved_orders',
                'under_review_orders',
                'blocked_orders',
                'pending_orders',
                'average_risk_score',
                'high_risk_count',
                'medium_risk_count',
                'low_risk_count',
            ]);
    }
}