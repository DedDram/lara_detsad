<?php

namespace Tests\Feature;


use App\Models\DetSad\DetsadImage;
use Faker\Factory;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class AddImageGalleryTest extends TestCase
{
    use DatabaseTransactions, WithFaker;

    /** @test */
    public function it_can_add_an_image_to_gallery(): void
    {
        Storage::fake('public');

        $item_id = 71;
        $file = UploadedFile::fake()->image('test_image.jpg')->size(600);

        $response = $this->postJson('/post/add-gallery', [
            'item_id' => $item_id,
            'condition' => 'agreed',
            'description' => 'Описание более 30 символов пишу тут уже устал...',
            'myfile' => $file,
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 1,
                'msg' => 'Изображение загружено и будет добавлено после проверки модератором.'
            ]);

        $image = DetsadImage::where('item_id', 71)
            ->where('alt', 'Описание более 30 символов пишу тут уже устал...')
            ->first();

        $filePath = public_path("/images/detsad/71/$image->original_name");
        $this->assertTrue(file_exists($filePath), "Файл $filePath не найден.");

        $filePath = public_path("/images/detsad/71/$image->original_name");
        $filePath2 = public_path("/images/detsad/71/$image->thumb");

        if (file_exists($filePath)) {
            if (file_exists($filePath)) {
                unlink($filePath);
            }

            if (file_exists($filePath2)) {
                unlink($filePath2);
            }
        }
    }

    /** @test */
    public function it_requires_item_id_condition_description_and_file(): void
    {
        $response = $this->postJson('/post/add-gallery', []);

        $response->assertJson([
            'status' => 2,
            'msg' => 'item_id не передан',
        ]);
    }

    /** @test */
    public function item_id_must_be_greater_than_zero(): void
    {
        $response = $this->postJson('/post/add-gallery', [
            'item_id' => 0,
            'condition' => 'agreed',
            'description' => $this->faker->sentence(10),
            'myfile' => UploadedFile::fake()->image('test_image.jpg'),
        ]);

        $response->assertJson([
            'status' => 2,
            'msg' => 'item_id не передан',
        ]);
    }

    /** @test */
    public function description_must_be_at_least_30_characters(): void
    {
        $response = $this->postJson('/post/add-gallery', [
            'item_id' => 1,
            'condition' => 'agreed',
            'description' => 'short description',
            'myfile' => UploadedFile::fake()->image('test_image.jpg'),
        ]);

        $response->assertJson([
            'status' => 2,
            'msg' => 'Минимальное описание 30 символов',
        ]);
    }

    /** @test */
    public function description_must_be_in_latin_characters(): void
    {
        $response = $this->postJson('/post/add-gallery', [
            'item_id' => 1,
            'condition' => 'agreed',
            'description' => 'description_must_be_in_latin_characters description_must_be_in_latin_characters',
            'myfile' => UploadedFile::fake()->image('test_image.jpg'),
        ]);

        $response->assertJson([
            'status' => 2,
            'msg' => 'Описания на латинице запрещены',
        ]);
    }

    /** @test */
    public function description_cannot_contain_spam_links(): void
    {
        $response = $this->postJson('/post/add-gallery', [
            'item_id' => 1,
            'condition' => 'agreed',
            'description' => 'Это я пытаюсь загрузить ссылку, можно или нет? <a href="/">ссылка</a>',
            'myfile' => UploadedFile::fake()->image('test_image.jpg'),
        ]);

        $response->assertJson([
            'status' => 2,
            'msg' => 'Спам не пройдет!',
        ]);
    }

    /** @test */
    public function file_must_be_a_valid_image(): void
    {
        $response = $this->postJson('/post/add-gallery', [
            'item_id' => 1,
            'condition' => 'agreed',
            'description' => 'Описание более 30 символов пишу тут уже устал...',
            'myfile' => UploadedFile::fake()->create('test_document.pdf'),
        ]);

        $response->assertJson([
            'status' => 2,
            'msg' => 'Поддерживаются только файлы с расширениями .jpg, .jpeg и .png.',
        ]);
    }

    /** @test */
    public function file_size_must_be_less_than_or_equal_to_5mb(): void
    {
        $response = $this->postJson('/post/add-gallery', [
            'item_id' => 1,
            'condition' => 'agreed',
            'description' => 'Описание более 30 символов пишу тут уже устал...',
            'myfile' => UploadedFile::fake()->image('large_image.jpg')->size(6000),
        ]);

        $response->assertJson([
            'status' => 2,
            'msg' => 'Максимальный размер файла 5 МБ',
        ]);
    }

    /** @test */
    public function file_size_must_be_greater_than_or_equal_to_500kb(): void
    {
        $response = $this->postJson('/post/add-gallery', [
            'item_id' => 1,
            'condition' => 'agreed',
            'description' => 'Описание более 30 символов пишу тут уже устал...',
            'myfile' => UploadedFile::fake()->image('small_image.jpg')->size(300),
        ]);

        $response->assertJson([
            'status' => 2,
            'msg' => 'Минимальный размер файла 500 КБ',
        ]);
    }
}
