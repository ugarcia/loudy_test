<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class InviteCreateTest extends TestCase
{
    use RefreshDatabase;

    private $userFrom;
    private $userTo;

    public function setUp(): void
    {
        parent::setUp();
        $this->userFrom = User::factory()->create([
            'email' => 'some@one.here'
        ]);
        $this->userTo = User::factory()->create([
            'email' => 'some@other.one'
        ]);    
    }
    
    public function tearDown(): void
    {
        parent::tearDown();
    }

    /**
     * Test Invite creation without auth.
     *
     * @return void
     */
    public function test_invite_create_no_auth()
    {
        $response = $this->postJson('/api/invitations', [
            'to_id' => $this->userTo->id
        ]);
        $response->assertStatus(401);
    }

    /**
     * Test Invite creation without invitee.
     *
     * @return void
     */
    public function test_invite_create_no_invitee()
    {
        $response = $this->actingAs($this->userFrom, 'api')
            ->postJson('/api/invitations', []);
        $response->assertStatus(400);                  
    }

    /**
     * Test Invite creation to self.
     *
     * @return void
     */
    public function test_invite_create_to_self()
    {
        $response = $this->actingAs($this->userFrom, 'api')
            ->postJson('/api/invitations', [
                'to_id' => $this->userFrom->id
            ]);
        $response->assertStatus(400);                  
    }

    /**
     * Test Invite creation to inexistent.
     *
     * @return void
     */
    public function test_invite_create_to_inexistent()
    {
        $response = $this->actingAs($this->userFrom, 'api')
            ->postJson('/api/invitations', [
                'to_id' => 999
            ]);
        $response->assertStatus(400);                  
    }

    /**
     * Test Invite creation success.
     *
     * @return void
     */
    public function test_invite_create_success()
    {
        $response = $this->actingAs($this->userFrom, 'api')
            ->postJson('/api/invitations', [
                'to_id' => $this->userTo->id
            ]);

        $response->assertStatus(201)
            ->assertJson([
                'state' => 'created'
            ]);

        $id = $response['id'];
        $response = $this->actingAs($this->userFrom, 'api')
            ->get("/api/invitations/${id}");
        $response->assertStatus(200)
            ->assertJson([
                'from_id' => $this->userFrom->id,
                'to_id' => $this->userTo->id,
                'state' => 'created'
            ]);                   
    }
}
