<?php
/**
 * Created by PhpStorm.
 * User: enesdayanc
 * Date: 09/08/2017
 * Time: 16:36
 */

namespace PaymentGateway\VPosPosnet;

use PaymentGateway\VPosPosnet\Exception\ValidationException;
use PaymentGateway\VPosPosnet\Model\Card;
use PaymentGateway\VPosPosnet\Request\PurchaseRequest;
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

        $this->amount = rand(1, 1000);
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
            'refNumber' => $response->getTransactionReference(),
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
}