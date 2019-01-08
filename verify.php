<?php

require('config.php');

session_start();

require('razorpay-php/Razorpay.php');
use Razorpay\Api\Api;
use Razorpay\Api\Errors\SignatureVerificationError;

$success = true;

$error = "Payment Failed";

if (empty($_POST['razorpay_payment_id']) === false)
{
    $api = new Api($keyId, $keySecret);

    try
    {
        // Please note that the razorpay order ID must
        // come from a trusted source (session here, but
        // could be database or something else)
        $attributes = array(
            'razorpay_order_id' => $_SESSION['razorpay_order_id'],
            'razorpay_payment_id' => $_POST['razorpay_payment_id'],
            'razorpay_signature' => $_POST['razorpay_signature']
        );

        $api->utility->verifyPaymentSignature($attributes);
    }
    catch(SignatureVerificationError $e)
    {
        $success = false;
        $error = 'Razorpay Error : ' . $e->getMessage();
    }
}

if ($success === true)
{
 
//Create New Order for eMandate Debit     
$orderData = [
    'receipt'         => 'Debit_1',
    'amount'          => 23.6 * 100, // 2000 rupees in paise
    'currency'        => 'INR',
    'payment_capture' => 1 // auto capture
];

$NewRazorpayOrder = $api->order->create($orderData);

$NewRazorpayOrderId = $NewRazorpayOrder['id'];

$amount = $orderData['amount'];

//Fetch token from payment_id

$payment  = $api->payment->fetch($_POST['razorpay_payment_id']);

$token = $payment['token_id'];

// Get cURL resource
$curl = curl_init();
// Set some options - we are passing in a useragent too here
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => 'https://rzp_test_ujyrjCmMoiIisD:tctebYocAriLzsMkWLbsZKvR@api.razorpay.com/v1/payments/create/recurring',
    CURLOPT_POST => 1,
    CURLOPT_POSTFIELDS => array(
        'contact'       => '9988776655',
        'email'         => 'debit@emandate.com',
        'amount'        => $amount,
        'currency'      => 'INR',
        'order_id'      => $NewRazorpayOrderId,
        'customer_id'   => $_SESSION['razorpay_customer_id'],
        'token'         => $token,                                                      
        'recurring'     => 1
    )
));
// Send the request & save response to $resp
$resp = curl_exec($curl);

//Fetch payment_id from the response
$resp_pay_id = json_decode($resp, true)['razorpay_payment_id'];


    $html = "<p>Your eMandate is successful registered</p>
             <p>Payment ID: {$_POST['razorpay_payment_id']}</p>
             <p>SIP debit has been successful initiated</p>
             <p> Debit Payment ID: $resp_pay_id</p>";
}
else
{
    $html = "<p>Your payment failed</p>
             <p>{$error}</p>";
}

echo $html;
