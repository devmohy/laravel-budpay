<?php

/*
 * This file is part of Laravel Budpay package.
 *
 * (c) Mohammed Yayah <yayahnmohammed@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Devmohy\Budpay;

use Exception;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Config;
use Devmohy\Budpay\Exceptions\PaymentVerificationFailedException;

class Budpay
{
  /**
   * Transaction Verification Successful
   */
  const VS = 'Verification successful';

  /**
   *  Invalid Transaction reference
   */
  const ITF = "Invalid transaction reference";

  /**
   * Issue Secret Key from your Budpay Dashboard
   * @var string
   */
  protected $secretKey;

  /**
   * Instance of Http
   * @var Http
   */
  protected $http;

  /**
   *  Response from requests made to Budpay
   * @var mixed
   */
  protected $response;

  /**
   * Budpay API base Url
   * @var string
   */
  protected $baseUrl;

  /**
   * Authorization Url - Budpay payment page
   * @var string
   */
  protected $authorizationUrl;

  public function __construct()
  {
    $http = new Http;
    $this->setKey();
    $this->setBaseUrl();
    $this->http = $http::withToken($this->secretKey);
  }

  /**
   * Get Base Url from Budpay config file
   */
  public function setBaseUrl()
  {
    $this->baseUrl = "https://api.budpay.com/api";
  }

  /**
   * Get secret key from Budpay config file
   */
  public function setKey()
  {
    //       Test Secret Key:
    // sk_test_3xd7ybrhumna6sn9kumn3eqljal6lghshb3uiu9
    // Test Public Key:
    // pk_test_jv8yueg1ycwkowviqw91swbewglvziwde9idpd
    $this->secretKey = "sk_test_3xd7ybrhumna6sn9kumn3eqljal6lghshb3uiu9";
  }


  /**
   * Initiate a payment request using Budpay Standard Checkout
   * Included the option to pass the payload to this method for situations
   * when the payload is built on the fly (not passed to the controller from a view)
   * @return Budpay
   */

  public function standardCheckout($data = null)
  {
    if ($data == null) {
      $quantity = intval(request()->quantity ?? 1);
      $data = array_filter([
        "amount" => intval(request()->amount) * $quantity,
        "reference" => request()->reference,
        "email" => request()->email,
        "callback" => request()->callback_url,
        "currency" => (request()->currency != ""  ? request()->currency : "NGN")
      ]);
    }
    $this->setHttpResponse('/v2/transaction/initialize', 'POST', $data);
    return $this->getResponse();
  }
  /**

   * Initiate a payment request using Budpay Server To Server Bank Transfer Checkout
   * Included the option to pass the payload to this method for situations
   * when the payload is built on the fly (not passed to the controller from a view)
   * @return Budpay
   */

  public function serverToServerBankTransferCheckout($data = null)
  {
    if ($data == null) {
      $quantity = intval(request()->quantity ?? 1);
      $data = array_filter([
        "amount" => intval(request()->amount) * $quantity,
        "reference" => request()->reference,
        "email" => request()->email,
        "name" => request()->name,
        "currency" => (request()->currency != "" ? request()->currency : "NGN")
      ]);
    }
    $this->setHttpResponse('/s2s/banktransfer/initialize', 'POST', $data);
    return $this->getResponse();
  }

  /**

   * Initiate a payment request to Budpay using server to server v2
   * Included the option to pass the payload to this method for situations
   * when the payload is built on the fly (not passed to the controller from a view)
   * @return Budpay
   */

  public function serverToServerV2(array $data = null)
  {
    if ($data == null) {
      $quantity = intval(request()->quantity ?? 1);
      $data = array_filter([
        "amount" => intval(request()->amount) * $quantity,
        "reference" => request()->reference,
        "email" => request()->email,
        "card" => request()->name,
        "currency" => (request()->currency != "" ? request()->currency : "NGN")
      ]);
    }
    $encryption = hash_hmac('sha512', json_encode($data), $this->secretKey);
    $this->http->withHeaders(["Encryption" => $encryption]);
    $this->setHttpResponse('/s2s/v2/transaction/initialize', 'POST', $data);
    return $this->getResponse();
  }

  /**

   * Initiate a payment request to Budpay
   * Included the option to pass the payload to this method for situations
   * when the payload is built on the fly (not passed to the controller from a view)
   * @return Budpay
   */

  public function encrypt(array $data = null)
  {
    $this->setHttpResponse(' /test/encryption', 'POST', $data);
    return $this->getResponse();
  }

  /**
   * Fetch transaction with $transaction_id
   * @return Budpay
   */

  public function fetchTransaction($transaction_id = null)
  {
    $transactionRef = $transaction_id ?? request()->query('trxid');
    $relativeUrl = "/v2/transaction/:{$transactionRef}";
    $this->response = $this->http->get($this->baseUrl . $relativeUrl, []);
    return $this->getResponse();
  }

  /**
   * Feature notworking yet
   * Initiate a payment request to Budpay
   * @param array $data
   * @return Budpay
   */
  public function requestPayment($data = null)
  {
    $this->setHttpResponse('/v2/request_payment', 'POST', $data);
    return $this->getResponse();
  }

  /**

   * Create customer on budpay
   * Included the option to pass the payload to this method for situations
   * when the payload is built on the fly (not passed to the controller from a view)
   * @return Budpay
   */

  public function createCustomer($data = null)
  {
    if ($data == null) {
      $data = array_filter([
        "email" => request()->email,
        "first_name" => request()->first_name,
        "last_name" => request()->last_name,
        "phone" => request()->phone,
        "metadata" => request()->metadata,
      ]);
    }
    $this->setHttpResponse('/v2/customer', 'POST', $data);
    return $this->getResponse();
  }

  /**

   * Initiate a payment request to Budpay
   * Included the option to pass the payload to this method for situations
   * when the payload is built on the fly (not passed to the controller from a view)
   * @return Budpay
   */

  public function createDedicatedVirtualAccount($data = null)
  {
    if ($data == null) {
      $data = array_filter([
        "email" => request()->email,
        "first_name" => request()->first_name,
        "last_name" => request()->last_name,
        "phone" => request()->phone,
        "customer" => request()->customer,
      ]);
    }
    $this->setHttpResponse('/v2/dedicated_virtual_account', 'POST', $data);
    return $this->getResponse();
  }

  /**

   * Initiate a payment request to Budpay
   * Included the option to pass the payload to this method for situations
   * when the payload is built on the fly (not passed to the controller from a view)
   * @return Budpay
   */

  public function listDedicatedVirtualAccount()
  {
    $this->setHttpResponse('/v2/list_dedicated_accounts', 'GET', []);
    return $this->getResponse();
  }

  /**

   * Initiate a payment request to Budpay
   * Included the option to pass the payload to this method for situations
   * when the payload is built on the fly (not passed to the controller from a view)
   * @return Budpay
   */

   public function fetchDedicatedVirtualAccountById($account_id = null)
   {
     $accountId = $account_id ?? request()->query('account_id');
 
     $relativeUrl = "/v2/dedicated_account/:{$accountId}";
 
     $this->response = $this->http->get($this->baseUrl . $relativeUrl, []);
     return $this->getResponse();
   }

  /**

   * Initiate a payment request to Budpay
   * Included the option to pass the payload to this method for situations
   * when the payload is built on the fly (not passed to the controller from a view)
   * @return Budpay
   */

  public function createPaymentLink($data = null)
  {
    if ($data == null) {
      $data = array_filter([
        "amount" => request()->amount,
        "currency" => request()->currency,
        "last_name" => request()->last_name,
        "name" => request()->name,
        "description" => request()->description,
        "redirect" => request()->description,
      ]);
    }
    $this->setHttpResponse('/v2/create_payment_link', 'POST', $data);
    return $this->getResponse();
  }


  /**

   * Initiate a payment request to Budpay
   * Included the option to pass the payload to this method for situations
   * when the payload is built on the fly (not passed to the controller from a view)
   * @return Budpay
   */

  public function getSettlements()
  {
    $this->setHttpResponse('/v2/settlement', '  GET', []);
    return $this->getResponse();

}
  /**

   * Initiate a payment request to Budpay
   * Included the option to pass the payload to this method for situations
   * when the payload is built on the fly (not passed to the controller from a view)
   * @return Budpay
   */

  public function getSettlementsByBatch($batch_id)
  {
    $batchId = $batch_id ?? request()->query('account_id');
 
    $relativeUrl = "/v2/settlement/:{$batchId}";

    $this->response = $this->http->get($this->baseUrl . $relativeUrl, []);
    return $this->getResponse();
  }

  /**

   * Initiate a payment request to Budpay
   * Included the option to pass the payload to this method for situations
   * when the payload is built on the fly (not passed to the controller from a view)
   * @return Budpay
   */

  public function createRefund($data = null)
  {
    if ($data == null) {
      $data = array_filter([
        "reference" => request()->reference,
      ]);
    }
    $this->setHttpResponse('/v2/refund', 'POST', $data);
    return $this->getResponse();
  }

  /**

   * Initiate a payment request to Budpay
   * Included the option to pass the payload to this method for situations
   * when the payload is built on the fly (not passed to the controller from a view)
   * @return Budpay
   */

  public function listRefunds()
  {
    $this->setHttpResponse('/v2/refund', 'GET', []);
    return $this->getResponse();
  }

  /**

   * Initiate a payment request to Budpay
   * Included the option to pass the payload to this method for situations
   * when the payload is built on the fly (not passed to the controller from a view)
   * @return Budpay
   */

  public function fetchRefund($data = null)
  {
    $this->setHttpResponse('/v2/refund/status', 'POST', $data);
    return $this->getResponse();
  }

  /**
   * Get all the transactions that have happened overtime
   * @return array
   */
  public function bankLists()
  {
    return $this->setHttpResponse("/v2/transaction", 'GET', [])->getData();
  }

  /**
   * Get all the transactions that have happened overtime
   * @return array
   */
  public function singlePayout()
  {
    return $this->setHttpResponse("/v2/transaction", 'GET', [])->getData();
  }

  /**
   * Get all the transactions that have happened overtime
   * @return array
   */
  public function bulkPayout()
  {
    return $this->setHttpResponse("/v2/transaction", 'GET', [])->getData();
  }

  /**
   * Get all the transactions that have happened overtime
   * @return array
   */
  public function verifyPayout()
  {
    return $this->setHttpResponse("/v2/transaction", 'GET', [])->getData();
  }
  /**
   * Get all the transactions that have happened overtime
   * @return array
   */
  public function payoutFee()
  {
    return $this->setHttpResponse("/v2/transaction", 'GET', [])->getData();
  }
  /**
   * Get all the transactions that have happened overtime
   * @return array
   */
  public function walletBalance()
  {
    return $this->setHttpResponse("/v2/transaction", 'GET', [])->getData();
  }
  /**
   * Get all the transactions that have happened overtime
   * @return array
   */
  public function walletTransactions()
  {
    return $this->setHttpResponse("/v2/transaction", 'GET', [])->getData();
  }


  /**
   * @param string $relativeUrl
   * @param string $method
   * @param array $body
   * @return Budpay
   * @throws IsNullException
   */
  private function setHttpResponse($relativeUrl, $method, $body = [])
  {
    if (is_null($method)) {
      throw new Exception("Empty method not allowed");
    }

    $this->response = $this->http->{strtolower($method)}($this->baseUrl . $relativeUrl, $body);

    return $this;
  }

  /**
   * Get the authorization url from the callback response
   * @return Budpay
   */
  public function getAuthorizationUrl($data = null)
  {
    $this->standardCheckout($data);

    $this->url = $this->getResponse()['data']['authorization_url'];

    return $this;
  }

  /**
   * Get the authorization callback response
   * In situations where Laravel serves as an backend for a detached UI, the api cannot redirect
   * and might need to take different actions based on the success or not of the transaction
   * @return array
   */
  public function getAuthorizationResponse($data)
  {
    $this->standardCheckout($data);

    $this->url = $this->getResponse()['data']['authorization_url'];

    return $this->getResponse();
  }

  /**
   * Hit Budpay Gateway to Verify that the transaction is valid
   */
  private function verifyTransactionAtGateway($trxRef= null)
  {
    $transactionRef = $trxRef ?? request()->query('trxref');

    $relativeUrl = "/v2/transaction/verify/:{$transactionRef}";

    $this->response = $this->http->get($this->baseUrl . $relativeUrl, []);
  }

  /**
   * True or false condition whether the transaction is verified
   * @return boolean
   */
  public function isTransactionVerificationValid($trxRef = null)
  {
    $this->verifyTransactionAtGateway($trxRef);

    $result = $this->getResponse()['message'];

    switch ($result) {
      case self::VS:
        $validate = true;
        break;
      case self::ITF:
        $validate = false;
        break;
      default:
        $validate = false;
        break;
    }

    return $validate;
  }

  /**
   * Get Payment details if the transaction was verified successfully
   * @return json
   * @throws PaymentVerificationFailedException
   */
  public function getPaymentData($trxRef = null)
  {
    if ($this->isTransactionVerificationValid($trxRef)) {
      return $this->getResponse();
    } else {
      throw new Exception("Invalid Transaction Reference");
    }
  }

  /**
   * Fluent method to redirect to Budpay Payment Page
   */
  public function redirectNow()
  {
    return redirect($this->url);
  }

  /**
   * Get Access code from transaction callback respose
   * @return string
   */
  public function getAccessCode()
  {
    return $this->getResponse()['data']['access_code'];
  }

  /**
   * Get all the transactions that have happened overtime
   * @return array
   */
  public function getAllTransactions()
  {
    return $this->setHttpResponse("/v2/transaction", 'GET', [])->getData();
  }

  /**
   * Get all the transactions that have happened overtime
   * @return array
   */
  public function airtimeProviders()
  {
    return $this->setHttpResponse("/v2/transaction", 'GET', [])->getData();
  }

  /**
   * Get all the transactions that have happened overtime
   * @return array
   */
  public function airtimeTopUp()
  {
    return $this->setHttpResponse("/v2/transaction", 'GET', [])->getData();
  }

  /**
   * Get all the transactions that have happened overtime
   * @return array
   */
  public function internetProviders()
  {
    return $this->setHttpResponse("/v2/transaction", 'GET', [])->getData();
  }

  /**
   * Get all the transactions that have happened overtime
   * @return array
   */
  public function internetDataPlans()
  {
    return $this->setHttpResponse("/v2/transaction", 'GET', [])->getData();
  }

  /**
   * Get all the transactions that have happened overtime
   * @return array
   */
  public function internetDataPurchase()
  {
    return $this->setHttpResponse("/v2/transaction", 'GET', [])->getData();
  }

  /**
   * Get all the transactions that have happened overtime
   * @return array
   */
  public function tvProviders()
  {
    return $this->setHttpResponse("/v2/transaction", 'GET', [])->getData();
  }

  /**
   * Get all the transactions that have happened overtime
   * @return array
   */
  public function tvProviderPackages()
  {
    return $this->setHttpResponse("/v2/transaction", 'GET', [])->getData();
  }


  /**
   * Get all the transactions that have happened overtime
   * @return array
   */
  public function tvValidate()
  {
    return $this->setHttpResponse("/v2/transaction", 'GET', [])->getData();
  }

  /**
   * Get all the transactions that have happened overtime
   * @return array
   */
  public function tvSubscription()
  {
    return $this->setHttpResponse("/v2/transaction", 'GET', [])->getData();
  }

  /**
   * Get all the transactions that have happened overtime
   * @return array
   */
  public function electricityProviders()
  {
    return $this->setHttpResponse("/v2/transaction", 'GET', [])->getData();
  }

  /**
   * Get all the transactions that have happened overtime
   * @return array
   */
  public function electricityValidate()
  {
    return $this->setHttpResponse("/v2/transaction", 'GET', [])->getData();
  }

  /**
   * Get all the transactions that have happened overtime
   * @return array
   */
  public function electricityRecharge()
  {
    return $this->setHttpResponse("/v2/transaction", 'GET', [])->getData();
  }


  /**
   * Get all the transactions that have happened overtime
   * @return array
   */
  public function accountNameVerify()
  {
    return $this->setHttpResponse("/v2/transaction", 'GET', [])->getData();
  }

  /**
   * Get all the transactions that have happened overtime
   * @return array
   */
  public function verifyBVN()
  {
    return $this->setHttpResponse("/v2/transaction", 'GET', [])->getData();
  }

  /**
   * Get the whole response from a get operation
   * @return array
   */
  private function getResponse()
  {
    return $this->response->json();
  }

  /**
   * Get the data response from a get operation
   * @return array
   */
  private function getData()
  {
    return $this->getResponse()['data'];
  }
}
