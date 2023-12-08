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
    public function user_can_register()
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

        // проверки
        $response->assertStatus(302);
        $this->assertAuthenticated();
    }

    /** @test */
    public function registration_requires_all_fields()
    {
        // Посылка POST-запроса на страницу регистрации с пустыми полями
        $response = $this->post('/register', ['name' => 'John',]);

        // Проверка статуса ответа (должен быть 302 для ошибки валидации)
        $response->assertStatus(302);

        // Проверяем, что редирект идет на ожидаемый URL
        $response->assertRedirect('/register');

        // Проверка, что в ответе есть сообщения об ошибках для каждого поля
        $response->assertJsonValidationErrors([
            'name'
        ]);
    }
}
