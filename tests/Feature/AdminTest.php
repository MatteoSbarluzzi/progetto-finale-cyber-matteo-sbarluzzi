<?php

namespace Tests\Feature;

use App\Models\User;
use Tests\TestCase;

class AdminTest extends TestCase
{
    public function test_admin_on_internal_host_can_see_dashboard(): void
    {
        config()->set('app.url', 'http://internal.admin');

        $admin = User::factory()->create(['is_admin' => true]);

        $res = $this->actingAs($admin)
                    ->get('http://internal.admin/admin/dashboard');

        $res->assertStatus(200);
    }

    public function test_admin_on_wrong_host_is_redirected(): void
    {
        config()->set('app.url', 'http://cyber.blog:8000');

        $admin = User::factory()->create(['is_admin' => true]);

        $res = $this->actingAs($admin)
                    ->get('http://cyber.blog:8000/admin/dashboard');

        $res->assertStatus(302);
        $res->assertRedirect(route('homepage'));
    }

    public function test_non_admin_on_internal_host_is_forbidden(): void
    {
        config()->set('app.url', 'http://internal.admin');

        $user = User::factory()->create(['is_admin' => false]);

        $res = $this->actingAs($user)
                    ->get('http://internal.admin/admin/dashboard');

        $res->assertStatus(403);
    }
}
