# PTT Akıllı Esnaf PHP Payment Gateway SDK

## Kurulum
```
$ composer require alttantire/ptt-payment-sdk
```

PHP Class ile entegre olmak için [PTT Akıllı Esnaf PHP Payment Class](https://github.com/alttantire/ptt-payment-php-class) reposunu kullanabilirsiniz.


## Kullanım
API çağrıları için aşağıdaki metodları kullanabilirsiniz.

### 3D Ödeme

```php
<?php

use AkilliEsnaf\Gateway;
include "vendor/autoload.php";

//### Sanal POS Üye İşyeri Ayarları
$apiUser = "Entegrasyon_01"; // Api kullanıcı adınız
$clientId = "1000000032"; // Api müşteri numaranız
$apiPass = "gkk4l2*TY112"; // Api şifreniz
$environment = "TEST"; // "LIVE" - Gerçek ortam | "TEST" - Test ortam
$callback_url = "https://www.siteadresiniz.com/payment-response.php";

//### Sipariş Bilgileri
$orderId = ""; // Sipariş numarası her sipariş için tekil olmalıdır. Boş bırakıldığında sistem tarafından otomatik üretilir
$amount = 1090; // 10 TL 90 kuruş
$instalment = 0; // Taksit sayısı - Tek çekim için 0

//### API Gateway
$gateway = new Gateway($environment, $clientId, $apiUser, $apiPass);
$payment = $gateway->threeDPayment($callback_url, $amount, $instalment, $orderId);

$three_d_session_id = $payment->ThreeDSessionId;
$form_post_url = $gateway->getFormUrl();
```


### Ortak Ödeme (iframe)

```php
<?php

use AkilliEsnaf\Gateway;
include "vendor/autoload.php";

//### Sanal POS Üye İşyeri Ayarları
$apiUser = "Entegrasyon_01"; // Api kullanıcı adınız
$clientId = "1000000032"; // Api müşteri numaranız
$apiPass = "gkk4l2*TY112"; // Api şifreniz
$environment = "TEST"; // "LIVE" - Gerçek ortam | "TEST" - Test ortam
$callback_url = "https://www.siteadresiniz.com/payment-response.php";

//### Sipariş Bilgileri
$orderId = ""; // Sipariş numarası her sipariş için tekil olmalıdır. Boş bırakıldığında sistem tarafından otomatik üretilir
$amount = 1090; // 10 TL 90 kuruş
$instalment = 0; // Taksit sayısı - Tek çekim için 0

//### API Gateway
$gateway = new Gateway($environment, $clientId, $apiUser, $apiPass);
$payment = $gateway->startPaymentThreeDSession($callback_url, $amount, $instalment, $orderId);

$iframe_url = $gateway->getFrameUrl($payment->ThreeDSessionId);
```

```html
<iframe src="<?php echo $iframe_url ?>" style=" width:100%; height: 550px;position: relative;" frameborder="0" allowfullscreen></iframe>
```


### Ödeme Sorgulama

```php
<?php

use AkilliEsnaf\Gateway;
include "vendor/autoload.php";

//### Sanal POS Üye İşyeri Ayarları
$apiUser = "Entegrasyon_01"; // Api kullanıcı adınız
$clientId = "1000000032"; // Api müşteri numaranız
$apiPass = "gkk4l2*TY112"; // Api şifreniz
$environment = "TEST"; // "LIVE" - Gerçek ortam | "TEST" - Test ortam
$callback_url = "https://www.siteadresiniz.com/payment-response.php";

//### Sipariş Bilgileri
$orderId = "20221011999"; // Sipariş numarası

//### API Gateway
$gateway = new Gateway($environment, $clientId, $apiUser, $apiPass);
$paymentCheck = $gateway->inquiry($orderId);

echo "<pre>";
print_r($paymentCheck);
```

### İşlem Listeleme

```php
<?php

use AkilliEsnaf\Gateway;
include "vendor/autoload.php";

//### Sanal POS Üye İşyeri Ayarları
$apiUser = "Entegrasyon_01"; // Api kullanıcı adınız
$clientId = "1000000032"; // Api müşteri numaranız
$apiPass = "gkk4l2*TY112"; // Api şifreniz
$environment = "TEST"; // "LIVE" - Gerçek ortam | "TEST" - Test ortam

//### API Gateway
$gateway = new Gateway($environment, $clientId, $apiUser, $apiPass);

/*
 * Sipariş numarası dolu gönderildiğinde o siparişe ait tüm işlemleri listeler.
 * Sipariş numarası boş gönderildiğinde o tarihteki tüm siparişleri listeler
*/
$orderId = "20221011999";

/*
 * Sipariş tarihi integer olmalıdır.
 * Listesi istenen tarih YYYYAAGG formatında yazılmalıdır.
*/
$date = 20221011;

$paymentCheck = $gateway->history($date, $orderId, $page = 1, $pageSize = 10);

echo "<pre>";
print_r($paymentCheck);
```

### İptal

```php
<?php

use AkilliEsnaf\Gateway;
include "vendor/autoload.php";

//### Sanal POS Üye İşyeri Ayarları
$apiUser = "Entegrasyon_01"; // Api kullanıcı adınız
$clientId = "1000000032"; // Api müşteri numaranız
$apiPass = "gkk4l2*TY112"; // Api şifreniz
$environment = "TEST"; // "LIVE" - Gerçek ortam | "TEST" - Test ortam

//### Sipariş Bilgileri
$orderId = "20221011999"; // Sipariş numarası

//### API Gateway
$gateway = new Gateway($environment, $clientId, $apiUser, $apiPass);
$paymentCheck = $gateway->void($orderId);

echo "<pre>";
print_r($paymentCheck);
```
### İade

```php
<?php

use AkilliEsnaf\Gateway;
include "vendor/autoload.php";

//### Sanal POS Üye İşyeri Ayarları
$apiUser = "Entegrasyon_01"; // Api kullanıcı adınız
$clientId = "1000000032"; // Api müşteri numaranız
$apiPass = "gkk4l2*TY112"; // Api şifreniz
$environment = "TEST"; // "LIVE" - Gerçek ortam | "TEST" - Test ortam

//### Sipariş Bilgileri
$orderId = "202210109"; // Sipariş numarası
$amount=125; // İade edilecek tutar. 1 TL 25 Kuruş

//### API Gateway
$gateway = new Gateway($environment, $clientId, $apiUser, $apiPass);
$paymentCheck = $gateway->refund($orderId, $amount);

echo "<pre>";
print_r($paymentCheck);
```
