<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Testing\Fluent\AssertableJson;
use Tests\TestCase;

class AuthenticationControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_login_returns_api_token(): void
    {
        $user = User::factory()->create();
        $user->setAttribute('password', 'password');
        $user->save();

        $response = $this->post('/api/simpsons-quotes/login', ['email' => $user->email, 'password' => 'password']);

        $response->assertStatus(200)
            ->assertJson(fn(AssertableJson $json) => $json->has('token')
            );
    }
}
