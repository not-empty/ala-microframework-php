<?php

namespace App\Http\Middlewares;

use App\Utils\FixPathUtil;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;
use Mockery;
use PHPUnit\Framework\TestCase;
use Intouch\Newrelic\Newrelic;

class NewRelicLumenTest extends TestCase
{
    /**
     * @covers \App\Http\Middlewares\NewRelicLumen::__construct
     */
    public function testCreateMiddleware()
    {
        $newRelic = Mockery::spy(Newrelic::class);
        $fixPathUtil = Mockery::spy(FixPathUtil::class);
        $newRelicLumen = new NewRelicLumen($newRelic, $fixPathUtil);

        $this->assertInstanceOf(NewRelicLumen::class, $newRelicLumen);
    }

    /**
     * @covers \App\Http\Middlewares\NewRelicLumen::handle
     */
    public function testHandleSetNewRelicAppName()
    {
        Config::set('newRelic.override_ini', true);
        Config::set('newRelic.application_name', 'eos-test');
        Config::set('newRelic.license', 'test');

        $request = Mockery::mock(Request::class);
        $request->shouldReceive('getPathInfo')
            ->withNoArgs()
            ->once()
            ->andReturn('/draft/test/01EQDQW71FJZ1WDHDSYHFZDMV1');

        $request->shouldReceive('getRequestUri')
            ->withNoArgs()
            ->once()
            ->andReturn('/draft/test/01EQDQW71FJZ1WDHDSYHFZDMV1');

        $request->newRelic = null;

        $newRelic = Mockery::mock(Newrelic::class);
        $newRelic->shouldReceive('nameTransaction')
            ->with('/draft/test')
            ->once()
            ->andReturnNull();

        $newRelic->shouldReceive('addCustomParameter')
            ->with('requestUri', '/draft/test/01EQDQW71FJZ1WDHDSYHFZDMV1')
            ->once()
            ->andReturnNull();

        $newRelic->shouldReceive('setAppName')
            ->with('eos-test', 'test', true)
            ->once()
            ->andReturnNull();

        $fixPathUtil = Mockery::mock(FixPathUtil::class);

        $fixPathUtil->shouldReceive('fixPath')
            ->with('/draft/test/01EQDQW71FJZ1WDHDSYHFZDMV1')
            ->once()
            ->andReturn('/draft/test');

        $newRelicLumen = new NewRelicLumen($newRelic, $fixPathUtil);

        $newRelicLumen->handle(
            $request,
            function ($request) {
                $this->assertNotNull($request->newRelic);
            }
        );
    }

    /**
     * @covers \App\Http\Middlewares\NewRelicLumen::handle
     */
    public function testHandleDoNotSetNewRelicAppName()
    {
        Config::set('newRelic.override_ini', false);

        $request = Mockery::mock(Request::class);
        $request->shouldReceive('getRequestUri')
            ->withNoArgs()
            ->once()
            ->andReturn('/draft/test/01EQDQW71FJZ1WDHDSYHFZDMV1');

        $request->shouldReceive('getPathInfo')
            ->withNoArgs()
            ->once()
            ->andReturn('/draft/test/01EQDQW71FJZ1WDHDSYHFZDMV1');
        $request->newRelic = null;

        $newRelic = Mockery::mock(Newrelic::class);
        $newRelic->shouldReceive('nameTransaction')
            ->with('/draft/test')
            ->once()
            ->andReturnNull();

        $newRelic->shouldReceive('addCustomParameter')
            ->with('requestUri', '/draft/test/01EQDQW71FJZ1WDHDSYHFZDMV1')
            ->once()
            ->andReturnNull();

        $fixPathUtil = Mockery::mock(FixPathUtil::class);

        $fixPathUtil->shouldReceive('fixPath')
            ->with('/draft/test/01EQDQW71FJZ1WDHDSYHFZDMV1')
            ->once()
            ->andReturn('/draft/test');

        $newRelicLumen = new NewRelicLumen($newRelic, $fixPathUtil);

        $newRelicLumen->handle(
            $request,
            function ($request) {
                $this->assertNotNull($request->newRelic);
            }
        );
    }

    protected function tearDown(): void
    {
        Mockery::close();
    }
}
