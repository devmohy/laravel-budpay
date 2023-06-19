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

    /**
     * Paystack Payment URL
     *
     */
    'paymentUrl' => env('BUDPAY_PAYMENT_URL'),

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
Route::post('/pay', [App\Http\Controllers\PaymentController::class, 'redirectToGateway'])->name('pay');

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
## - Accept payment

```php
/*
* This convenient method handles the behind-the-scenes tasks of sending a POST request with the * form data to the Budpay API. It takes care of all the necessary steps, including obtaining 
* the authorization URL and redirecting the user to the Budpay Payment Page. We've abstracted
* away all the complexities, allowing you to focus on your coding tasks without worrying about  * these implementation details. So go ahead, enjoy your coding journey while we handle the rest!
*/
Budpay::getAuthorizationUrl()->redirectNow();

/**
 * This method gets all the transactions that have occurred
 * @returns array
 */
Budpay::getAllTransactions();

/**
 * This method Initiate a payment request using Budpay Standard Checkout
 * Included the option to pass the payload to this method for situations
 * when the payload is built on the fly (not passed to the controller from a view)
 * @returns array
 */
Budpay::standardCheckout();

/**
 * This method Initiate a payment request using Budpay Server To Server Bank Transfer Checkout
 * Included the option to pass the payload to this method for situations
 * when the payload is built on the fly (not passed to the controller from a view)
 * @returns array
 */
Budpay::serverToServerBankTransferCheckout();

/**
 * This method Initiate a payment request to Budpay using server to server v2
 * Included the option to pass the payload to this method for situations
 * when the payload is built on the fly (not passed to the controller from a view)
 * @returns array
 */
Budpay::serverToServerV2();

/**
 * This method Fetch transaction using transaction ID
 * Included the option to pass the payload to this method for situations
 * when the payload is built on the fly (not passed to the controller from a view)
 * @returns array
 */
Budpay::fetchTransaction();


```

A sample form will look like so:

```html
<form method="POST" action="{{ route('pay') }}" accept-charset="UTF-8" class="form-horizontal" role="form">
    <div class="row" style="margin-bottom:40px;">
        <div class="col-md-8 col-md-offset-2">
            <p>
                <div>
                    Donjazzy's Burger
                    ₦ 2,400
                </div>
            </p>
            <input type="hidden" name="email" value="yayahmohammed@gmail.com"> {{-- required --}}
            <input type="hidden" name="orderID" value="ORD123">
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
## - Payment Features

```php
/**
 * Initiate a payment request to Budpay
 * Included the option to pass the payload to this method for situations
 * when the payload is built on the fly (not passed to the controller from a view)
 * @returns array
 */
Budpay::requestPayment();

/**
 * This method Create customer on budpay
 * Included the option to pass the payload to this method for situations
 * when the payload is built on the fly (not passed to the controller from a view)
 * @returns array
 */
Budpay::createCustomer();

/**
 * This method Create a dedicated virtual account and assign to a customer
 * Included the option to pass the payload to this method for situations
 * when the payload is built on the fly (not passed to the controller from a view)
 * @returns array
 */
Budpay::createDedicatedVirtualAccount();

/**
 * This method List dedicated virtual accounts
 * @returns array
 */
Budpay::listDedicatedVirtualAccount();

/**
 * This method Fetch Dedicated Virtual Account By ID
 * @returns array
 */
Budpay::fetchDedicatedVirtualAccountById();

/**
 * This method Create Payment Link
 * Included the option to pass the payload to this method for situations
 * when the payload is built on the fly (not passed to the controller from a view)
 * @returns array
 */
Budpay::createPaymentLink();

/**
 * This method Fetch Settlements
 * Included the option to pass the payload to this method for situations
 * when the payload is built on the fly (not passed to the controller from a view)
 * @returns array
 */
Budpay::getSettlements();

/**
 * This method Fetch Settlements By Batch ID
 * @returns array
 */
Budpay::getSettlementsByBatch();

/**
 * This method Create Refund
 * Included the option to pass the payload to this method for situations
 * when the payload is built on the fly (not passed to the controller from a view)
 * @returns array
 */
Budpay::createRefund();

/**
 * This method Fetch Refunds
 * Included the option to pass the payload to this method for situations
 * when the payload is built on the fly (not passed to the controller from a view)
 * @returns array
 */
Budpay::listRefunds();

/**
 * This method Fetch refund by Reference
 * @returns array
 */
Budpay::fetchRefund();
```

## - Payout

```php
/**
 * This method Fetch Banks
 * @returns array
 */
Budpay::bankLists();

/**
 * This method Initiate Transfer
 * Included the option to pass the payload to this method for situations
 * when the payload is built on the fly (not passed to the controller from a view)
 * @returns array
 */
Budpay::singlePayout();

/**
 * This method Initiate Bulk Transfer
 * "currency": "NGN",
 * "transfers": [
 *      {
 *      "amount": "200",
 *      "bank_code": "000013",
 *      "bank_name": "GUARANTY TRUST BANK",
 *      "account_number": "0050883605",
 *      "narration": "January Salary"
 *      },
 *      {
 *       "amount": "100",
 *       "bank_code": "000013",
 *          "bank_name": "GUARANTY TRUST BANK",
 *           "account_number": "0050883605",
 *          "narration": "February  Salary"
 *      },
 *   ]
 * @returns array
 */
Budpay::bulkPayout(
    ["currency" => "NGN",
    "transfers" => [
        {
            "amount" => "200",
            "bank_code" => "000013",
            "bank_name" => "GUARANTY TRUST BANK",
            "account_number" => "0050883605",
            "narration" => "January Salary"
        },
        {
            "amount" => "100",
            "bank_code" => "000013",
            "bank_name" => "GUARANTY TRUST BANK",
            "account_number" => "0050883605",
            "narration" => "February  Salary"
        },
        {
            "amount" => "100",
            "bank_code" => "000013",
            "bank_name" => "GUARANTY TRUST BANK",
            "account_number" => "0050883605",
            "narration" => "March  Salary"
        }
    ]]
);

/**
 * This method Fetch a payout record using payout reference.
 * @param $ref
 * @returns array
 */
Budpay::verifyPayout($ref);

/**
 * This method return Payout Fee (Bank Transfer Fee)
 * Included the option to pass the payload to this method for situations
 * when the payload is built on the fly (not passed to the controller from a view)
 * @returns array
 */
Budpay::payoutFee();

/**
 * This method return  Wallet balance by Currency
 * Included the option to pass the payload to this method for situations
 * when the payload is built on the fly (not passed to the controller from a view)
 * @returns array
 */
Budpay::walletBalance();

/**
 * This Wallet transactions method allows you fetch all your wallet transaction history.
 * @returns array
 */
Budpay::walletTransactions();
```
## - Bills Payment

```php
/**
 * This method Fetch all available Airtime Providers
 * @returns array
 */
Budpay::airtimeProviders();

/**
 * This method Buy Airtime
 * Included the option to pass the payload to this method for situations
 * when the payload is built on the fly (not passed to the controller from a view)
 * @returns array
 */
Budpay::airtimeTopUp();

/**
 * This method FFetch all available Internet Providers.
 * @returns array
 */
Budpay::internetProviders();

/**
 * This method Get all available Internet Data Plans
 * @returns array
 */
Budpay::internetDataPlans();

/**
 * This method Initiate a Internet Data Purchase Transaction
 * Included the option to pass the payload to this method for situations
 * when the payload is built on the fly (not passed to the controller from a view)
 * @returns array
 */
Budpay::internetDataPurchase();

/**
 * This method Get all available Tv Packages (Bouquet) of a Provider
 * @returns array
 */
Budpay::tvProviders();

/**
 * This method Get all available Tv Packages (Bouquet) of a Provider
 * @param string $provider
 * @returns array
 */
Budpay::tvProviderPackages();

/**
 * This method Perform a Tv UIC Number Validation
 * Included the option to pass the payload to this method for situations
 * when the payload is built on the fly (not passed to the controller from a view)
 * @returns array
 */
Budpay::tvValidate();

/**
 * This method Initiate a Tv Subscription Payment
 * Included the option to pass the payload to this method for situations
 * when the payload is built on the fly (not passed to the controller from a view)
 * @returns array
 */
Budpay::tvSubscription();

/**
 * This method Get all available Electricity Providers
 * @returns array
 */
Budpay::electricityProviders();

/**
 * This method Perform a Electricity Meter Number Validation
 * Included the option to pass the payload to this method for situations
 * when the payload is built on the fly (not passed to the controller from a view)
 * @returns array
 */
Budpay::electricityValidate();

/**
 * This method Initiate a Electricity Recharge Payment
 * Included the option to pass the payload to this method for situations
 * when the payload is built on the fly (not passed to the controller from a view)
 * @returns array
 */
Budpay::electricityRecharge();
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
