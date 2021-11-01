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
        //            throw new Exception($gateway->getError());
        echo $gateway->getError();
    }

    exit;
}

$returnUrl = "https://opencart2x.alttantire.com/ptt-payment-sdk/examples/3d.php?return=1";

$card = $testCards[0];

$orderId = "123007102";

$amount = 1000; //10 TL

$installment = "0"; //Taksit

$result = $gateway->threeDPayment($returnUrl, $amount, $installment, $orderId);

$ThreeDSessionId = $result->ThreeDSessionId;

$formUrl = $gateway->getFormUrl();

$params = $gateway->getFormParams($ThreeDSessionId, $card->name, $card->number, $card->expiry, $card->cvv);

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
<form method="POST" action="<?= $formUrl ?>">
    <?php foreach ($params as $key => $value): ?>
        <input type="text" name="<?= $key ?>" value="<?= $value ?>" />
    <?php endforeach; ?>
    <button type="submit">Gönder</button>
</form>
</body>
</html>
