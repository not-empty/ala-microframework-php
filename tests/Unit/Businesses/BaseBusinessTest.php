<?php

namespace App\Business;

use App\Businesses\BaseBusiness;
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

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
