<?php

namespace App\Business;

use App\Businesses\BaseBusiness;
use App\Repositories\BaseRepository;
use Mockery;
use PHPUnit\Framework\TestCase;

class BaseBusinessTest extends TestCase
{
    /**
     * @covers \App\Businesses\BaseBusiness::decodeJsonFields
     */
    public function testDecodeJsonFields()
    {
        $data = [
            'id' => 'id',
            'name' => 'test',
            'convert' => '{"name":"test"}',
        ];
        $expected = [
            'id' => 'id',
            'name' => 'test',
            'convert' => [
                'name' => 'test',
            ],
        ];
        $fields = [
            'convert',
        ];

        $BaseBusiness = $this->getMockForAbstractClass(
            BaseBusiness::class
        );

        $result = $BaseBusiness->decodeJsonFields(
            $data,
            $fields
        );

        $this->assertEquals($result, $expected);
    }

    /**
     * @covers \App\Businesses\BaseBusiness::setRepositoryTable
     */
    public function testSetRepositoryTable()
    {
        $baseRepositoryMock = Mockery::mock(
            BaseRepository::class
        )->shouldReceive('getTable')
            ->once()
            ->andReturn('table')
            ->shouldReceive('setTable')
            ->with('table_test')
            ->once()
            ->getMock();

        $config = app()->make('config');
        $config->set('app.db_suffix', '_test');

        $BaseBusiness = $this->getMockForAbstractClass(
            BaseBusiness::class
        );

        $result = $BaseBusiness->setRepositoryTable(
            $baseRepositoryMock
        );

        $this->assertEquals($result, true);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
