<?php

namespace Tests\Feature\Domains\Auth;

use Laravel\Lumen\Testing\DatabaseMigrations;
use Tests\Feature\TestCaseFeature;

class AuthGenerateControllerTest extends TestCaseFeature
{
    use DatabaseMigrations;

    /**
     * @covers \App\Domains\Auth\Http\Controllers\AuthGenerateController::__construct
     * @covers \App\Domains\Auth\Http\Controllers\AuthGenerateController::process
     * @covers \App\Domains\Auth\Businesses\AuthGenerateBusiness::process
     * @covers \App\Domains\Auth\Businesses\AuthGenerateBusiness::generateToken
     * @covers \App\Domains\Auth\Businesses\AuthGenerateBusiness::getFromToken
     * @covers \App\Utils\FixPathUtil::fixPath
     */
    public function testAuthenticate()
    {
        $body = [
            'token' => '32c5a206ee876f4c6e1c483457561dbed02a531a89b380c3298bb131a844ac3c',
            'secret' => 'a1c5930d778e632c6684945ca15bcf3c752d17502d4cfbd1184024be6de14540',
        ];

        $this->json('POST', '/auth/generate', $body);

        $response = json_decode($this->response->getContent(), true);

        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertNotNull($response['data']['token']);
        $this->assertNotNull($response['data']['valid_until']);
    }

    /**
     * @covers \App\Domains\Auth\Http\Controllers\AuthGenerateController::__construct
     * @covers \App\Domains\Auth\Http\Controllers\AuthGenerateController::process
     * @covers \App\Domains\Auth\Businesses\AuthGenerateBusiness::process
     * @covers \App\Domains\Auth\Businesses\AuthGenerateBusiness::generateToken
     * @covers \App\Domains\Auth\Businesses\AuthGenerateBusiness::getFromToken
     * @covers \App\Utils\FixPathUtil::fixPath
     */
    public function testAuthenticateInvalidCredencials()
    {
        config(['tokens.data' => null]);

        $body = [
            'token' => 'c50683082e1c741e81aba9246e63095e2e5a19b7ea296f3dcb06f557b9f626e8',
            'secret' => '5c6eec9722d3e008afb301d3d6b18bf3ef2008230910f995b590d322635b8adc',
        ];

        $this->json('POST', '/auth/generate', $body);

        $response = json_decode($this->response->getContent(), true);

        $this->assertEquals(401, $this->response->getStatusCode());
        $this->assertEquals('Invalid credentials', $response['message']);
    }
}
