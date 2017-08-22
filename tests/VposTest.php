<?php
/**
 * Created by PhpStorm.
 * User: enesdayanc
 * Date: 09/08/2017
 * Time: 16:36
 */

namespace PaymentGateway\VPosPosnet;

use PaymentGateway\VPosPosnet\Constant\ReverseTransaction;
use PaymentGateway\VPosPosnet\Exception\ValidationException;
use PaymentGateway\VPosPosnet\Model\Card;
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

    protected $orderId;
    protected $authorizeOrderId;
    protected $amount;
    protected $userId;
    protected $installment;
    protected $userIp;

    public function setUp()
    {
        $credential = new Credential();
        $credential->setMerchantId(MERCHANT_ID);
        $credential->setTerminalId(TERMINAL_ID);

        $settings = new YapiKrediTest();
        $settings->setCredential($credential);

        $this->vPos = new VPos($settings);

        $card = new Card();
        $card->setCreditCardNumber("4048097006508842");
        $card->setExpiryMonth('02');
        $card->setExpiryYear('20');
        $card->setCvv('000');

        $this->card = $card;

        $this->amount = rand(1, 100);
        $this->orderId = 'MO' . substr(md5(microtime() . rand()), 0, 20);
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
        $purchaseRequest->setCurrency($this->currency);
        $purchaseRequest->setUserId($this->userId);
        $purchaseRequest->setInstallment($this->installment);
        $purchaseRequest->setIp('198.168.1.1');
        $purchaseRequest->setEmail('enes.dayanc@modanisa.com.tr');

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

        $this->vPos->purchase($purchaseRequest);
    }

    public function testPurchaseFailInstallment()
    {
        $purchaseRequest = new PurchaseRequest();

        $purchaseRequest->setCard($this->card);
        $purchaseRequest->setOrderId($this->orderId);
        $purchaseRequest->setAmount($this->amount);
        $purchaseRequest->setInstallment(50);

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

        $response = $this->vPos->authorize($authorizeRequest);

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

    public function testAuthorizeFail()
    {
        $authorizeRequest = new AuthorizeRequest();

        $authorizeRequest->setCard($this->card);
        $authorizeRequest->setOrderId(1);
        $authorizeRequest->setAmount($this->amount);
        $authorizeRequest->setInstallment($this->installment);

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
        sleep(2);
        $captureRequest = new CaptureRequest();

        $captureRequest->setTransactionReference($params['transactionReference']);
        $captureRequest->setAmount($params['amount']);
        $captureRequest->setInstallment(1);

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

        $response = $this->vPos->capture($captureRequest);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertFalse($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
        $this->assertSame('0123', $response->getErrorCode());
    }

    /**
     * @depends testPurchase
     * @param $params
     */
    public function testRefund($params)
    {
        $refundRequest = new RefundRequest();
        $refundRequest->setAmount($params['amount'] / 2);
        $refundRequest->setTransactionReference($params['transactionReference']);

        $response = $this->vPos->refund($refundRequest);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());

        return $params;
    }

    /**
     * @depends testPurchaseForVoid
     * @param $params
     */
    public function testVoid($params)
    {
        $voidRequest = new VoidRequest();
        $voidRequest->setTransactionReference($params['transactionReference']);
        $voidRequest->setReverseTransaction(ReverseTransaction::SALE);

        $response = $this->vPos->void($voidRequest);

        $this->assertInstanceOf(Response::class, $response);
        $this->assertTrue($response->isSuccessful());
        $this->assertFalse($response->isRedirect());
    }
}