<?php

namespace App\Http\Middlewares;

use Mockery;
use PHPUnit\Framework\TestCase;

class CorsMiddlewareTest extends TestCase
{
    /**
     * @covers \App\Http\Middlewares\Cors::handle
     */
    public function testCorsWhenIsOptionsRequest()
    {
        $requestMock = Mockery::mock(Request::class)
            ->shouldReceive('isMethod')
            ->with('OPTIONS')
            ->andReturn(true)
            ->getMock();

        $middleware = new Cors();

        $response = $middleware->handle($requestMock, function () {
        });

        $data = json_encode(['method' => 'OPTIONS']);
        $headers = $response->headers->all();

        $this->assertEquals($response->getData(), $data);
        $this->assertEquals($headers['access-control-allow-origin'][0], '*');
        $this->assertEquals($headers['access-control-allow-methods'][0], 'POST, GET, OPTIONS, PUT, DELETE, PATCH');
        $this->assertEquals($headers['access-control-allow-credentials'][0], 'true');
        $this->assertEquals($headers['access-control-max-age'][0], '86400');
        $this->assertEquals(
            $headers['access-control-allow-headers'][0],
            'Content-Type, Accept, Accept-Language, Authorization, X-Requested-With, Context, Suffix'
        );
    }

    /**
     * @covers \App\Http\Middlewares\Cors::handle
     */
    public function testHandle()
    {
        $requestMock = Mockery::mock(Request::class)
            ->shouldReceive('isMethod')
            ->with('OPTIONS')
            ->andReturn(false)
            ->shouldReceive('header')
            ->times(5)
            ->andReturn(true)
            ->getMock();

        $middleware = new Cors();

        $response = $middleware->handle($requestMock, function ($response) use ($requestMock) {
            $this->assertNotNull($response);
            return $requestMock;
        });
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
