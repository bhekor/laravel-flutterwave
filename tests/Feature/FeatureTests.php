<?php

namespace Tests\Feature;

use Tests\TestCase;
use Tests\Stubs\Request;
use Bhekor\LaravelFlutterwave\Flutterwave;
use Tests\Stubs\PaymentEventHandler;
use Tests\Concerns\ExtractProperties;

class FeatureTests extends TestCase
{

    use ExtractProperties;

    /**
     * Test if parameters are set on setData.
     *
     * @test
     * @return void
     */
    function getParams()
    {

        $request = new Request();
        $request->subaccounts = [];
        $request->meta = [];
        $request->ref = false;
        $request->logo = false;
        $request->title = false;
        $request->paymentplan = false;
        $request->phonenumber = '080232382382';
        $request->payment_method = 'online';
        $request->pay_button_text = 'Pay Now';
        $flutterwave = new flutterwave();
        $flutterwave->initialize("http://localhost");

        $this->assertTrue($flutterwave instanceof Flutterwave);

        return $flutterwave;
    }

    /**
     * Test if hash is created.
     *
     * @test
     * @depends getParams
     * @param Flutterwave $rave
     * @return void
     * @throws \ReflectionException
     */
    function creatingCheckSum(Flutterwave $flutterwave)
    {

        #$rave = $rave->createReferenceNumber();
        $publicKey = "FLWPUBK-MOCK-1cf610974690c2560cb4c36f4921244a-X";
        $flutterwave->initialize("http://localhost");
        $flutterwave = $flutterwave->createCheckSum('http://localhost');

        $hash = $this->extractProperty($flutterwave, "integrityHash");

        $this->assertEquals(64, strlen($hash["value"]));

        return $flutterwave;
    }

    /**
     * Testing payment.
     *
     * @test
     * @depends creatingCheckSum
     * @param Rave $rave
     * @return void
     */
    function paymentInitialize(Flutterwave $flutterwave)
    {

        $response = $flutterwave->eventHandler(new PaymentEventHandler)->initialize("http://localhost");

        $values = json_decode($response, true);

        $class = $this->data["class"];

        $this->assertArrayHasKey("meta", $values);
        $this->assertArrayHasKey("txref", $values);
        $this->assertArrayHasKey("amount", $values);
        $this->assertArrayHasKey("country", $values);
        $this->assertArrayHasKey("currency", $values);
        $this->assertArrayHasKey("PBFPubKey", $values);
        $this->assertArrayHasKey("custom_logo", $values);
        $this->assertArrayHasKey("redirect_url", $values);
        $this->assertArrayHasKey("data-integrity_hash", $values);
        $this->assertArrayHasKey("payment_method", $values);
        $this->assertArrayHasKey("customer_phone", $values);
        $this->assertArrayHasKey("customer_email", $values);
        $this->assertArrayHasKey("pay_button_text", $values);
        $this->assertArrayHasKey("customer_lastname", $values);
        $this->assertArrayHasKey("custom_description", $values);
        $this->assertArrayHasKey("customer_firstname", $values);
    }

    /**
     * Test if proper actions are taken when payment is cancelled.
     *
     * @test
     * @return void
     */
    function paymentCancelledTest()
    {
        $request = new Request();
        $request->cancelled = true;
        $flutterwave = new Flutterwave();
        $flutterwave = $flutterwave->createReferenceNumber();
        $ref = $flutterwave->getReferenceNumber();

        // This section tests if json is returned when no handler is set.

        $returned = $flutterwave->paymentCanceled($ref);

        $this->assertTrue(is_object($returned));

        // Tests if json has certain keys when payment is cancelled.

        $returned = json_decode(json_encode($returned), true);

        $this->assertArrayHasKey("data", $returned);
        $this->assertArrayHasKey("txRef", $returned['data']);
        $this->assertArrayHasKey("status", $returned['data']);

        // This section tests if instance of flutterwave is returned when a handler is set.
        $flutterwave->eventHandler(new PaymentEventHandler)->paymentCanceled($ref);

        $this->assertEquals(Flutterwave::class, get_class($flutterwave));

        return $ref;
    }

    /**
     * Testing requery transactions.
     *
     * @test
     * @depends paymentCancelledTest
     * @dataProvider providesResponse
     * @runInSeparateProcess
     * @preserveGlobalState disabled
     * @param  string $ref txref
     */
    //    function requeryTransactionTransactionTest($mResponse, $ref) {
    //
    //        $data = [
    //            'txref' => $ref,
    //            'SECKEY' => $this->app->config->get("secretKey"),
    //            'last_attempt' => '1'
    //            // 'only_successful' => '1'
    //        ];
    //
    //        $url = "https://rave-api-v2.herokuapp.com";
    //        $headers = ['Content-Type' => 'application/json'];
    //
    //        $data = Body::json($data);
    //        $response = json_encode($mResponse);
    //
    //        $decodedResponse = json_decode($response);
    //
    //        $mRequest = $this->m->mock("alias:Unirest\Request");
    //        $mRequest->shouldReceive("post")
    //                 ->andReturn($decodedResponse);
    //
    //        $rave = new Rave(new Request(['cancelled' => true]), $mRequest, new Body);
    //
    //        $raveResponse = $rave->verifyTransaction($ref);
    //
    //        // Test if data is returned when no handler.
    //        // $this->assertEquals($decodedResponse->body->status, $raveResponse->status);
    //
    //        $this->setProperty($rave, "handler", new PaymentEventHandler);
    //
    //        $raveResponse = $rave->verifyTransaction($ref);
    //
    //        // Tests that an instance of rave is returned when a handler is set
    //        $this->assertTrue(Rave::class, get_class($raveResponse));
    //    }

    /**
     * Provides data for all events of requery transaction.
     *
     * @return array
     */
    function providesResponse()
    {

        return [
            [
                [
                    "body" => [
                        "status" => "unknown",
                        "data" => ["status", "unknown"]
                    ],
                ],
            ],
            [
                [
                    "body" => [
                        "status" => "success",
                    ],
                ]
            ],
            [
                [
                    "body" => [
                        "status" => "success",
                        "data" => [
                            "status" => "failed"
                        ]
                    ],
                ]
            ],
            [
                [
                    "body" => [
                        "status" => "success",
                        "data" => [
                            "status" => "successful"
                        ]
                    ],
                ]
            ]
        ];
    }
}