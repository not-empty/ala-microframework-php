<?php

namespace App\Http\Middlewares;

use Laravel\Lumen\Routing\Router;
use Mockery;
use PHPUnit\Framework\TestCase;

class BaseRequestTest extends TestCase
{
    /**
     * @covers \App\Http\Middlewares\BaseRequest::getRoutes
     */
    public function testGetRoutes()
    {
        $response = [
            'uses' => 'TestListController@process',
            'validator' => 'App\TestFiles\TestListValidator',
            'parameters' => 'App\TestFiles\TestParameters',
        ];

        $routerMock = Mockery::mock(Router::class)
            ->shouldReceive('getRoutes')
            ->withNoArgs()
            ->andReturn([
                'get/test/list' => [
                    'action' => $response,
                ],
            ])
            ->getMock();

        $requestMock = Mockery::mock(Request::class)
            ->shouldReceive('method')
            ->withNoArgs()
            ->andReturn('get')
            ->shouldReceive('getPathInfo')
            ->withNoArgs()
            ->andReturn('/test/list')
            ->getMock();

        $baseRequest = Mockery::mock(BaseRequest::class)->makePartial();
        $baseRequest->shouldReceive('newApp')
            ->withNoArgs()
            ->andReturn((object) ['router' => $routerMock]);

        $middleware = $baseRequest->getRoutes();

        $this->assertEquals($response, $middleware['get/test/list']['action']);
    }

    /**
     * @covers \App\Http\Middlewares\BaseRequest::getRouteDetails
     */
    public function testGetRouteDetails()
    {
        $response = [
            'uses' => 'TestListController@process',
            'validator' => 'App\TestFiles\TestListValidator',
            'parameters' => 'App\TestFiles\TestParameters',
        ];

        $routerMock = Mockery::mock(Router::class)
            ->shouldReceive('getRoutes')
            ->withNoArgs()
            ->andReturn([
                'get/test/list' => [
                    'action' => $response,
                ],
            ])
            ->getMock();

        $requestMock = Mockery::mock(Request::class)
            ->shouldReceive('method')
            ->withNoArgs()
            ->andReturn('get')
            ->shouldReceive('getPathInfo')
            ->withNoArgs()
            ->andReturn('/test/list')
            ->getMock();

        $baseRequest = Mockery::mock(BaseRequest::class)->makePartial();
        $baseRequest->shouldReceive('newApp')
            ->withNoArgs()
            ->andReturn((object) ['router' => $routerMock]);

        $middleware = $baseRequest->getRouteDetails($requestMock);

        $this->assertEquals($response, $middleware);
    }

    /**
     * @covers \App\Http\Middlewares\BaseRequest::getRouteDetails
     */
    public function testGetAndNotHasRouteDetails()
    {
        $routerMock = Mockery::mock(Router::class)
            ->shouldReceive('getRoutes')
            ->withNoArgs()
            ->andReturn([
                'get/test/list' => [
                    'action' => null,
                ],
            ])
            ->getMock();

        $requestMock = Mockery::mock(Request::class)
            ->shouldReceive('method')
            ->withNoArgs()
            ->andReturn('get')
            ->shouldReceive('getPathInfo')
            ->withNoArgs()
            ->andReturn('/test/list')
            ->getMock();

        $baseRequest = Mockery::mock(BaseRequest::class)->makePartial();
        $baseRequest->shouldReceive('newApp')
            ->withNoArgs()
            ->andReturn((object) ['router' => $routerMock]);

        $middleware = $baseRequest->getRouteDetails($requestMock);

        $this->assertNull($middleware);
    }

    /**
     * @covers \App\Http\Middlewares\BaseRequest::fixKey
     */
    public function testFixKeyValid()
    {
        $goodKey = '/tag/dead_detail';

        $baseRequest = Mockery::mock(BaseRequest::class)->makePartial();
        $key = $baseRequest->fixKey($goodKey);

        $this->assertEquals($key, $goodKey);
    }

    /**
     * @covers \App\Http\Middlewares\BaseRequest::fixKey
     */
    public function testFixKeyInvalid()
    {
        $badKey = '/tag/dead_detail/{id:[0-9A-Z]{26}';
        $goodKey = '/tag/dead_detail';

        $baseRequest = Mockery::mock(BaseRequest::class)->makePartial();
        $key = $baseRequest->fixKey($badKey);

        $this->assertEquals($key, $goodKey);
    }

    /**
     * @covers \App\Http\Middlewares\BaseRequest::fixPath
     */
    public function testFixPathUsingOnlyPipe()
    {
        $goodPath = '/';

        $baseRequest = Mockery::mock(BaseRequest::class)->makePartial();
        $path = $baseRequest->fixPath($goodPath);

        $this->assertEquals($path, $goodPath);
    }

    /**
     * @covers \App\Http\Middlewares\BaseRequest::fixPath
     */
    public function testFixPath()
    {
        $goodPath = '/test/dead_detail';

        $baseRequest = Mockery::mock(BaseRequest::class)->makePartial();
        $path = $baseRequest->fixPath($goodPath);

        $this->assertEquals($path, $goodPath);
    }

    /**
     * @covers \App\Http\Middlewares\BaseRequest::fixPath
     */
    public function testFixPathInvalid()
    {
        $badPath = '/test/dead_detail/01E492KQX6BW62YEA45SGWRXYQ';
        $goodPath = '/test/dead_detail';

        $baseRequest = Mockery::mock(BaseRequest::class)->makePartial();
        $path = $baseRequest->fixPath($badPath);

        $this->assertEquals($path, $goodPath);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
