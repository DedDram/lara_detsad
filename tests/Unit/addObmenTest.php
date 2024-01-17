<?php

namespace Tests\Unit;

use Tests\TestCase;

class addObmenTest extends TestCase
{
    /**
     * A basic unit test example.
     */
    public function test_add_announcement_obmen_no_text(): void
    {
        // тестовые данные
        $userData = [
            'city_id' => 1,
            'text' => '',
            'username' => 'Anna',
            'email' => 'email@email.ru',
            'phone' => '5555555',
        ];

        // Посылка POST-запроса на страницу регистрации
        $response = $this->post('/obmen-add', $userData);

        // проверки
        $response->assertJson([
            'status' => 2,
            'msg' => 'Пожалуйста, введите текст объявления',
        ]);
    }

    public function test_add_announcement_obmen_text_min50(): void
    {
        // тестовые данные
        $userData = [
            'city_id' => 1,
            'text' => 'text',
            'username' => 'Anna',
            'email' => 'email@email.ru',
            'phone' => '5555555',
        ];

        // Посылка POST-запроса на страницу регистрации
        $response = $this->post('/obmen-add', $userData);

        // проверки
        $response->assertJson([
            'status' => 2,
            'msg' => 'Минимальная длина объявления 50 символов',
        ]);
    }

    public function test_add_announcement_obmen_text_latin(): void
    {
        // тестовые данные
        $userData = [
            'city_id' => 1,
            'text' => 'The minimum length of the ad is 50 characters is just text',
            'username' => 'Anna',
            'email' => 'email@email.ru',
            'phone' => '5555555',
        ];

        // Посылка POST-запроса на страницу регистрации
        $response = $this->post('/obmen-add', $userData);

        // проверки
        $response->assertJson([
            'status' => 2,
            'msg' => 'Объявления на латинице запрещены',
        ]);
    }

    public function test_add_announcement_obmen_text_links(): void
    {
        // тестовые данные
        $userData = [
            'city_id' => 1,
            'text' => '<a href="/ru/translator">Можно разместить безобидную ссылочку?</a>',
            'username' => 'Anna',
            'email' => 'email@email.ru',
            'phone' => '5555555',
        ];

        // Посылка POST-запроса на страницу регистрации
        $response = $this->post('/obmen-add', $userData);

        // проверки
        $response->assertJson([
            'status' => 2,
            'msg' => 'Спам не пройдет!',
        ]);
    }

    public function test_add_announcement_obmen_no_username(): void
    {
        // тестовые данные
        $userData = [
            'city_id' => 1,
            'text' => 'Минимальная длина объявления 50 символов это просто текст',
            'username' => '',
            'email' => 'email@email.ru',
            'phone' => '5555555',
        ];

        // Посылка POST-запроса на страницу регистрации
        $response = $this->post('/obmen-add', $userData);

        // проверки
        $response->assertJson([
            'status' => 2,
            'msg' => 'Пожалуйста, введите Ваше имя',
        ]);
    }

    public function test_add_announcement_obmen_no_city_id(): void
    {
        // тестовые данные
        $userData = [
            'city_id' => '',
            'text' => 'Минимальная длина объявления 50 символов это просто текст',
            'username' => 'Anna',
            'email' => 'email@email.ru',
            'phone' => '5555555',
        ];

        // Посылка POST-запроса на страницу регистрации
        $response = $this->post('/obmen-add', $userData);

        // проверки
        $response->assertJson([
            'status' => 2,
            'msg' => 'Пожалуйста, выберите город',
        ]);
    }

    public function test_add_announcement_obmen_city_id_no_numeric(): void
    {
        // тестовые данные
        $userData = [
            'city_id' => 'test',
            'text' => 'Минимальная длина объявления 50 символов это просто текст',
            'username' => 'Anna',
            'email' => 'email@email.ru',
            'phone' => '5555555',
        ];

        // Посылка POST-запроса на страницу регистрации
        $response = $this->post('/obmen-add', $userData);

        // проверки
        $response->assertJson([
            'status' => 2,
            'msg' => 'ID города должен быть числом',
        ]);
    }

    public function test_add_announcement_obmen_no_email(): void
    {
        // тестовые данные
        $userData = [
            'city_id' => 1,
            'text' => 'Минимальная длина объявления 50 символов это просто текст',
            'username' => 'Anna',
            'email' => '',
            'phone' => '5555555',
        ];

        // Посылка POST-запроса на страницу регистрации
        $response = $this->post('/obmen-add', $userData);

        // проверки
        $response->assertJson([
            'status' => 2,
            'msg' => 'Пожалуйста, введите E-mail',
        ]);
    }

    public function test_add_announcement_obmen_valid_email(): void
    {
        // тестовые данные
        $userData = [
            'city_id' => 1,
            'text' => 'Минимальная длина объявления 50 символов это просто текст',
            'username' => 'Anna',
            'email' => 'dfdfgdg.ru',
            'phone' => '5555555',
        ];

        // Посылка POST-запроса на страницу регистрации
        $response = $this->post('/obmen-add', $userData);

        // проверки
        $response->assertJson([
            'status' => 2,
            'msg' => 'Пожалуйста, введите корректный E-mail',
        ]);
    }

    public function test_add_announcement_obmen_no_phone(): void
    {
        // тестовые данные
        $userData = [
            'city_id' => 1,
            'text' => 'Минимальная длина объявления 50 символов это просто текст',
            'username' => 'Anna',
            'email' => 'dfdf@gdg.ru',
            'phone' => '',
        ];

        // Посылка POST-запроса на страницу регистрации
        $response = $this->post('/obmen-add', $userData);

        // проверки
        $response->assertJson([
            'status' => 2,
            'msg' => 'Пожалуйста, введите Телефон',
        ]);
    }

    public function test_add_announcement_obmen_its_all_ok(): void
    {
        // тестовые данные
        $userData = [
            'city_id' => 1,
            'text' => 'Минимальная длина объявления 50 символов это просто текст',
            'username' => 'Anna',
            'email' => 'dfdf@gdg.ru',
            'phone' => '6786867868',
        ];

        // Посылка POST-запроса на страницу регистрации
        $response = $this->post('/obmen-add', $userData);

        // проверки
        $response->assertJson([
            'status' => 1,
            'msg' => 'Объявление будет опубликовано после проверки модератором',
        ]);
    }
}
