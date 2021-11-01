<?php
/**
 *
 *   Posta ve Telgraf Teşkilatı A.Ş. Genel Müdürlüğü adına Alttantire Yazılım Çözümleri tarafından geliştirilmiştir.
 *   Tüm hakları Posta ve Telgraf Teşkilatı A.Ş. Genel Müdürlüğü'ne aittir.
 *
 * @author      Erdem KAYA <erdem@alttantire.com>
 * @site        <https//akilliesnaf.ptt.gov.tr/>
 * @date        2021
 *
 */

include './variables.php';

$gateway = gateway();

if (isset($_GET['return'])) {
    $gateway->setPost($_POST);

    if ($gateway->isSuccessfull()) {

        die("Ödeme Çekildi, Sipariş No:" . $gateway->getOrderId());
    } else {
        throw new Exception($gateway->getError());
    }

    exit;
}

$returnUrl = "http://localhost/ptt-payment-sdk/examples/iframe.php?return=1";

$orderId = "123108";

$amount = 1000; //10 TL

$installment = "0"; //Taksit

$result = $gateway->startPaymentThreeDSession($returnUrl, $amount, $installment, $orderId);

$ThreeDSessionId = $result->ThreeDSessionId;

$iframeUrl = $gateway->getFrameUrl($ThreeDSessionId);

?><!DOCTYPE html>
<html lang="tr">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width,initial-scale=1.0">
    <base href="/">
    <title>Test</title>
</head>
<body>
<iframe src="<?= $iframeUrl ?>" height="550" width="800"></iframe>
</body>
</html>
