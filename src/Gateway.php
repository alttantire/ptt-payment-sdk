<?php
/**
 *
 *   Posta ve Telgraf Teşkilatı A.Ş. Genel Müdürlüğü adına Alttantire Yazılım Çözümleri tarafından geliştirilmiştir.
 *   Tüm hakları Posta ve Telgraf Teşkilatı A.Ş. Genel Müdürlüğü'ne aittir.
 *
 * @author      Alttantire Yazılım Çözümleri <info@alttantire.com>
 * @site        <https//akilliesnaf.ptt.gov.tr/>
 * @date        2022
 * @version     2.0
 *
 */

namespace AkilliEsnaf;

use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\BadResponseException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;

class Gateway
{

    const DEFAULT_CURRENCY = 949;
    protected $clientId;
    protected $apiUser;
    protected $apiPass;
    protected $rnd;
    protected $timeSpan;
    protected $httpClient;
    protected $container;
    protected $url = "https://payment.testdgpf.dgpaysit.com/api/Payment/";
    protected $postdata = []; //default

    public function __construct($environment, $clientId, $apiUser, $apiPass)
    {
        $this->clientId = $clientId;
        $this->apiUser = $apiUser;
        $this->apiPass = $apiPass;

        if ($environment == "LIVE") {
            $this->url = "https://aeo.ptt.gov.tr/api/Payment/";
        }

        $this->init();
    }

    public function init()
    {
        $this->newRandomString();
        $this->newTimeSpan();

        return $this;
    }

    public function newRandomString($length = 12)
    {
        return $this->rnd = rand(1, 10000); //substr(sha1(rand()), 0, $length);
    }

    public function newTimeSpan($date = null)
    {
        return $this->timeSpan = date("YmdHis", $date ?: time());
    }

    /**
     * Non 3D işlem başlatma servisi
     *
     * @param $amount //tutarı 100 ile çarparak gönderin, 1TL için 100 gönderilmeli
     * @param int $installment //Taksit
     * @param string $cardHolderName // Kart hamilinin adı soyadı
     * @param string $cardNo // Kredi Kart numarası
     * @param string $expireDate // Son kullanım tarihi AA/YY formatında gönderilmeli
     * @param string $cvv // Güvenlik kodu
     * @param string $orderId // Sipariş Numarası. Boş bırakıldığında sistem tarafından üretilir.
     * @param string $description // Opsiyonel sipariş açıklaması Max 256 karakter
     * @param int $currency
     *
     * @return mixed
     * @throws Exception
     */
    public function payment($amount, int $installment = 0, string $cardHolderName, string $cardNo, string $expireDate, string $cvv, string $orderId = "", string $description = "", int $currency = 949)
    {
        return $this->post("Payment", $this->params([
            'orderId' => $orderId,
            'amount' => $amount,
            'currency' => $currency,
            'installmentCount' => $installment,
            'cardHolderName' => $cardHolderName,
            'cardNo' => $cardNo,
            'expireDate' => $expireDate,
            'cvv' => $cvv,
            'description' => $description,
        ]));
    }

    /**
     * 3D işlem başlatma servisi
     *
     * @param $callbackUrl //3D işlemi için geri dönüş URL si
     * @param $amount //tutarı 100 ile çarparak gönderin, 1TL için 100 gönderilmeli
     * @param int $installment //Taksit
     * @param string $orderId
     * @param int $currency
     *
     * @return mixed
     * @throws Exception
     */
    public function threeDPayment($callbackUrl, $amount, int $installment = 0, string $orderId = "", int $currency = 949)
    {
        return $this->post("threeDPayment", $this->params([
            'callbackUrl' => $callbackUrl,
            'orderId' => $orderId,
            'amount' => $amount,
            'currency' => $currency,
            'installmentCount' => $installment,
        ]));
    }

    /**
     * 3D Ön Otorizasyon işlem başlatma servisi
     *
     * @param string $callbackUrl //3D işlemi için geri dönüş URL si
     * @param integer $amount //tutarı 100 ile çarparak gönderin, 1TL için 100 gönderilmeli
     * @param int $installment //Taksit
     * @param string $orderId
     * @param string $description
     * @param int $currency
     *
     * @return mixed
     * @throws Exception
     */
    public function threeDPreAuth(string $callbackUrl, int $amount, int $installment = 0, string $orderId = "", string $description="", int $currency = 949)
    {
        return $this->post("threeDPreAuth", $this->params([
            'callbackUrl' => $callbackUrl,
            'orderId' => $orderId,
            'description' => $description,
            'amount' => $amount,
            'currency' => $currency,
            'installmentCount' => $installment,
        ]));
    }

    /**
     * 3D Otorizasyon işlem tamamlama servisi.
     * Ön Otorizasyonu alınan tutar müşterinin kartından tahsil edilir.
     * Bu servis çağrılmadan satış işlemi tamamlanmış olmaz.
     *
     * @param string $orderId
     * @param integer $amount // Satışa dönüştürülecek tutar. 100 ile çarparak gönderin, 1TL için 100 gönderilmeli
     *
     * @return mixed
     * @throws Exception
     */
    public function threeDPostAuth( string $orderId, int $amount )
    {
        return $this->post("threeDPreAuth", $this->params([
            'orderId' => $orderId,
            'amount' => $amount,
        ]));
    }

    public function post($url, $postdata = [])
    {
        return $this->call(function () use ($url, $postdata) {
            return $this->httpClient()->request('POST', $url, [
                'json' => $postdata
            ]);
        });
    }

    /**
     * @param $callable
     *
     * @return mixed
     * @throws \Exception
     */
    public function call($callable)
    {
        try {
            $response = $callable();

            $responseBody = (string)$response->getBody();

            $result = json_decode($responseBody);

            return $result;
        } catch (BadResponseException $e) {
            $body = "";
            foreach ($this->container as $transaction) {
                $body .= (string)$transaction['request']->getBody();
            }
            //            echo $body;

            throw new Exception($e->getMessage() . "\n{$body}", 0, $e);
        }
    }

    public function httpClient()
    {
        $this->container = [];

        $history = Middleware::history($this->container);

        $stack = HandlerStack::create();
        // Add the history middleware to the handler stack.
        $stack->push($history);

        return $this->httpClient ?: $this->httpClient = new Client([
            'base_uri' => "{$this->url}",
            'debug' => false,
            'handler' => $stack,
            'headers' => [
                'Accept' => 'application/json',
            ]
        ]);
    }

    public function params($params = [])
    {
        return [
                'clientId' => $this->clientId,
                'apiUser' => $this->apiUser,
                'Rnd' => $this->rnd ?: $this->newRandomString(),
                'timeSpan' => $this->timeSpan ?: $this->newTimeSpan(),
                'Hash' => $this->generateHash(),
            ] + $params;
    }

    /**
     * Hash üretme servisi
     *
     * @return string
     */
    public function generateHash()
    {
        $apiPass = $this->apiPass;
        $clientId = $this->clientId;
        $apiUser = $this->apiUser;
        $rnd = $this->rnd;
        $timeSpan = $this->timeSpan;

        $hashString = $apiPass . $clientId . $apiUser . $rnd . $timeSpan;

        $hashingbytes = hash("sha512", ($hashString), true);

        $hash = base64_encode($hashingbytes);

        return $hash;
    }

    /**
     * Ödeme doğrulama servisi
     *
     * @param $orderId
     * @return mixed
     */
    public function inquiry($orderId)
    {
        return $this->post("inquiry", $this->params(compact('orderId')));
    }

    /**
     * Sipariş iptal sevisi - ödemenin tamamı iade edilir.
     *
     * @param $orderId
     * @return mixed
     */
    public function void($orderId)
    {
        return $this->post("void", $this->params([
            'orderId' => $orderId,
        ]));
    }

    /**
     * Sipariş iade servisi - kısmi iade yapılır
     *
     * @param $amount
     * @param string $orderId
     * @return mixed
     */
    public function refund($amount, $orderId = "")
    {
        return $this->post("refund", $this->params([
            'OrderId' => $orderId,
            'Amount' => $amount,
        ]));
    }

    /**
     * Sipariş geçmişi sorgulama servisi
     *
     * @param     $date //date format: yyyyMMdd date('Ymd')
     * @param int $page
     * @param int $pageSize
     *
     * @return mixed
     * @throws Exception
     */
    public function history($date, $orderId = "", $page = 1, $pageSize = 10)
    {
        return $this->post("history", $this->params([
            'TransactionDate' => $date,
            'OrderId' => $orderId,
            'Page' => $page,
            'pageSize' => $pageSize,
        ]));
    }

    /**
     * 3D işlem başlatma servisi - ThreeDSessionId almak için kullanılır.
     *
     * @param $callbackUrl
     * @param $amount
     * @param int $installment
     * @param string $orderId
     * @param int $currency
     * @return mixed
     * @throws Exception
     */
    public function startPaymentThreeDSession($callbackUrl, $amount, $installment = 0, $orderId = "", $currency = 949)
    {
        return $this->post("startPaymentThreeDSession", $this->params([
            'callbackUrl' => $callbackUrl,
            'orderId' => $orderId,
            'amount' => $amount,
            'currency' => $currency,
            'installmentCount' => $installment,
        ]));
    }

    public function startPreAuthThreeDSession($callbackUrl, $amount, $installment = 0, $orderId = "", $currency = 949)
    {
        return $this->post("startPreAuthThreeDSession", $this->params([
            'callbackUrl' => $callbackUrl,
            'orderId' => $orderId,
            'amount' => $amount,
            'currency' => $currency,
            'installmentCount' => $installment,
        ]));
    }

    /**
     * ThreeDSessionId doğrulama/kontrol servisi
     *
     * @param $threeDSessionId
     * @return mixed
     * @throws Exception
     */
    public function threeDSessionResult($threeDSessionId)
    {
        return $this->post("threeDSessionResult", $this->params([
            'threeDSessionId' => $threeDSessionId,
        ]));
    }

    public function processThreeD($threeDSessionId, $orderId)
    {
        return $this->post("processThreeD", $this->params([
            'threeDSessionId' => $threeDSessionId,
            'orderId' => $orderId,
        ]));
    }

    public function get($url)
    {
        return $this->call(function () use ($url) {
            return $this->httpClient()->request('GET', $url);
        });
    }

    public function setPost($data): Gateway
    {
        $this->postdata = $data;
        return $this;
    }

    /**
     * Ödeme durum kontrol servisi
     *
     * @return bool
     */
    public function isSuccessfull()
    {
        if ($this->validateHash($this->postdata)) {
            return isset($this->postdata['BankResponseCode']) && $this->postdata['BankResponseCode'] === '00';
        }

        return false;
    }

    /**
     * Hash doğrulama servisi
     *
     * @param $data
     * @return bool
     */
    public function validateHash($data)
    {
        if (isset($data['HashParameters'])) {
            $keys = explode(",", $data['HashParameters']);

            $extra = [
                'ClientId' => $this->clientId,
                'ApiUser' => $this->apiUser,
            ];

            $hashString = $this->apiPass;

            foreach ($keys as $key) {
                $hashString .= isset($extra[$key]) ? $extra[$key] : $data[$key];
            }

            $hashingbytes = hash("sha512", ($hashString), true);

            return $data['Hash'] === base64_encode($hashingbytes);
        }

        return false;
    }

    /**
     * @return mixed|null
     */
    public function getOrderId()
    {
        return isset($this->postdata['OrderId']) ? $this->postdata['OrderId'] : null;
    }

    public function getError()
    {
        /* if (!$this->validateHash($this->postdata)) {
             return 'Hash doğrulama başarısız';
         }*/

        return isset($this->postdata['BankResponseMessage']) ? $this->postdata['BankResponseMessage'] : null;
    }

    public function getFormParams($id, $name, $cardNumber, $expiry, $cvv)
    {
        return [
            'ThreeDSessionId' => $id,
            'CardHolderName' => $name,
            'CardNo' => $cardNumber,
            'ExpireDate' => $expiry,
            'Cvv' => $cvv,
        ];
    }

    /**
     *
     * @return string
     */
    public function getFormUrl()
    {
        return "{$this->url}ProcessCardForm";
    }

    /**
     * Ortak ödeme sayfası url bilgisini döndürür.
     *
     * @param $id
     * @return string
     */
    public function getFrameUrl($id)
    {
        return "{$this->url}threeDSecure/{$id}";
    }
}
