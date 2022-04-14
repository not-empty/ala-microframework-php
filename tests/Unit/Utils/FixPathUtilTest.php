<?php

namespace App\Repositories;

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

        $fixedPath = $fixPath->fixPath('//draft/test/01EQDQW71FJZ1WDHDSYHFZDMV1');

        $this->assertEquals('/draft/test/01EQDQW71FJZ1WDHDSYHFZDMV1', $fixedPath);
    }
}
