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
        $response->assertJson([
            'status' => 1,
            'msg' => 'Вам на почту ушло письмо, перейдите по ссылке в нем для подтверждения email.',
        ]);
    }

    /** @test */
    public function registration_requires_all_fields()
    {
        // Посылка POST-запроса на страницу регистрации с пустыми полями
        $response = $this->post('/register', []);

        $response->assertStatus(302)
            ->assertSessionHasErrors(['name', 'email', 'password'])
            ->assertSessionHasInput([]);
    }
}
