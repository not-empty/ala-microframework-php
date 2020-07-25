<?php

namespace App\Http\Filters;

use App\Constants\FiltersTypesConstants;
use Illuminate\Validation\Factory;
use Mockery;
use PHPUnit\Framework\TestCase;

class BaseFiltersTest extends TestCase
{
    /**
     * @covers \App\Http\Filters\BaseFilters::__construct
     */
    public function testCreateFilters()
    {
        $baseFilters = new BaseFilters([]);
        $this->assertInstanceOf(BaseFilters::class, $baseFilters);
    }

    /**
     * @covers \App\Http\Filters\BaseFilters::getValidFilters
     * @covers \App\Http\Filters\BaseFilters::isValidFilterType
     * @covers \App\Http\Filters\BaseFilters::isValidFilterData
     * @covers \App\Http\Filters\BaseFilters::typeOnly
     * @covers \App\Http\Filters\BaseFilters::valueOnly
     * @covers \App\Http\Filters\BaseFilters::validateFilter
     */
    public function testGetValidFilters()
    {
        $filter = [
            'user_name' => [
                'validate' => 'string|min:3',
                'permissions' => [
                    FiltersTypesConstants::FILTER_EQUAL,
                    FiltersTypesConstants::FILTER_NOT_EQUAL,
                    FiltersTypesConstants::FILTER_LIKE,
                ],
            ],
        ];

        $factoryMock = Mockery::mock(Factory::class)
            ->shouldReceive('fails')
            ->withNoArgs()
            ->andReturn(false)
            ->getMock();

        $data = ['user_name' => 'lik,test'];
        $baseFilters = Mockery::mock(BaseFilters::class, [$data])->makePartial();
        $baseFilters->shouldReceive('validate')
            ->with(['user_name' => 'test'], ['user_name' => 'string|min:3'])
            ->andReturn($factoryMock);

        $baseFilters->filter = $filter;

        $filters = $baseFilters->getValidFilters();
        $this->assertEquals([
            'user_name' => [
                'type' => 'lik',
                'data' => 'test',
            ]
        ], $filters);
    }

    /**
     * @covers \App\Http\Filters\BaseFilters::getValidFilters
     * @covers \App\Http\Filters\BaseFilters::isValidFilterType
     * @covers \App\Http\Filters\BaseFilters::isValidFilterData
     * @covers \App\Http\Filters\BaseFilters::valueOnly
     * @covers \App\Http\Filters\BaseFilters::validateFilter
     */
    public function testGetValidFiltersAndFailValidate()
    {
        $filter = [
            'user_name' => [
                'validate' => 'string|min:3',
                'permissions' => [
                    FiltersTypesConstants::FILTER_EQUAL,
                    FiltersTypesConstants::FILTER_NOT_EQUAL,
                    FiltersTypesConstants::FILTER_LIKE,
                ],
            ],
        ];

        $factoryMock = Mockery::mock(Factory::class)
            ->shouldReceive('fails')
            ->withNoArgs()
            ->andReturn(true)
            ->getMock();

        $data = ['user_name' => 'lik,test'];
        $baseFilters = Mockery::mock(BaseFilters::class, [$data])->makePartial();
        $baseFilters->shouldReceive('validate')
            ->with(['user_name' => 'test'], ['user_name' => 'string|min:3'])
            ->andReturn($factoryMock);

        $baseFilters->filter = $filter;

        $this->expectExceptionObject(new \Exception('Invalid filter value to field user_name', 422));

        $filters = $baseFilters->getValidFilters();
    }

    /**
     * @covers \App\Http\Filters\BaseFilters::getValidFilters
     * @covers \App\Http\Filters\BaseFilters::isValidFilterType
     * @covers \App\Http\Filters\BaseFilters::typeOnly
     */
    public function testGetValidFiltersAndNotFilterPermissions()
    {
        $filter = [
            'user_name' => [
                'validate' => 'string|min:3',
                'permissions' => [
                    FiltersTypesConstants::FILTER_EQUAL,
                    FiltersTypesConstants::FILTER_NOT_EQUAL,
                ],
            ],
        ];

        $factoryMock = Mockery::mock(Factory::class)
            ->shouldReceive('fails')
            ->withNoArgs()
            ->andReturn(true)
            ->getMock();

        $data = ['user_name' => 'lik,test'];
        $baseFilters = Mockery::mock(BaseFilters::class, [$data])->makePartial();
        $baseFilters->shouldReceive('validate')
            ->with(['user_name' => 'test'], ['user_name' => 'string|min:3'])
            ->never()
            ->andReturn($factoryMock);

        $baseFilters->filter = $filter;

        $this->expectExceptionObject(new \Exception('Filter type lik not allowed to field user_name', 422));

        $filters = $baseFilters->getValidFilters();
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
