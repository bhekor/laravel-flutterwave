<?php

namespace Tests\Unit;

use Carbon\Carbon;
use Tests\TestCase;
use ReflectionClass;
use ReflectionProperty;
use Bhekor\LaravelFlutterwave\Flutterwave;
use Tests\Stubs\PaymentEventHandler;
use Tests\Concerns\ExtractProperties;

class UnitTests extends TestCase
{

    use ExtractProperties;

    /**
     * Tests if app returns \Bhekor\LaravelFlutterwave\Flutterwave if called with ailas.
     *
     * @test
     * @return \Bhekor\LaravelFlutterwave\Flutterwave
     */
    function initiateRaveFromApp()
    {

        $flutterwave = $this->app->make("laravelflutterwave");

        $this->assertTrue($flutterwave instanceof Flutterwave);

        return $flutterwave;
    }

    /**
     * Test Rave initiallizes with default values;.
     *
     * @test
     *
     * @depends initiateRaveFromApp
     * @param \Bhekor\LaravelFlutterwave\Flutterwave $rave
     * @return void
     * @throws \ReflectionException
     */
    function initializeWithDefaultValues(Flutterwave $flutterwave)
    {

        $reflector = new ReflectionClass($flutterwave);

        $methods = $reflector->getProperties(ReflectionProperty::IS_PROTECTED);

        foreach ($methods as $method) {
            if ($method->getName() == 'baseUrl') $baseUrl = $method;
            if ($method->getName() == 'secretKey') $secretKey = $method;
            if ($method->getName() == 'publicKey') $publicKey = $method;
        };

        $baseUrl->setAccessible(true);
        $publicKey->setAccessible(true);
        $secretKey->setAccessible(true);

        $this->assertEquals($this->app->config->get("flutterwave.secretKey"), $secretKey->getValue($flutterwave));
        $this->assertEquals($this->app->config->get("flutterwave.publicKey"), $publicKey->getValue($flutterwave));
        $this->assertEquals(
            "https://api.flutterwave.com/v3",
            $baseUrl->getValue($flutterwave)
        );
    }

    /**
     * Tests if transaction reference is generated.
     *
     * @test
     * @depends initiateRaveFromApp
     * @param Flutterwave $flutterwave
     * @return void
     */
    function generateReference(Flutterwave $flutterwave)
    {

        $ref = $flutterwave->generateReference();

        $prefix = 'flw';

        $this->assertRegExp("/^{$prefix}_\w{13}$/", $ref);
    }

    /**
     * Testing if keys are modified using setkeys.
     *
     * @test
     * @depends initiateRaveFromApp
     * @param Flutterwave $flutterwave
     * @return void
     * @throws \ReflectionException
     */
    function settingKeys(Flutterwave $flutterwave)
    {

        $newPublicKey = "public_key";
        $newSecretKey = "secret_key";
        $flutterwave->setKeys($newPublicKey, $newSecretKey);
        $reflector = new ReflectionClass($flutterwave);
        $reflector = $reflector->getProperties(ReflectionProperty::IS_PROTECTED);

        $keys = array_map(function ($value) use ($flutterwave, $newPublicKey, $newSecretKey) {
            $name = $value->getName();
            if ($name === "publicKey" || $name === "secretKey") {
                $value->setAccessible(true);
                $key = $value->getValue($flutterwave);
                $this->assertEquals(${"new" . ucfirst($name)}, $key);
            }
        }, $reflector);
    }
}