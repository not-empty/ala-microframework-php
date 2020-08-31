<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\DB;
use Laravel\Lumen\Testing\TestCase;
use ReflectionObject;

class TestCaseFeature extends TestCase
{
    protected $header;

    public function createApplication()
    {
        return require __DIR__.'/../../bootstrap/app.php';
    }

    protected function setUp(): void
    {
        parent::setUp();

        $credencials = [
            'token' => '32c5a206ee876f4c6e1c483457561dbed02a531a89b380c3298bb131a844ac3c',
            'secret' => 'a1c5930d778e632c6684945ca15bcf3c752d17502d4cfbd1184024be6de14540',
        ];

        $this->json('POST', '/auth/generate', $credencials);

        $token = json_decode($this->response->getContent(), true)['data']['token'];

        $this->header = [
            'Authorization' => $token,
            'Context' => 'app-test',
        ];

        $this->id = env('ID_FEAT_TEST', null);
        $this->idDead = env('ID_DEAD_FEAT_TEST', null);
    }

    protected function tearDown(): void
    {
        $refl = new ReflectionObject($this);
        foreach ($refl->getProperties() as $prop) {
            if (!$prop->isStatic() && 0 !== strpos($prop->getDeclaringClass()->getName(), 'PHPUnit_')) {
                $prop->setAccessible(true);
                $prop->setValue($this, null);
            }
        }
        DB::disconnect();
        parent::tearDown();
    }
}
