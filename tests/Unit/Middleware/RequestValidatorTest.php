<?php

namespace App\Http\Middlewares;

use App\TestFiles\TestListValidator;
use Mockery;
use PHPUnit\Framework\TestCase;

class RequestValidatorTest extends TestCase
{
    /**
     * @covers \App\Http\Middlewares\RequestValidator::handle
     */
    public function testHandle()
    {
        $routeDetails = [
            'uses' => 'TestListController@process',
            'validator' => 'App\TestFiles\TestListValidator',
            'parameters' => 'App\TestFiles\TestParameters',
        ];

        $testListValidatorMock = Mockery::mock(TestListValidator::class)
            ->shouldReceive('validate')
            ->with([])
            ->once()
            ->andReturn(true)
            ->getMock();

        $requestMock = Mockery::mock(Request::class)
            ->shouldReceive('all')
            ->withNoArgs()
            ->andReturn([])
            ->getMock();

        $requestValidator = Mockery::mock(RequestValidator::class)->makePartial();
        $requestValidator->shouldReceive('getRouteDetails')
            ->with($requestMock)
            ->andReturn($routeDetails)
            ->shouldReceive('newClass')
            ->once()
            ->with($routeDetails['validator'])
            ->andReturn($testListValidatorMock);

        $requestValidator->handle($requestMock, function ($request) {
            $this->assertEquals(true, $request->validFields);
        });
    }

    /**
     * @covers \App\Http\Middlewares\RequestValidator::handle
     */
    public function testHandleAndNoValidator()
    {
        $routeDetails = [
            'uses' => 'TestListController@process',
            'parameters' => 'App\TestFiles\TestParameters',
        ];

        $testListValidatorMock = Mockery::mock(TestListValidator::class)
            ->shouldReceive('validate')
            ->with([])
            ->never()
            ->andReturn(true)
            ->getMock();

        $requestMock = Mockery::mock(Request::class)
            ->shouldReceive('all')
            ->withNoArgs()
            ->never()
            ->andReturn([])
            ->getMock();

        $requestValidator = Mockery::mock(RequestValidator::class)->makePartial();
        $requestValidator->shouldReceive('getRouteDetails')
            ->with($requestMock)
            ->andReturn($routeDetails)
            ->shouldReceive('newClass')
            ->never()
            ->andReturn($testListValidatorMock);

        $requestValidator->handle($requestMock, function ($request) {
            $this->assertNull($request->validFields ?? null);
        });
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
