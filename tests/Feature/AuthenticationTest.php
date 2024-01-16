<?php

namespace Tests\Feature;


use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class AuthenticationTest extends TestCase
{
    public function test_login_screen_can_be_rendered()
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }

    public function test_users_can_not_authenticate_with_invalid_password()
    {
        // Создание фейкового пользователя
        $user = User::factory()->create();
        $this->post('/login', [
            'email' => $user->email,
            'password' => 'wrong-password',
        ]);

        $this->assertGuest();
        $user->delete();
    }

    /** @test */
    public function it_authenticates_user_with_valid_credentials_and_verified_email()
    {
        // Создание фейкового пользователя
        $password = '111111';
        $user = User::factory()->create(['email_verified_at' => now(), 'status' => 1, 'password' => Hash::make($password)]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
            '_token' => csrf_token(),
        ]);
        $response->assertRedirect('/profile');
        $this->assertAuthenticatedAs($user);
        // Удаление фейкового пользователя
        $user->delete();
    }

    /** @test */
    public function it_redirects_user_back_with_error_if_account_not_activated_by_admin()
    {
        // Создание фейкового пользователя
        $password = '111111';
        $user = User::factory()->create(['email_verified_at' => now(), 'status' => 0, 'password' => Hash::make($password)]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
        ]);
        $response->assertSessionHasErrors(['email' => 'Дождитесь активации аккаунта администратором.']);
        $this->assertGuest();
        // Удаление фейкового пользователя
        $user->delete();
    }

    /** @test */
    public function it_redirects_user_back_with_error_if_email_not_verified()
    {
        // Создание фейкового пользователя
        $password = '111111';
        $user = User::factory()->create(['email_verified_at' => null, 'status' => 1, 'password' => Hash::make($password)]);

        $response = $this->post('/login', [
            'email' => $user->email,
            'password' => $password,
        ]);

        $response->assertSessionHasErrors(['email' => 'Ваш аккаунт еще не подтвержден (нужно перейти по ссылке из письма, которое высылают после регистрации)']);
        $this->assertGuest();
        // Удаление фейкового пользователя
        $user->delete();
    }
}
