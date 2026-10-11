<?php

namespace Tests\Feature;

use App\Models\Campaign;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CampaignToggleTest extends TestCase
{
    use RefreshDatabase;

    private function campaign(): Campaign
    {
        return Campaign::create([
            'title' => 'Spring Cleanup',
            'description' => 'Join the cleanup.',
            'start_date' => now()->toDateString(),
            'end_date' => now()->addWeek()->toDateString(),
            'status' => 'active',
        ]);
    }

    public function test_guest_is_redirected_to_login_when_toggling_campaign(): void
    {
        $this->post(route('campaigns.toggle', $this->campaign()))
            ->assertRedirect(route('login.form'));

        $this->assertDatabaseCount('participations', 0);
    }

    public function test_guest_check_returns_not_joined(): void
    {
        $this->getJson(route('campaigns.check', $this->campaign()))
            ->assertOk()
            ->assertJson(['joined' => false]);
    }

    public function test_user_check_reports_joined_status(): void
    {
        $user = User::factory()->create();
        $campaign = $this->campaign();

        $this->actingAs($user)
            ->getJson(route('campaigns.check', $campaign))
            ->assertOk()
            ->assertJson(['joined' => false]);

        $campaign->participations()->create(['user_id' => $user->id, 'status' => 'approved']);

        $this->actingAs($user)
            ->getJson(route('campaigns.check', $campaign))
            ->assertOk()
            ->assertJson(['joined' => true]);
    }

    public function test_user_can_join_a_campaign(): void
    {
        $user = User::factory()->create();
        $campaign = $this->campaign();

        $this->actingAs($user)
            ->postJson(route('campaigns.toggle', $campaign))
            ->assertOk()
            ->assertJson(['joined' => true, 'participants' => 1]);

        $this->assertDatabaseHas('participations', [
            'campaign_id' => $campaign->id,
            'user_id' => $user->id,
            'status' => 'approved',
        ]);

        $this->assertDatabaseHas('campaigns', [
            'id' => $campaign->id,
            'participants_count' => 1,
        ]);
    }

    public function test_user_can_leave_a_campaign(): void
    {
        $user = User::factory()->create();
        $campaign = $this->campaign();
        $campaign->participations()->create(['user_id' => $user->id, 'status' => 'approved']);
        $campaign->increment('participants_count');

        $this->actingAs($user)
            ->postJson(route('campaigns.toggle', $campaign))
            ->assertOk()
            ->assertJson(['joined' => false, 'participants' => 0]);

        $this->assertDatabaseMissing('participations', [
            'campaign_id' => $campaign->id,
            'user_id' => $user->id,
        ]);

        $this->assertDatabaseHas('campaigns', [
            'id' => $campaign->id,
            'participants_count' => 0,
        ]);
    }
}