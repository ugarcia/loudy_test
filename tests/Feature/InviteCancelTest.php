<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class InviteCancelTest extends TestCase
{
    use RefreshDatabase;

    private $userFrom;
    private $userTo;
    private $invitationId;

    public function setUp(): void
    {
        parent::setUp();
        $this->userFrom = User::factory()->create([
            'email' => 'some@one.here'
        ]);
        $this->userTo = User::factory()->create([
            'email' => 'some@other.one'
        ]); 
        $response = $this->actingAs($this->userFrom, 'api')
            ->postJson('/api/invitations', [
                'to_id' => $this->userTo->id
            ]);
        $this->invitationId = $response['id']; 
    }
    
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test Invite cancel wrong user
     *
     * @return void
     */
    public function test_invite_cancel_wrong_user()
    {
        $response = $this->actingAs($this->userTo, 'api')
            ->post("/api/invitations/{$this->invitationId}/cancel");
        $response->assertStatus(401);                  
    }

    /**
     * Test Invite cancel without invitation.
     *
     * @return void
     */
    public function test_invite_cancel_inexistent()
    {
        $response = $this->actingAs($this->userFrom, 'api')
            ->post("/api/invitations/999/cancel");
        $response->assertStatus(400);                  
    }

    /**
     * Test Invite cancel success
     *
     * @return void
     */
    public function test_invite_cancel_success()
    {
        $response = $this->actingAs($this->userFrom, 'api')
            ->post("/api/invitations/{$this->invitationId}/cancel");
        $response->assertStatus(200);                  
    }

    /**
     * Test Invite cancel already cancelled.
     *
     * @return void
     */
    public function test_invite_cancel_already()
    {
        $response = $this->actingAs($this->userFrom, 'api')
            ->post("/api/invitations/{$this->invitationId}/cancel");
        $response->assertStatus(200); 
        $response = $this->actingAs($this->userFrom, 'api')
            ->post("/api/invitations/{$this->invitationId}/cancel");
        $response->assertStatus(400);                  
    }
}
