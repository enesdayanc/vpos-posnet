<?php
/**
 * Created by PhpStorm.
 * User: enesdayanc
 * Date: 09/08/2017
 * Time: 16:36
 */

namespace PaymentGateway\VPosPosnet;

use PaymentGateway\ISO4217\ISO4217;
use PaymentGateway\ISO4217\Model\Currency;
use PaymentGateway\VPosPosnet\Constant\ReverseTransaction;
use PaymentGateway\VPosPosnet\Exception\ValidationException;
use PaymentGateway\VPosPosnet\Model\Card;
use PaymentGateway\VPosPosnet\Model\ThreeDResponse;
use PaymentGateway\VPosPosnet\Request\AuthorizeRequest;
use PaymentGateway\VPosPosnet\Request\CaptureRequest;
use PaymentGateway\VPosPosnet\Request\PurchaseRequest;
use PaymentGateway\VPosPosnet\Request\RefundRequest;
use PaymentGateway\VPosPosnet\Request\VoidRequest;
use PaymentGateway\VPosPosnet\Response\Response;
use PaymentGateway\VPosPosnet\Setting\Credential;
use PaymentGateway\VPosPosnet\Setting\YapiKrediTest;
use PHPUnit\Framework\TestCase;

class VposTest extends TestCase
{
    /** @var  VPos $vPos */
    protected $vPos;
    /** @var  Card $card */
    protected $card;

    /** @var  Currency $currency */
    protected $currency;

    protected $orderId;
    protected $authorizeOrderId;
    protected $amount;
    protected $userId;
    protected $installment;
    protected $userIp;

    public function setUp()
    {
        $credential = new Credential();
        $credential->setPosnetId(POSNET_ID);
        $credential->setMerchantId(MERCHANT_ID);
        $credential->setTerminalId(TERMINAL_ID);

        $settings = new YapiKrediTest();
        $settings->setCredential($credential);
        $settings->setThreeDReturnUrl('http://enesdayanc.com');

        $this->vPos = new VPos($settings);

        $card = new Card();
        $card->setCreditCardNumber("4048097007190236");
        $card->setExpiryMonth('02');
        $card->setExpiryYear('20');
        $card->setCvv('000');

        $this->card = $card;

        $iso4217 = new ISO4217();

        $this->currency = $iso4217->getByCode('TRY');

        $this->amount = rand(1, 100);
        $this->orderId = 'MO' . substr(md5(microtime() . rand()), 0, 10);
        $this->userId = md5(microtime() . rand());
        $this->installment = rand(1, 6);
        $this->userIp = '192.168.1.1';
    }

    public function testPurchase()
    {
        $purchaseRequest = new PurchaseRequest();

        $purchaseRequest->setCard($this->card);
        $purchaseRequest->setOrderId($this->orderId);
        $purchaseRequest->setAmount($this->amount);
        $purchaseRequest->setInstallment($this->installment);
        $purchaseRequest->setCurrency($this->currency);

        $response = $this->vPos->purchase($purchaseRequest);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());

        return array(
            'orderId' => $this->orderId,
            'amount' => $this->amount,
            'userId' => $this->userId,
            'transactionReference' => $response->getTransactionReference(),
        );
    }

    public function testPurchaseForVoid()
    {
        $purchaseRequest = new PurchaseRequest();

        $purchaseRequest->setCard($this->card);
        $purchaseRequest->setOrderId($this->orderId);
        $purchaseRequest->setAmount($this->amount);
        $purchaseRequest->setInstallment($this->installment);
        $purchaseRequest->setCurrency($this->currency);

        $response = $this->vPos->purchase($purchaseRequest);


        $this->assertInstanceOf(Response::class, $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());

        return array(
            'orderId' => $this->orderId,
            'amount' => $this->amount,
            'userId' => $this->userId,
            'transactionReference' => $response->getTransactionReference(),
        );
    }


    public function testPurchaseFailAmount()
    {
        $this->expectException(ValidationException::class);
        $this->expectExceptionMessage('Invalid Amount');

        $purchaseRequest = new PurchaseRequest();

        $purchaseRequest->setCard($this->card);
        $purchaseRequest->setOrderId($this->orderId);
        $purchaseRequest->setAmount(0);
        $purchaseRequest->setInstallment($this->installment);
        $purchaseRequest->setCurrency($this->currency);

        $this->vPos->purchase($purchaseRequest);
    }

    public function testPurchaseFailInstallment()
    {
        $purchaseRequest = new PurchaseRequest();

        $purchaseRequest->setCard($this->card);
        $purchaseRequest->setOrderId($this->orderId);
        $purchaseRequest->setAmount($this->amount);
        $purchaseRequest->setInstallment(50);
        $purchaseRequest->setCurrency($this->currency);

        $response = $this->vPos->purchase($purchaseRequest);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('cvc-maxInclusive-valid', $response->getErrorCode());
    }

    public function testAuthorize()
    {
        $authorizeRequest = new AuthorizeRequest();

        $authorizeRequest->setCard($this->card);
        $authorizeRequest->setOrderId($this->orderId);
        $authorizeRequest->setAmount($this->amount);
        $authorizeRequest->setInstallment($this->installment);
        $authorizeRequest->setCurrency($this->currency);

        $response = $this->vPos->authorize($authorizeRequest);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());

        return array(
            'orderId' => $this->orderId,
            'amount' => $this->amount,
            'userId' => $this->userId,
            'transactionReference' => $response->getTransactionReference(),
            'installment' => $this->installment,
        );
    }

    public function testAuthorizeFail()
    {
        $authorizeRequest = new AuthorizeRequest();

        $authorizeRequest->setCard($this->card);
        $authorizeRequest->setOrderId(1);
        $authorizeRequest->setAmount($this->amount);
        $authorizeRequest->setInstallment($this->installment);
        $authorizeRequest->setCurrency($this->currency);

        $response = $this->vPos->authorize($authorizeRequest);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('0127', $response->getErrorCode());
    }


    /**
     * @depends testAuthorize
     * @param $params
     */
    public function testCapture($params)
    {
        $captureRequest = new CaptureRequest();

        $captureRequest->setTransactionReference($params['transactionReference']);
        $captureRequest->setAmount($params['amount']);
        $captureRequest->setInstallment($params['installment']);
        $captureRequest->setCurrency($this->currency);

        $response = $this->vPos->capture($captureRequest);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
    }

    public function testCaptureFail()
    {
        $captureRequest = new CaptureRequest();

        $captureRequest->setTransactionReference('0000000041P0502141');
        $captureRequest->setAmount($this->amount);
        $captureRequest->setInstallment(1);
        $captureRequest->setCurrency($this->currency);

        $response = $this->vPos->capture($captureRequest);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('0123', $response->getErrorCode());
    }

//    Dont refund before 24 hours
//    /**
//     * @depends testPurchase
//     * @param $params
//     */
//    public function testRefund($params)
//    {
//        $refundRequest = new RefundRequest();
//        $refundRequest->setAmount($params['amount'] / 2);
//        $refundRequest->setOrderId($params['orderId']);
//
//        $response = $this->vPos->refund($refundRequest);
//
//        $this->assertInstanceOf(Response::class, $response);
//        $this->assertTrue($response->isSuccessful());
//        $this->assertFalse($response->isRedirect());
//
//        return $params;
//    }

    /**
     * @depends testPurchaseForVoid
     * @param $params
     */
    public function testVoid($params)
    {
        $voidRequest = new VoidRequest();
        $voidRequest->setOrderId($params['orderId']);
        $voidRequest->setReverseTransaction(ReverseTransaction::SALE);

        $response = $this->vPos->void($voidRequest);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
    }


    public function test3DPurchaseFormCreate()
    {
        $purchaseRequest = new PurchaseRequest();

        $purchaseRequest->setCard($this->card);
        $purchaseRequest->setOrderId($this->orderId);
        $purchaseRequest->setAmount($this->amount);
        $purchaseRequest->setInstallment($this->installment);
        $purchaseRequest->setCurrency($this->currency);

        $response = $this->vPos->purchase3D($purchaseRequest);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertTrue($response->isRedirect());
        $this->assertInternalType('array', $response->getRedirectData());
    }


    public function test3DResponseHandleSignatureFail()
    {
        $threeDResponse = new ThreeDResponse();
        $threeDResponse->setMerchantPacket('F9B546CE07C0F26184100F382F79F6CA40A593713652AD24B63B4C6F9766420775C7F79E12CEAE450F7EA1BE49A78CFC202ED5B782C790E18E641BA70943CC802803DB3944A06380C708F881F557020D89B179CF771EC1B3632C59BEF6ACAE61D0147659312A9E971382A4C982444DA408506A6EB1794F595BB70730598F92E25ADB0809DB6FF33608FA8B8AB8DF498BBA2373E61EEE880487D433A874628A6F5834D2727BA005B05B8D0F70EC80E9B420FA2228F6FD089796F43EB7C0A9810A4ECFD6D03D3210FA53385BC4');
        $threeDResponse->setBankPacket('D5B1135EC2A3865E5193FDFEE18C75C015AEF9068B1620F0A3DAD12C4EFE9B35C41F03C50863AF6CC30B3F044BCC42E3F4437D1D99DB2799081097C18FF4A4662E4E7B66833B111781333F7BCDF58BCB57FAC64F326A1A18F080F9A4B2E7BAABB5AE9AA53BA10D7ACBDD4F6209988D589E7A5D2052DD076A996E80AA0C08DDF1D2DC16D6440563D17B4B03FD');
        $threeDResponse->setSign('4DE8852C5AFC14D1546CE889C06F0A13');
        $threeDResponse->setCCPrefix('540061');
        $threeDResponse->setTranType('Sale');
        $threeDResponse->setPosnetAmount('1200');
        $threeDResponse->setXid('00000000MOb21432af7b');
        $threeDResponse->setMerchantId('6706598320');

        $response = $this->vPos->handle3DResponse($threeDResponse, 'MOb21432af7b');

        $this->assertInstanceOf(Response::class, $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('Invalid Signature', $response->getErrorMessage());
    }
}