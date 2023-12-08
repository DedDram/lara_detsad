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
        // Ваши тестовые данные
        $userData = [
            'name' => 'John Doe',
            'email' => 'john@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
        ];

        // Посылка POST-запроса на страницу регистрации
        $response = $this->post('/register', $userData);

        // Ваши проверки
        $response->assertStatus(302);
        $this->assertAuthenticated();

        // Дополнительные проверки, если необходимо
    }
}
