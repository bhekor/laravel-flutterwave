<?php

namespace Tests\Unit;

use Tests\TestCase;
use KingFlamez\Rave\Rave;

class FlutterwaveServiceProviderTests extends TestCase
{
    /**
     * Tests if service provider Binds alias "laravelrave" to \KingFlamez\Rave\Rave
     *
     * @test
     */
    public function isBound()
    {
        $this->assertTrue($this->app->bound('laravelflutterwave'));
    }
    /**
     * Test if service provider returns \Flutterwave as alias for \KingFlamez\Rave\Rave
     *
     * @test
     */
    public function hasAliased()
    {
        $this->assertTrue($this->app->isAlias("Bhekor\LaravelFlutterwave\Flutterwave"));
        $this->assertEquals('laravelflutterwave', $this->app->getAlias("Bhekor\LaravelFlutterwave\Flutterwave"));
    }
}