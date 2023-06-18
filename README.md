# Laravel Budpay

Laravel Budpay is a package that provides integration with the Budpay payment gateway for Laravel applications.

## Installation

You can install the Laravel Budpay package via Composer by running the following command:

```bash
composer require devmohy/laravel-budpay
```

## Configuration

After installing the package, you need to publish the configuration file. Run the following command:

```bash
php artisan vendor:publish --provider="Devmohy\Budpay\BudpayServiceProvider"
```

This will create a `budpay.php` configuration file in the `config` directory of your Laravel application. Open the file and set your Budpay secret key:

```php
<?php

return [

    /**
     * Public Key From Budpay Dashboard
     *
     */
    'publicKey' => getenv('BUDPAY_PUBLIC_KEY'),

    /**
     * Secret Key From Budpay Dashboard
     *
     */
    'secretKey' => getenv('BUDPAY_SECRET_KEY'),

];
```

## Usage

Open your .env file and add your public key, secret key, merchant email and payment url like so:

```php
BUDPAY_PUBLIC_KEY=xxxxxxxxxxxxx
BUDPAY_SECRET_KEY=xxxxxxxxxxxxx
BUDPAY_PAYMENT_URL=https://api.budpay.com/api
```

Set up routes and controller methods like so:

```php
// Laravel 8
Route::get('/payment/callback', [App\Http\Controllers\PaymentController::class, 'handleGatewayCallback']);
```

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Redirect;
use Budpay;

class PaymentController extends Controller
{

    /**
     * Redirect the User to Budpay Payment Page
     * @return Url
     */
    public function redirectToGateway()
    {
        try{
            return Budpay::getAuthorizationUrl()->redirectNow();
        }catch(\Exception $e) {
            return Redirect::back()->withMessage(['msg'=>'The budpay token has expired. Please refresh the page and try again.', 'type'=>'error']);
        }        
    }

    /**
     * Obtain Budpay payment information
     * @return void
     */
    public function handleGatewayCallback()
    {
        $paymentDetails = Budpay::getPaymentData();

        dd($paymentDetails);
        // Now you have the payment details,
        // you can then redirect or do whatever you want
    }
}
```

The Laravel Budpay package offers several methods to facilitate integration with the Budpay payment gateway in Laravel applications. Here are the key methods provided by the package:
```php
/*
This convenient method handles the behind-the-scenes tasks of sending a POST request with the form data to the Budpay API. It takes care of all the necessary steps, including obtaining the authorization URL and redirecting the user to the Budpay Payment Page. We've abstracted away all the complexities, allowing you to focus on your coding tasks without worrying about these implementation details. So go ahead, enjoy your coding journey while we handle the rest!
*/
Budpay::getAuthorizationUrl()->redirectNow();

/**
 * This method gets all the transactions that have occurred
 * @returns array
 */
Budpay::getAllTransactions();
```

A sample form will look like so:

```html
<form method="POST" action="{{ route('pay') }}" accept-charset="UTF-8" class="form-horizontal" role="form">
    <div class="row" style="margin-bottom:40px;">
        <div class="col-md-8 col-md-offset-2">
            <p>
                <div>
                    Donjazzy's Burger
                    ₦ 2,950
                </div>
            </p>
            <input type="hidden" name="email" value="yayahmohammed@gmail.com"> {{-- required --}}
            <input type="hidden" name="orderID" value="345">
            <input type="hidden" name="amount" value="800"> {{-- required in kobo --}}
            <input type="hidden" name="quantity" value="3">
            <input type="hidden" name="currency" value="NGN">
            <p>
                <button class="btn btn-success btn-lg btn-block" type="submit" value="Pay Now!">
                    <i class="fa fa-plus-circle fa-lg"></i> Pay Now!
                </button>
            </p>
        </div>
    </div>
</form>
```


## Contributing

Please feel free to fork this package and contribute by submitting a pull request to enhance the functionalities.

## How can I thank you?

Why not star the github repo? I'd love the attention! Why not share the link for this repository on Twitter or HackerNews? Spread the word!

Don't forget to [follow me on twitter](https://twitter.com/devmohy)!

Thanks!
Mohammed Yayah.


## License
Laravel Budpay is open-source software licensed under the MIT License (MIT) [License File](LICENSE.md)
