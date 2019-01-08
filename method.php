<?php

require('config.php');
require('razorpay-php/Razorpay.php');
session_start();

// Create the Razorpay Order

use Razorpay\Api\Api;

$api = new Api($keyId, $keySecret);



$curl = curl_init();
// Set some options - we are passing in a useragent too here
curl_setopt_array($curl, array(
    CURLOPT_RETURNTRANSFER => 1,
    CURLOPT_URL => 'https://rzp_test_ujyrjCmMoiIisD@api.razorpay.com/v1/methods',
    CURLOPT_GET => 1,
));
// Send the request & save response to $resp
echo $resp = curl_exec($curl);

//Fetch payment_id from the response
//$resp_pay_id = json_decode($resp, true)['razorpay_payment_id'];