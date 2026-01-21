<?php

namespace Tests\Feature\Auth;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

class ForgotPasswordTest extends TestCase
{
    use RefreshDatabase;

    public function test_google_only_user_cannot_request_password_reset(): void
    {
        User::factory()->create([
            'email' => 'google@test.com',
            'password' => null,
            'google_id' => '123456',
        ]);

        $this->postJson('/forgot-password', [
            'email' => 'google@test.com',
        ])->assertStatus(422);
    }

    public function test_local_user_can_request_password_reset(): void
    {
        Password::shouldReceive('sendResetLink')
            ->once()
            ->andReturn(Password::RESET_LINK_SENT);

        User::factory()->create([
            'email' => 'local@test.com',
            'password' => bcrypt('secret'),
        ]);

        $this->postJson('/forgot-password', [
            'email' => 'local@test.com',
        ])->assertStatus(200);
    }
}
