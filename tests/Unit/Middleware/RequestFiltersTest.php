<?php

namespace App\Http\Middlewares;

use App\Http\Filters\BaseFilters;
use Mockery;
use PHPUnit\Framework\TestCase;

class RequestFiltersTest extends TestCase
{
    /**
     * @covers \App\Http\Middlewares\RequestFilters::handle
     */
    public function testHandleWithoutFilter()
    {
        $requestSpy = Mockery::spy(Request::class);

        $middleware = Mockery::mock(RequestFilters::class)->makePartial();
        $middleware->shouldReceive('getRouteDetails')
            ->with($requestSpy)
            ->andReturn(null);

        $middleware->handle($requestSpy, function ($request) {
            $this->assertNull($request->filters);
        });
    }

    /**
     * @covers \App\Http\Middlewares\RequestFilters::handle
     * @covers \App\Http\Middlewares\RequestFilters::extractFilters
     */
    public function testHandleWithIncorrectFilterSyntax()
    {
        $requestMock = Mockery::mock(Request::class)
            ->shouldReceive('only')
            ->with(['user_name'])
            ->andReturn(['user_name' => 'lik,dim'])
            ->getMock();

        $requestMock->validFields = ['user_name'];

        $middleware = Mockery::mock(RequestFilters::class)->makePartial();
        $middleware->shouldReceive('getRouteDetails')
            ->with($requestMock)
            ->andReturn([
                'filters' => 'App\\TestFiles\\TestFilters',
                'middleware' => [
                    0 => 'start',
                    1 => 'auth',
                    2 => 'validator',
                    3 => 'filters',
                    4 => 'parameter',
                ],
            ]);

        $middleware->handle($requestMock, function ($request) {
            $this->assertNull($request->filters);
        });
    }

    /**
     * @covers \App\Http\Middlewares\RequestFilters::handle
     * @covers \App\Http\Middlewares\RequestFilters::extractFilters
     */
    public function testHandleNotValidFilter()
    {
        $baseFiltersMock = Mockery::mock(BaseFilters::class)
            ->shouldReceive('getValidFilters')
            ->withNoArgs()
            ->andReturn([])
            ->getMock();

        $requestMock = Mockery::mock(Request::class)
            ->shouldReceive('only')
            ->with(['filter_user_name'])
            ->andReturn(['filter_user_name' => 'lik,dim'])
            ->getMock();

        $requestMock->validFields = ['filter_user_name'];

        $middleware = Mockery::mock(RequestFilters::class)->makePartial();
        $middleware->shouldReceive('getRouteDetails')
            ->with($requestMock)
            ->andReturn([
                'filters' => 'App\\TestFiles\\TestFilters',
                'middleware' => [
                    0 => 'start',
                    1 => 'auth',
                    2 => 'validator',
                    3 => 'filters',
                    4 => 'parameter',
                ],
            ])
            ->shouldReceive('newClass')
            ->with('App\\TestFiles\\TestFilters', ['user_name' => 'lik,dim'])
            ->andReturn($baseFiltersMock);

        $middleware->handle($requestMock, function ($request) {
            $this->assertNull($request->filters);
        });
    }

    /**
     * @covers \App\Http\Middlewares\RequestFilters::handle
     * @covers \App\Http\Middlewares\RequestFilters::extractFilters
     */
    public function testHandle()
    {
        $baseFiltersMock = Mockery::mock(BaseFilters::class)
            ->shouldReceive('getValidFilters')
            ->withNoArgs()
            ->andReturn([
                'user_name' => [
                    'type' => 'lik',
                    'data' => 'dim',
                ]
            ])
            ->getMock();

        $requestMock = Mockery::mock(Request::class)
            ->shouldReceive('only')
            ->with(['filter_user_name'])
            ->andReturn(['filter_user_name' => 'lik,dim'])
            ->getMock();

        $requestMock->validFields = ['filter_user_name'];

        $middleware = Mockery::mock(RequestFilters::class)->makePartial();
        $middleware->shouldReceive('getRouteDetails')
            ->with($requestMock)
            ->andReturn([
                'filters' => 'App\\TestFiles\\TestFilters',
                'middleware' => [
                    0 => 'start',
                    1 => 'auth',
                    2 => 'validator',
                    3 => 'filters',
                    4 => 'parameter',
                ],
            ])
            ->shouldReceive('newClass')
            ->with('App\\TestFiles\\TestFilters', ['user_name' => 'lik,dim'])
            ->andReturn($baseFiltersMock);

        $middleware->handle($requestMock, function ($request) {
            $this->assertEquals([
                'user_name' => [
                    'type' => 'lik',
                    'data' => 'dim',
                ]
            ], $request->filters);
        });
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
