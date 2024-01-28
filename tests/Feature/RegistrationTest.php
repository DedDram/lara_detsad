<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class RegistrationTest extends TestCase
{
    use DatabaseTransactions;
    use WithFaker;

    /** @test */
    public function test_register_screen_can_be_rendered(): void
    {
        $response = $this->get('/register');
        $response->assertStatus(200);
    }

    /** @test */
    public function user_can_register(): void
    {
        // тестовые данные
        $userData = [
            'name' => 'John Doe',
            'email' => 'ltpm@ya.ru',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // Посылка POST-запроса на страницу регистрации
        $response = $this->post('/register', $userData);

        $response->assertRedirect('/verification-message');

        $response = $this->get('/verification-message');
        $response->assertStatus(200);
        $response->assertSeeText('Подтвердите свою почту');
    }

    /** @test */
    public function user_can_register_no_name(): void
    {
        // тестовые данные
        $userData = [
            'name' => '',
            'email' => 'ltpm@ya.ru',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // Посылка POST-запроса на страницу регистрации
        $response = $this->post('/register', $userData);

        $response->assertSessionHasErrors([
            'name' => 'Пожалуйста, введите имя'
        ]);
    }

    /** @test */
    public function user_can_register_no_name_valid(): void
    {
        // тестовые данные
        $userData = [
            'name' => 'Jo',
            'email' => 'ltpm@ya.ru',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // Посылка POST-запроса на страницу регистрации
        $response = $this->post('/register', $userData);

        $response->assertSessionHasErrors([
            'name' => 'Минимальная длина имени 5 символов'
        ]);
    }


    /** @test */
    public function user_can_register_no_email(): void
    {
        // тестовые данные
        $userData = [
            'name' => 'John Doe',
            'email' => '',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // Посылка POST-запроса на страницу регистрации
        $response = $this->post('/register', $userData);

        $response->assertSessionHasErrors([
            'email' => 'Пожалуйста, введите E-mail'
        ]);
    }

    /** @test */
    public function user_can_register_no_valid_email(): void
    {
        // тестовые данные
        $userData = [
            'name' => 'John Doe',
            'email' => 'ltpmya.ru',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // Посылка POST-запроса на страницу регистрации
        $response = $this->post('/register', $userData);

        $response->assertSessionHasErrors([
            'email' => 'Пожалуйста, введите корректный E-mail'
        ]);
    }


    /** @test */
    public function user_can_register_no_unique_email(): void
    {
        // тестовые данные
        $userData = [
            'name' => 'John Doe',
            'email' => 'info@detskysad.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // Посылка POST-запроса на страницу регистрации
        $response = $this->post('/register', $userData);

        $response->assertSessionHasErrors([
            'email' => 'Пользователь с таким email уже зарегистрирован'
        ]);
    }

    /** @test */
    public function user_can_register_no_password(): void
    {
        // тестовые данные
        $userData = [
            'name' => 'John Doe',
            'email' => 'ltpm@ya.ru',
            'password' => '',
            'password_confirmation' => '',
        ];

        // Посылка POST-запроса на страницу регистрации
        $response = $this->post('/register', $userData);

        $response->assertSessionHasErrors([
            'password' => 'Пожалуйста, введите пароль'
        ]);
    }

    /** @test */
    public function registration_requires_all_fields(): void
    {
        // Посылка POST-запроса на страницу регистрации с пустыми полями
        $response = $this->post('/register', []);

        $response->assertStatus(302)
            ->assertSessionHasErrors(['name', 'email', 'password'])
            ->assertSessionHasInput([]);
    }
}
