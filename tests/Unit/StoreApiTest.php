<?php

namespace Tests\Unit;

use Faker\Factory;
use Tests\TestCase;

class StoreApiTest extends TestCase
{

    /**
     * Create a new instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->faker = Factory::create();
    }

    /**
     * Basic functional test of save values.
     *
     * @return void
     */
    public function testSaveValue()
    {
        $data = [
            $this->faker->name => $this->faker->sentence,
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
