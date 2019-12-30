<?php

namespace Tests\Unit;

use Faker\Factory;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class StoreApiTest extends TestCase
{
    /**
     * Basic functional test of save values.
     *
     * @return void
     */
    public function testSaveValue()
    {
        $faker = Factory::create();
        $data = [
            $faker->name => $faker->sentence,
        ];
        $response = $this->json('POST', '/api/values', $data);
        $response
            ->assertStatus(201)
            ->assertJson($data);
    }
    
    /**
     * Basic functional test of get values.
     *
     * @return void
     */
    public function testGetValue()
    {
        $response = $this->json('GET', '/api/values');
        $response->assertStatus(200);
    }


    /**
     * Basic functional test of update values.
     *
     * @return void
     */
    public function testUpdateValue()
    {
        $response = $this->json('PATCH', '/api/values');
        $response->assertStatus(200);
    }
}
