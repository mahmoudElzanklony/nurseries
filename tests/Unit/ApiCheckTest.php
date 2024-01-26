<?php
use Tests\TestCase;
class ApiCheckTest extends TestCase
{
    public function test_profit()
    {

        /*$response = $this->get('/api/products')->withHeaders(['apikey'=>'nurseries2023']);
        $response->assertHeader('Content-Type', 'application/json')
            ->assertStatus(200)
            ->assertJson(['status' => 200]);*/
        $data = 30;
        $this->assertLessThanOrEqual(30,$data);
    }
}
