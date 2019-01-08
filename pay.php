<?php

require('config.php');
require('razorpay-php/Razorpay.php');
session_start();

// Create the Razorpay Order

use Razorpay\Api\Api;

$api = new Api($keyId, $keySecret);

//
// We create an razorpay order using orders api
// Docs: https://docs.razorpay.com/docs/orders
//
$orderData = [
    'receipt'         => 'SI_1',
    'amount'          => 0 * 100, // 2000 rupees in paise
    'currency'        => 'INR',
    'method'          => 'emandate',
    'payment_capture' => 1 // auto capture
];

$razorpayOrder = $api->order->create($orderData);

$razorpayOrderId = $razorpayOrder['id'];

$_SESSION['razorpay_order_id'] = $razorpayOrderId;


//We create a customer using customer API: https://razorpay.com/docs/getting-started/customers/#creating-a-customer for eMandate 

$customerData = [
    'name'          =>  $_POST['name-field'],
    'email'         => $_POST['email-field'],
    'contact'       => $_POST['contact-field'],
    'fail_existing' => 0
];


$customer = $api->customer->create($customerData);

$razorpayCustomerId = $customer['id'];

$_SESSION['razorpay_customer_id'] = $razorpayCustomerId;

$amount = $orderData['amount'];


$data = [
    "key"                           => $keyId,
    "amount"                        => $amount,
    "name"                          => "Mutual Fund",
    "description"                   => "SIP",
    "recurring"                     => 1,
    "prefill"                       => [
    "name"                          => $_POST['name-field'],
    "email"                         => $_POST['email-field'],
    "contact"                       => $_POST['contact-field'],
    "method"                        => "emandate",
    //"bank"                        => "UTIB",
    "bank_account[name]"            => $_POST['name-field'],
    "bank_account[account_number]"  => $_POST['account-field'],
    "bank_account[ifsc]"            => $_POST['ifsc-field'],
    "aadhaar[number]"               => $_POST['aadhaar-field'],
    //"auth_type"                   => "aadhaar"            
    ],
    "notes"                         => [
    "merchant_order_id"             => "12312321",
    ],
    "theme"                         => [
    "color"                         => "brown",
    "hide_topbar"                   => "true",
    "image_frame"                   => "false"
    ],
    "order_id"                      => $razorpayOrderId,
    "customer_id"                   => $razorpayCustomerId
];


$json = json_encode($data);

require("checkout/manual.php");
