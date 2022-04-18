<?php

namespace App\Http\Middlewares;

use App\Exceptions\Custom\SuffixRequiredException;
use App\Http\Middlewares\SuffixTable;
use Mockery;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\HeaderBag;

class SuffixTableTest extends TestCase
{
    /**
     * @covers \App\Http\Middlewares\SuffixTable::handle
     */
    public function testNotUsingSuffix()
    {
        $middleware = new SuffixTable;

        $middleware = Mockery::mock(SuffixTable::class)->makePartial();
        $middleware->shouldReceive('setTableAttribute')
            ->with('')
            ->andReturn(true)
            ->shouldReceive('getConfig')
            ->with('db_use_suffix')
            ->andReturn(false)
            ->getMock();

        $requestMock = (object) [
            'db_suffix' => '',
        ];

        $middleware->handle($requestMock, function ($request) {
            $this->assertEquals($request->db_suffix, '');
        });
    }

    /**
     * @covers \App\Http\Middlewares\SuffixTable::handle
     */
    public function testInvalisdSuffix()
    {

        $headerBagMock = Mockery::mock(HeaderBag::class)
            ->shouldReceive('get')
            ->with('Suffix')
            ->andReturn('@test')
            ->getMock();

        $middleware = new SuffixTable;

        $middleware = Mockery::mock(SuffixTable::class)->makePartial();
        $middleware->shouldReceive('setTableAttribute')
            ->with('')
            ->andReturn(true)
            ->shouldReceive('getConfig')
            ->with('db_use_suffix')
            ->andReturn(true)
            ->shouldReceive('getConfig')
            ->with('db_suffix_list')
            ->andReturn('')
            ->shouldReceive('getConfig')
            ->with('db_suffix_required')
            ->andReturn(false)
            ->getMock();

        $requestMock = (object) [
            'headers' => $headerBagMock,
            'db_suffix' => '',
        ];

        $middleware->handle($requestMock, function ($request) {
            $this->assertEquals($request->db_suffix, '');
        });
    }

    /**
     * @covers \App\Http\Middlewares\SuffixTable::handle
     */
    public function testdSuffixNotInList()
    {

        $headerBagMock = Mockery::mock(HeaderBag::class)
            ->shouldReceive('get')
            ->with('Suffix')
            ->andReturn('_prod')
            ->getMock();

        $middleware = new SuffixTable;

        $middleware = Mockery::mock(SuffixTable::class)->makePartial();
        $middleware->shouldReceive('setTableAttribute')
            ->with('')
            ->andReturn(true)
            ->shouldReceive('getConfig')
            ->with('db_use_suffix')
            ->andReturn(true)
            ->shouldReceive('getConfig')
            ->with('db_suffix_list')
            ->andReturn('_test')
            ->shouldReceive('getConfig')
            ->with('db_suffix_required')
            ->andReturn(false)
            ->getMock();

        $requestMock = (object) [
            'headers' => $headerBagMock,
            'db_suffix' => '',
        ];

        $middleware->handle($requestMock, function ($request) {
            $this->assertEquals($request->db_suffix, '');
        });
    }

    /**
     * @covers \App\Http\Middlewares\SuffixTable::handle
     */
    public function testdSuffixRequiredException()
    {
        $headerBagMock = Mockery::mock(HeaderBag::class)
            ->shouldReceive('get')
            ->with('Suffix')
            ->andReturn('_prod')
            ->getMock();

        $middleware = new SuffixTable;

        $middleware = Mockery::mock(SuffixTable::class)->makePartial();
        $middleware->shouldReceive('setTableAttribute')
            ->with('')
            ->andReturn(true)
            ->shouldReceive('getConfig')
            ->with('db_use_suffix')
            ->andReturn(true)
            ->shouldReceive('getConfig')
            ->with('db_suffix_list')
            ->andReturn('_test')
            ->shouldReceive('getConfig')
            ->with('db_suffix_required')
            ->andReturn(true)
            ->getMock();

        $requestMock = (object) [
            'headers' => $headerBagMock,
            'db_suffix' => '',
        ];

        $this->expectExceptionObject(new SuffixRequiredException('Suffix header is missing or invalid.', 400));

        $middleware->handle($requestMock, function () {
        });
    }

    /**
     * @covers \App\Http\Middlewares\SuffixTable::handle
     */
    public function testdSuffixCorrect()
    {
        $headerBagMock = Mockery::mock(HeaderBag::class)
            ->shouldReceive('get')
            ->with('Suffix')
            ->andReturn('_test')
            ->getMock();

        $middleware = new SuffixTable;

        $middleware = Mockery::mock(SuffixTable::class)->makePartial();
        $middleware->shouldReceive('setTableAttribute')
            ->with('')
            ->andReturn(true)
            ->shouldReceive('getConfig')
            ->with('db_use_suffix')
            ->andReturn(true)
            ->shouldReceive('getConfig')
            ->with('db_suffix_list')
            ->andReturn('_test')
            ->shouldReceive('getConfig')
            ->with('db_suffix_required')
            ->andReturn(true)
            ->getMock();

        $requestMock = (object) [
            'headers' => $headerBagMock,
            'db_suffix' => '_test',
        ];

        $middleware->handle($requestMock, function ($request) {
            $this->assertEquals($request->db_suffix, '_test');
        });
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
