<?php

namespace App\Http\Validators;

use App\Exceptions\Custom\ValidationException;
use Illuminate\Validation\Factory;
use Mockery;
use PHPUnit\Framework\TestCase;

class BaseValidatorTest extends TestCase
{
    /**
     * @covers \App\Http\Validators\BaseValidator::validate
     */
    public function testBaseValidator()
    {
        $data = [
            'token' => 'token_teste',
            'secret' => 'secret_teste',
        ];

        $rules = [
            'token' => 'required|string',
            'secret' => 'required|string',
        ];

        $factoryMock = Mockery::mock(Factory::class)
            ->shouldReceive('fails')
            ->withNoArgs()
            ->andReturn(false)
            ->shouldReceive('messages')
            ->withNoArgs()
            ->never()
            ->andReturnSelf()
            ->shouldReceive('toArray')
            ->withNoArgs()
            ->never()
            ->andReturn(
                [
                    'token' => 'Is required',
                    'secret' => 'Is required'
                ]
            )
            ->getMock();

        $baseValidator = Mockery::mock(BaseValidator::class)
            ->makePartial();
        $baseValidator->shouldReceive('validator')
            ->with($data, $rules)
            ->andReturn($factoryMock)
            ->shouldReceive('getRules')
            ->withNoArgs()
            ->andReturn($rules);

        $validator = $baseValidator->validate($data);

        $this->assertEquals(
            [
                'token',
                'secret'
            ],
            $validator
        );
    }

    /**
     * @covers \App\Http\Validators\BaseValidator::validate
     */
    public function testBaseValidatorException()
    {
        $data = [
            'teste' => 'teste_teste',
        ];

        $rules = [
            'token' => 'required|string',
            'secret' => 'required|string',
        ];

        $factoryMock = Mockery::mock(Factory::class)
            ->shouldReceive('fails')
            ->withNoArgs()
            ->andReturn(true)
            ->shouldReceive('messages')
            ->withNoArgs()
            ->once()
            ->andReturnSelf()
            ->shouldReceive('toArray')
            ->withNoArgs()
            ->once()
            ->andReturn(
                [
                    'token' => 'Is required',
                    'secret' => 'Is required'
                ]
            )
            ->getMock();

        $baseValidator = Mockery::mock(BaseValidator::class)
            ->makePartial();
        $baseValidator->shouldReceive('validator')
            ->with($data, $rules)
            ->andReturn($factoryMock)
            ->shouldReceive('getRules')
            ->withNoArgs()
            ->andReturn($rules);

        $this->expectExceptionObject(
            new ValidationException(
                [
                    'token' => 'Is required',
                    'secret' => 'Is required'
                ],
                422
            )
        );

        $validator = $baseValidator->validate($data);
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
