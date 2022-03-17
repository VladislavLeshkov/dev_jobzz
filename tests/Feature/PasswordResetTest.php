<?php

namespace Tests\Feature;

use App\Models\Auth\User;
use App\Notifications\Auth\UserResetPassword;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class PasswordResetTest extends TestCase
{
    use RefreshDatabase;

    public function test_reset_password_link_screen_can_be_rendered()
    {
        $response = $this->get('/auth/forgot-password');

        $response->assertStatus(200);
    }

    public function test_reset_password_link_can_be_requested()
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/auth/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, UserResetPassword::class);
    }

    public function test_reset_password_screen_can_be_rendered()
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/auth/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, UserResetPassword::class, function ($notification) {
            $response = $this->get('/auth/reset-password/'.$notification->token);

            $response->assertStatus(200);

            return true;
        });
    }

    public function test_password_can_be_reset_with_valid_token()
    {
        Notification::fake();

        $user = User::factory()->create();

        $this->post('/auth/forgot-password', ['email' => $user->email]);

        Notification::assertSentTo($user, UserResetPassword::class, function ($notification) use ($user) {
            $response = $this->post('/auth/reset-password', [
                'token' => $notification->token,
                'email' => $user->email,
                'password' => 'secret12345',
                'password_confirmation' => 'secret12345',
            ]);

            $response->assertSessionHasNoErrors();

            return true;
        });
    }
}
