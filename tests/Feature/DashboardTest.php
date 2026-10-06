<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\Donation;
use App\Models\User;
use App\Models\Waste;
use App\Models\WasteCategory;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_guests_are_redirected_to_the_login_page(): void
    {
        $this->get('/dashboard')->assertRedirect('/login');
    }

    public function test_authenticated_users_can_visit_the_dashboard(): void
    {
        $this->actingAs($user = User::factory()->create());

        $this->get('/dashboard')->assertStatus(200);
    }

    public function test_dashboard_renders_with_seeded_activity(): void
    {
        $user = User::factory()->create();
        $category = WasteCategory::factory()->create();
        $pointId = DB::table('collection_points')->insertGetId([
            'name' => 'Test Point',
            'status' => 'active',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Waste::create([
            'user_id' => $user->id,
            'collection_point_id' => $pointId,
            'waste_category_id' => $category->id,
            'type' => 'Plastic',
            'description' => 'Bottles',
            'weight' => 12.5,
            'status' => 'recyclable',
        ]);

        Campaign::create([
            'title' => 'Spring Cleanup',
            'start_date' => now()->subDays(10)->toDateString(),
            'end_date' => now()->addDays(20)->toDateString(),
            'status' => 'active',
            'user_id' => $user->id,
        ]);

        $wasteId = DB::table('wastes')->insertGetId([
            'user_id' => $user->id,
            'collection_point_id' => $pointId,
            'waste_category_id' => $category->id,
            'type' => 'Glass',
            'description' => 'Jars',
            'weight' => 5,
            'status' => 'recyclable',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Donation::create([
            'user_id' => $user->id,
            'waste_id' => $wasteId,
            'item_name' => 'Garden pallets',
            'condition' => 'good',
            'status' => 'available',
        ]);

        $this->actingAs($user)
            ->get('/back/home')
            ->assertStatus(200)
            ->assertSee('Waste Collection Trends')
            ->assertSee('Spring Cleanup')
            ->assertSee('trendData')
            ->assertSee('vendor/chart.js/chart.umd.js');
    }
}
