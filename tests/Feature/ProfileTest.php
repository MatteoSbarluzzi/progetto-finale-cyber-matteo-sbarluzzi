<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ProfileTest extends TestCase
{
    use RefreshDatabase;

    public function test_user_can_view_own_profile_edit(): void
    {
        $user = User::factory()->create();

        $res = $this->actingAs($user)->get('/profile');

        $res->assertStatus(200);
        $res->assertSee('Modifica profilo');
    }

    public function test_user_can_update_own_profile_vulnerable(): void
    {
        $user = User::factory()->create([
            'name'  => 'Old Name',
            'email' => 'old@example.com',
        ]);

        $payload = [
            'name'  => 'New Name',
            'email' => 'new@example.com',
        ];

        $res = $this->actingAs($user)->post('/profile/update', $payload);

        $res->assertSessionHas('status');
        $res->assertRedirect(); // back()

        $this->assertDatabaseHas('users', [
            'id'    => $user->id,
            'name'  => 'New Name',
            'email' => 'new@example.com',
        ]);
    }
}
