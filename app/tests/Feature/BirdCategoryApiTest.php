<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BirdCategoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_category(): void
    {
        $payload = [
            'name' => 'Rare Birds',
            'description' => 'High value sightings',
        ];

        $res = $this->postJson('/api/categories', $payload);

        $res->assertStatus(201)
            ->assertJsonPath('data.name', 'Rare Birds')
            ->assertJsonPath('data.description', 'High value sightings')
            ->assertJsonStructure(['data' => ['id', 'name', 'slug', 'description', 'created_at', 'updated_at']]);

        $this->assertDatabaseHas('bird_categories', ['name' => 'Rare Birds']);
    }

    public function test_name_is_required(): void
    {
        $this->postJson('/api/categories', ['name' => ''])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_name_must_be_unique(): void
    {
        $this->postJson('/api/categories', ['name' => 'Common'])->assertStatus(201);
        $this->postJson('/api/categories', ['name' => 'Common'])
            ->assertStatus(422)
            ->assertJsonValidationErrors(['name']);
    }

    public function test_can_list_categories(): void
    {
        $this->postJson('/api/categories', ['name' => 'Common'])->assertStatus(201);
        $this->postJson('/api/categories', ['name' => 'Rare'])->assertStatus(201);

        $this->getJson('/api/categories')
            ->assertStatus(200)
            ->assertJsonCount(2, 'data');
    }
}
