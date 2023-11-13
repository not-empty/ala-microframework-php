<?php

namespace App\Http\Parameters;

use \Mockery;
use PHPUnit\Framework\TestCase;

class BaseParametersTest extends TestCase
{
    /**
     * @covers \App\Http\Parameters\BaseParameters::__construct
     */
    public function testCreateParameters()
    {
        $baseParameters = new BaseParameters([]);
        $this->assertInstanceOf(BaseParameters::class, $baseParameters);
    }

    /**
     * @covers \App\Http\Parameters\BaseParameters::fields
     */
    public function testFields()
    {
        $fields = 'person_id, user_name';

        $baseParameters = new BaseParameters([
            'fields' => $fields,
        ]);

        $helpers = $baseParameters->fields();

        $this->assertEquals($helpers, []);
    }

    /**
     * @covers \App\Http\Parameters\BaseParameters::fields
     */
    public function testFieldsWithIntersect()
    {
        $fields = 'id, person_id, user_name';

        $baseParameters = new BaseParameters([
            'fields' => $fields,
        ]);

        $baseParameters->fields = ['id'];

        $helpers = $baseParameters->fields();

        $this->assertEquals($helpers, ['id']);
    }

    /**
     * @covers \App\Http\Parameters\BaseParameters::fields
     */
    public function testFieldsAndReturnDefaultFields()
    {
        $fields = 'id, person_id, user_name';

        $baseParameters = new BaseParameters([
            'fields' => null,
        ]);

        $baseParameters->fields = ['id'];

        $helpers = $baseParameters->fields();

        $this->assertEquals($helpers, ['id']);
    }

    /**
     * @covers \App\Http\Parameters\BaseParameters::order
     */
    public function testOrder()
    {
        $fields = 'person_id';

        $baseParameters = new BaseParameters([
            'order' => $fields,
        ]);

        $baseParameters->order = ['person_id'];

        $helpers = $baseParameters->order();

        $this->assertEquals($helpers, 'person_id');
    }

    /**
     * @covers \App\Http\Parameters\BaseParameters::order
     */
    public function testWithNoOrder()
    {
        $baseParameters = new BaseParameters([
            'order' => null,
        ]);

        $baseParameters->order = ['person_id'];

        $helpers = $baseParameters->order();

        $this->assertEquals($helpers, 'person_id');
    }

    /**
     * @covers \App\Http\Parameters\BaseParameters::order
     */
    public function testOrderAndIsNotPermited()
    {
        $fields = 'person_id';

        $baseParameters = new BaseParameters([
            'order' => $fields,
        ]);

        $baseParameters->order = ['id'];

        $helpers = $baseParameters->order();

        $this->assertEquals($helpers, 'id');
    }

    /**
     * @covers \App\Http\Parameters\BaseParameters::classification
     */
    public function testClassEmpty()
    {
        $baseParameters = new BaseParameters([
            'class' => null,
        ]);

        $helpers = $baseParameters->classification();

        $this->assertEquals($helpers, 'desc');
    }

    /**
     * @covers \App\Http\Parameters\BaseParameters::classification
     */
    public function testClassification()
    {
        $baseParameters = new BaseParameters([
            'class' => 'asc',
        ]);

        $helpers = $baseParameters->classification();

        $this->assertEquals($helpers, 'asc');
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
