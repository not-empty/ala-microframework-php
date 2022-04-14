<?php

namespace App\Repositories;

use App\Exceptions\Custom\RouteNotFoundException;
use App\Utils\FixPathUtil;
use PHPUnit\Framework\TestCase;

class FixPathUtilTest extends TestCase
{
    /**
     * @covers \App\Utils\FixPathUtil::fixPath
     */
    public function testFixPathWithRootPath()
    {
        $fixPath = new FixPathUtil();

        $fixedPath = $fixPath->fixPath('/');

        $this->assertEquals('/', $fixedPath);
    }

    /**
     * @covers \App\Utils\FixPathUtil::fixPath
     */
    public function testFixPathWithHealthPath()
    {
        $fixPath = new FixPathUtil();

        $fixedPath = $fixPath->fixPath('/health');

        $this->assertEquals('/health', $fixedPath);
    }

    /**
     * @covers \App\Utils\FixPathUtil::fixPath
     */
    public function testFixPathWithUlidInThirdPosition()
    {
        $fixPath = new FixPathUtil();

        $fixedPath = $fixPath->fixPath('/draft/test/01EQDQW71FJZ1WDHDSYHFZDMV1');

        $this->assertEquals('/draft/test', $fixedPath);
    }

    /**
     * @covers \App\Utils\FixPathUtil::fixPath
     */
    public function testFixPathWithInvalidRoute()
    {
        $fixPath = new FixPathUtil();

        $this->expectExceptionObject(new RouteNotFoundException('Route not found', 404));

        $fixPath->fixPath('//draft/test');
    }
}
