<?php

namespace Tests\Feature\Security;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class MiscellaneousTest extends TestCase
{
    use RefreshDatabase;

    public function test_sign_in_errors_are_escaped(): void
    {
        $this->withSession([
            'sdclient.error' => '<script>alert(1)</script>',
            'sdclient.error_description' => '<img src=x onerror=alert(2)>',
        ])
            ->get('/sdclient/error')
            ->assertOk()
            ->assertDontSee('<script>alert(1)</script>', false)
            ->assertDontSee('<img src=x onerror=alert(2)>', false);
    }

    public function test_remember_token_is_not_exposed_by_the_api(): void
    {
        $user = User::forceCreate([
            'id' => 'student-1',
            'name' => 'Anna',
            'email' => 'anna@example.com',
            'type' => 'student',
            'remember_token' => 'secret-remember-token',
        ]);

        Sanctum::actingAs($user);

        $this->getJson('/api/user')
            ->assertOk()
            ->assertJsonPath('id', 'student-1')
            ->assertJsonMissingPath('remember_token');
    }
}
