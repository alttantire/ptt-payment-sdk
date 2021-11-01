# PTT Akıllı Esnaf PHP Payment Gateway SDK

## Installation
This project using composer.
```
$ composer require alttantire/ptt-payment-sdk
```

## Usage
Generate 3dSessionId and payment form url.
```php
<?php

use AkilliEsnaf\Gateway;
include "../vendor/autoload.php";

$apiUser = "Entegrasyon_01";
$clientId = "1000000032";
$apiPass = "gkk4l2*TY112";

$gateway = new Gateway($clientId, $apiUser, $apiPass);

$orderId = "123127"; // must be unique
$amount = 125; // 1.25 TL
$installment = 0; // No installment

$result = $gateway->threeDPayment("http://www.example.com/return", $amount, $installment, $orderId);
$threeDSessionId = $result->ThreeDSessionId;

$params = $gateway->getFormParams($threeDSessionId, $cardName, $cardNumber, $cardExpiry, $cardCvv);
$url = $gateway->getFormUrl();

```