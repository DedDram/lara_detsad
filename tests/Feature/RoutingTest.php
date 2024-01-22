<?php

namespace Tests\Feature;


use Tests\TestCase;

class RoutingTest extends TestCase
{
    public function test_main_page(): void
    {
        $response = $this->get('/');
        $response->assertStatus(200);
    }
    public function test_section_page_200(): void
    {
        $response = $this->get('/3-vao');
        $response->assertStatus(200);
    }
    public function test_section_page_404(): void
    {
        $response = $this->get('/3vao');
        $response->assertStatus(404);
    }
    public function test_section_page_301(): void
    {
        $response = $this->get('/3-va');
        $response->assertStatus(301)->assertRedirect('https://detskysad.com/3-vao');
    }
    public function test_category_page_200(): void
    {
        $response = $this->get('/3-vao/8-metrogorodok');
        $response->assertStatus(200);
    }
    public function test_category_page_404(): void
    {
        $response = $this->get('/3-vao/8metrogorodok');
        $response->assertStatus(404);
    }
    public function test_category_page_301(): void
    {
        $response = $this->get('/3-vao/8-metr');
        $response->assertStatus(301)->assertRedirect('https://detskysad.com/3-vao/8-metrogorodok');
    }
    public function test_sadik_page_200(): void
    {
        $response = $this->get('/3-vao/8-metrogorodok/69-detskiy-sad-357');
        $response->assertStatus(200);
    }
    public function test_sadik_page_404(): void
    {
        $response = $this->get('/3-vao/8-metrogorodok/69detskiy-sad-357');
        $response->assertStatus(404);
    }
}
