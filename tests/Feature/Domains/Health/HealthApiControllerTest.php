<?php

namespace Tests\Feature\Domains\Health;

use Tests\Feature\TestCaseFeature;

class HealthApiControllerTest extends TestCaseFeature
{
    /**
     * @covers \App\Domains\Health\Http\Controllers\HealthApiController::process
     */
    public function testHealthApi()
    {
        $this->json('GET', '/health', []);

        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertArrayHasKey('status', json_decode($this->response->getContent(), true));
        $this->assertArrayHasKey('version', json_decode($this->response->getContent(), true));
    }

    /**
     * @covers \App\Domains\Health\Http\Controllers\HealthApiController::process
     */
    public function testHealthApiUsingOtherUri()
    {
        $this->json('GET', '/', []);

        $this->assertEquals(200, $this->response->getStatusCode());
        $this->assertArrayHasKey('status', json_decode($this->response->getContent(), true));
        $this->assertArrayHasKey('version', json_decode($this->response->getContent(), true));
    }
}
