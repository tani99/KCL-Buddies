<?php
namespace Tests\Feature;

use Tests\TestCase;

class LoginTest extends TestCase
{

    /**
     * A basic test example.
     *
     * @return void
     */
    public function testUserCanViewLoginForm()
    {
        $response = $this->get('/login');
        $response->assertSuccessful();
        $response->assertViewIs('auth.login');
    }

}