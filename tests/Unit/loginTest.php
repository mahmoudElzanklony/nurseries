<?php

namespace Tests\Unit;

use App\Actions\ProductsProblemsWithAllData;
use App\Models\User;
use Tests\TestCase;

class loginTest extends TestCase
{
    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_example()
    {
        $user = User::query()->create([
            'username'=>'ali123',
            'country_id'=>1,
            'email'=>'ali123@yahoo.com',
            'password'=>'ali',
            'role_id'=>1,
            'activation_code'=>1234,
            'phone'=>'03123123',
        ]);
        return $this->assertTrue(true);
    }
}
