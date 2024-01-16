<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\DatabaseTransactions;
use Tests\TestCase;

class PostCommentsControllerTest extends TestCase
{
    use DatabaseTransactions;
    public function test_addComment_no_user_register(): void
    {

        // Данные формы для добавления отзыва
        $formData = [
            'object_group' => 'com_content',
            'object_id' => 3281,
            'task' => 'create',
            'star' => 4,
            'username' => 'John Doe',
            'email' => 'john@example.com',
            'subscribe' => true,
            'description' => 'Добавляем сюда текст, который более 100 символов, а то сработает ошибка и тест закончится ошибкой, как-то так и все тогда, хана!',
        ];

        // Эмулируем HTTP-запрос с данными формы
        $response = $this->post('/post/comment', $formData);

        // Проверяем, что отзыв был успешно добавлен
        $response->assertJson([
            'status' => 1,
            'msg' => 'Спасибо, Ваш отзыв будет добавлен после проверки модератором',
        ]);

    }

    public function test_addComment_no_user_register_no_name(): void
    {

        // Данные формы для добавления отзыва
        $formData = [
            'object_group' => 'com_content',
            'object_id' => 3281,
            'task' => 'create',
            'star' => 4,
            'username' => '',
            'email' => 'john@example.com',
            'subscribe' => true,
            'description' => 'Добавляем сюда текст, который более 100 символов, а то сработает ошибка и тест закончится ошибкой, как-то так и все тогда, хана!',
        ];

        // Эмулируем HTTP-запрос с данными формы
        $response = $this->post('/post/comment', $formData);

        $response->assertJson([
            'status' => 2,
            'msg' => 'Пожалуйста, введите Ваше имя',
        ]);

    }

    public function test_addComment_no_user_register_no_mail(): void
    {

        // Данные формы для добавления отзыва
        $formData = [
            'object_group' => 'com_content',
            'object_id' => 3281,
            'task' => 'create',
            'star' => 4,
            'username' => 'John Doe',
            'email' => '',
            'subscribe' => true,
            'description' => 'Добавляем сюда текст, который более 100 символов, а то сработает ошибка и тест закончится ошибкой, как-то так и все тогда, хана!',
        ];

        // Эмулируем HTTP-запрос с данными формы
        $response = $this->post('/post/comment', $formData);

        $response->assertJson([
            'status' => 2,
            'msg' => 'Пожалуйста, введите E-mail',
        ]);

    }

    public function test_addComment_no_user_register_no_text(): void
    {

        // Данные формы для добавления отзыва
        $formData = [
            'object_group' => 'com_content',
            'object_id' => 3281,
            'task' => 'create',
            'star' => 4,
            'username' => 'John Doe',
            'email' => '',
            'subscribe' => true,
            'description' => '',
        ];

        // Эмулируем HTTP-запрос с данными формы
        $response = $this->post('/post/comment', $formData);

        $response->assertJson([
            'status' => 2,
            'msg' => 'Пожалуйста, введите текст отзыва',
        ]);
    }

    public function test_addComment_no_user_register_no_object_id(): void
    {
        // Данные формы для добавления отзыва
        $formData = [
            'object_group' => 'com_content',
            'object_id' => 0,
            'task' => 'create',
            'star' => 4,
            'username' => 'John Doe',
            'email' => 'john@example.com',
            'subscribe' => true,
            'description' => 'Добавляем сюда текст, который более 100 символов, а то сработает ошибка и тест закончится ошибкой, как-то так и все тогда, хана!',
        ];

        // Эмулируем HTTP-запрос с данными формы
        $response = $this->post('/post/comment', $formData);

        $response->assertJson([
            'status' => 2,
            'msg' => 'Не передан object_group или object_group',
        ]);
    }

    public function test_addComment_no_user_register_no_object_group(): void
    {
        // Данные формы для добавления отзыва
        $formData = [
            'object_group' => '------',
            'object_id' => 3281,
            'task' => 'create',
            'star' => 4,
            'username' => 'John Doe',
            'email' => 'john@example.com',
            'subscribe' => true,
            'description' => 'Добавляем сюда текст, который более 100 символов, а то сработает ошибка и тест закончится ошибкой, как-то так и все тогда, хана!',
        ];

        // Эмулируем HTTP-запрос с данными формы
        $response = $this->post('/post/comment', $formData);

        $response->assertJson([
            'status' => 2,
            'msg' => 'Не передан object_group или object_group',
        ]);
    }
}
