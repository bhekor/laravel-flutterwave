# Flutterwave Package for Laravel 7+ above

[![Latest Version on Packagist][ico-version]][link-packagist]
[![Software License][ico-license]](LICENSE.md)
[![Total Downloads][ico-downloads]][link-downloads]


> Implement Flutterwave Rave payment gateway easily with Laravel

- Go to [Flutterwave](https://dashboard.flutterwave.com/dashboard/settings/apis) to get your public and private key

To get the latest version of this Flutterwave package, simply use composer

```php
composer require bhekor/laravel-flutterwave
```

Then publish the configuration file using this command:
```php
php artisan vendor:publish --provider="Bhekor\LaravelFlutterwave\FlutterwaveServiceProvider"
```

Open your .env file and add your public key, secret key, environment variable and logo url like so:

```php
FLW_PUBLIC_KEY=FLWPUBK-xxxxxxxxxxxxxxxxxxxxx-X
FLW_SECRET_KEY=FLWSECK-xxxxxxxxxxxxxxxxxxxxx-X
FLW_SECRET_HASH='awesome-project'
```

- **FLW_PUBLIC_KEY** - This is the api public key gotten from your dashboard (compulsory)

- **FLW_SECRET_KEY** - This is the api secret key gotten from your dashboard (compulsory)

- **FLW_SECRET_HASH** - This is the secret hash for your webhook

`welcome.blade.php`
```html
<h3>Buy Movie Tickets N500.00</h3>
<form method="POST" action="{{ route('pay') }}" id="paymentForm">
    {{ csrf_field() }}

    <input name="name" placeholder="Name" />
    <input name="email" type="email" placeholder="Your Email" />
    <input name="phone" type="tel" placeholder="Phone number" />

    <input type="submit" value="Buy" />
</form>
```

Setup your controller to handle the routes. `FlutterwaveController`. Then import the Flutterwave facade

```php

<?php

namespace App\Http\Controllers;

use Bhekor\LaravelFlutterwave\Facades\Flutterwave;

class FlutterwaveController extends Controller
{
    /**
     * Initialize Rave payment process
     * @return void
     */
    public function initialize()
    {
        //This generates a payment reference
        $reference = Flutterwave::generateReference();

        // Enter the details of the payment
        $data = [
            'payment_options' => 'card,banktransfer',
            'amount' => 500,
            'email' => request()->email,
            'tx_ref' => $reference,
            'currency' => "NGN",
            'redirect_url' => route('callback'),
            'customer' => [
                'email' => request()->email,
                "phone_number" => request()->phone,
                "name" => request()->name
            ],

            "customizations" => [
                "title" => 'Movie Ticket',
                "description" => "20th October"
            ]
        ];

        $payment = Flutterwave::initializePayment($data);


        if ($payment['status'] !== 'success') {
            // notify something went wrong
            return;
        }

        return redirect($payment['data']['link']);
    }

    /**
     * Obtain Rave callback information
     * @return void
     */
    public function callback()
    {
        
        $status = request()->status;

        //if payment is successful
        if ($status ==  'successful') {
        
        $transactionID = Flutterwave::getTransactionIDFromCallback();
        $data = Flutterwave::verifyTransaction($transactionID);

        dd($data);
        }
        elseif ($status ==  'cancelled'){
            //Put desired action/code after transaction has been cancelled here
        }
        else{
            //Put desired action/code after transaction has failed here
        }
        // Get the transaction from your DB using the transaction reference (txref)
        // Check if you have previously given value for the transaction. If you have, redirect to your successpage else, continue
        // Confirm that the currency on your db transaction is equal to the returned currency
        // Confirm that the db transaction amount is equal to the returned amount
        // Update the db transaction record (including parameters that didn't exist before the transaction is completed. for audit purpose)
        // Give value for the transaction
        // Update the transaction to note that you have given value for the transaction
        // You can also redirect to your success page from here

    }
}
```
Setup the Routes
```php
// The page that displays the payment form
Route::get('/', function () {
    return view('welcome');
});
// The route that the button calls to initialize payment
Route::post('/pay', [FlutterwaveController::class, 'initialize'])->name('pay');
// The callback url after a payment
Route::get('/rave/callback', [FlutterwaveController::class, 'callback'])->name('callback');
```


## Documentation

 A friendly documentation from a package that this was created from can be found [here](https://laravelrave.netlify.com/)

<!-- 
## Credits

- [Oluwole Adebiyi (Flamez)][link-author]
- [Emmanuel Okeke](https://github.com/emmanix2002) -->

## Contributing
Please feel free to fork this package and contribute by submitting a pull request to enhance the functionalities. I will appreciate that a lot. Also please add your name to the credits.

Kindly [follow me on twitter](https://twitter.com/bhekor)!

## Features

The current features have been implemented

- Payment
- Verification
- Transfers
- Banks
- Beneficiaries

I will be working on this next
- Tokenized Charge
- Pre Auth Charge

> If there are features you need urgently, I will be willing to prioritize them, please reach out to my twitter account
## License

The MIT License (MIT). Please see [License File](LICENSE.md) for more information.

[ico-version]: https://img.shields.io/packagist/v/bhekor/laravel-flutterwave?style=for-the-badge
[ico-license]: https://img.shields.io/github/license/bhekor/laravel-flutterwave?style=for-the-badge
[ico-downloads]: https://img.shields.io/packagist/dt/bhekor/laravel-flutterwave?style=for-the-badge

[ico-travis]: https://travis-ci.org/toondaey/laravelrave.svg?branch=master
[ico-scrutinizer]: https://img.shields.io/scrutinizer/coverage/g/kingflamez/laravelrave.svg?style=flat-square
[ico-code-quality]: https://scrutinizer-ci.com/g/toondaey/laravelrave/badges/quality-score.png?b=master
[ico-code-intelligence]: https://sscrutinizer-ci.com/g/toondaey/laravelrave/badges/code-intelligence.svg?b=master
[ico-coverage]: https://scrutinizesr-ci.com/g/toondaey/laravelrave/badges/coverage.png?b=master

[link-packagist]: https://packagist.org/packages/bhekor/laravelflutterwave
[link-travis]: https://travis-ci.org/toondaey/laravelflutterwave
[link-scrutinizer]: https://scrutinizer-ci.com/g/bhekor/laravelflutterwave/code-structure
[link-code-quality]: https://scrutinizer-ci.com/g/toondaey/laravelflutterwave/?branch=master
[link-downloads]: https://packagist.org/packages/bhekor/laravelflutterwave
[link-author]: https://github.com/bhekor
[link-contributors]: ../../contributors
[link-coverage]: https://scrutinizer-ci.com/g/toondaey/laravelflutterwave/?branch=master
[link-code-intelligence]: https://scrutinizer-ci.com/code-intelligence