<?php

namespace App\Http\Middlewares;

use Ulid\Ulid;
use Mockery;
use PHPUnit\Framework\TestCase;

class RequestStartTest extends TestCase
{
    /**
     * @covers \App\Http\Middlewares\RequestStart::__construct
     */
    public function testCreateMiddleware()
    {
        $ulidSpy = Mockery::spy(Ulid::class);
        $requestStart = new RequestStart($ulidSpy);
        $this->assertInstanceOf(RequestStart::class, $requestStart);
    }

    /**
     * @covers \App\Http\Middlewares\RequestStart::handle
     */
    public function testHandle()
    {
        $requestSpy = Mockery::spy(Request::class);

        $ulidMock = Mockery::mock(Ulid::class)
            ->shouldReceive('generate')
            ->withNoArgs()
            ->andReturn('hashulid')
            ->getMock();

        $requestStart = new RequestStart($ulidMock);
        $handle = $requestStart->handle($requestSpy, function ($request) {
            $this->assertEquals('hashulid', $request->requestId);
            $this->assertNotNull($request->startProfile);
        });
    }
    
    protected function tearDown(): void
    {
        Mockery::close();
    }
}
