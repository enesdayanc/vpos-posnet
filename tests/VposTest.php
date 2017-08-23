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
        $card->setCreditCardNumber("5400617030332817");
        $card->setExpiryMonth('02');
        $card->setExpiryYear('20');
        $card->setCvv('000');

        $this->card = $card;

        $this->amount = rand(1, 100);
        $this->orderId = 'MO' . substr(md5(microtime() . rand()), 0, 10);
        $this->userId = md5(microtime() . rand());
        $this->installment = rand(1, 6);
        $this->userIp = '192.168.1.1';
    }

//    public function testPurchase()
//    {
//        $purchaseRequest = new PurchaseRequest();
//
//        $purchaseRequest->setCard($this->card);
//        $purchaseRequest->setOrderId($this->orderId);
//        $purchaseRequest->setAmount($this->amount);
//        $purchaseRequest->setInstallment($this->installment);
//
//        $response = $this->vPos->purchase($purchaseRequest);
//
//        $this->assertInstanceOf(Response::class, $response);
//        $this->assertTrue($response->isSuccessful());
//        $this->assertFalse($response->isRedirect());
//
//        return array(
//            'orderId' => $this->orderId,
//            'amount' => $this->amount,
//            'userId' => $this->userId,
//            'transactionReference' => $response->getTransactionReference(),
//        );
//    }
//
//    public function testPurchaseForVoid()
//    {
//        $purchaseRequest = new PurchaseRequest();
//
//        $purchaseRequest->setCard($this->card);
//        $purchaseRequest->setOrderId($this->orderId);
//        $purchaseRequest->setAmount($this->amount);
//        $purchaseRequest->setInstallment($this->installment);
//
//        $response = $this->vPos->purchase($purchaseRequest);
//
//
//        $this->assertInstanceOf(Response::class, $response);
//        $this->assertTrue($response->isSuccessful());
//        $this->assertFalse($response->isRedirect());
//
//        return array(
//            'orderId' => $this->orderId,
//            'amount' => $this->amount,
//            'userId' => $this->userId,
//            'transactionReference' => $response->getTransactionReference(),
//        );
//    }
//
//
//    public function testPurchaseFailAmount()
//    {
//        $this->expectException(ValidationException::class);
//        $this->expectExceptionMessage('Invalid Amount');
//
//        $purchaseRequest = new PurchaseRequest();
//
//        $purchaseRequest->setCard($this->card);
//        $purchaseRequest->setOrderId($this->orderId);
//        $purchaseRequest->setAmount(0);
//        $purchaseRequest->setInstallment($this->installment);
//
//        $this->vPos->purchase($purchaseRequest);
//    }
//
//    public function testPurchaseFailInstallment()
//    {
//        $purchaseRequest = new PurchaseRequest();
//
//        $purchaseRequest->setCard($this->card);
//        $purchaseRequest->setOrderId($this->orderId);
//        $purchaseRequest->setAmount($this->amount);
//        $purchaseRequest->setInstallment(50);
//
//        $response = $this->vPos->purchase($purchaseRequest);
//
//        $this->assertInstanceOf(Response::class, $response);
//        $this->assertFalse($response->isSuccessful());
//        $this->assertFalse($response->isRedirect());
//        $this->assertSame('cvc-maxInclusive-valid', $response->getErrorCode());
//    }
//
//    public function testAuthorize()
//    {
//        $authorizeRequest = new AuthorizeRequest();
//
//        $authorizeRequest->setCard($this->card);
//        $authorizeRequest->setOrderId($this->orderId);
//        $authorizeRequest->setAmount($this->amount);
//        $authorizeRequest->setInstallment($this->installment);
//
//        $response = $this->vPos->authorize($authorizeRequest);
//
//        $this->assertInstanceOf(Response::class, $response);
//        $this->assertTrue($response->isSuccessful());
//        $this->assertFalse($response->isRedirect());
//
//        return array(
//            'orderId' => $this->orderId,
//            'amount' => $this->amount,
//            'userId' => $this->userId,
//            'transactionReference' => $response->getTransactionReference(),
//            'installment' => $this->installment,
//        );
//    }
//
//    public function testAuthorizeFail()
//    {
//        $authorizeRequest = new AuthorizeRequest();
//
//        $authorizeRequest->setCard($this->card);
//        $authorizeRequest->setOrderId(1);
//        $authorizeRequest->setAmount($this->amount);
//        $authorizeRequest->setInstallment($this->installment);
//
//        $response = $this->vPos->authorize($authorizeRequest);
//
//        $this->assertInstanceOf(Response::class, $response);
//        $this->assertFalse($response->isSuccessful());
//        $this->assertFalse($response->isRedirect());
//        $this->assertSame('0127', $response->getErrorCode());
//    }
//
//
//    /**
//     * @depends testAuthorize
//     * @param $params
//     */
//    public function testCapture($params)
//    {
//        $captureRequest = new CaptureRequest();
//
//        $captureRequest->setTransactionReference($params['transactionReference']);
//        $captureRequest->setAmount($params['amount']);
//        $captureRequest->setInstallment($params['installment']);
//
//        $response = $this->vPos->capture($captureRequest);
//
//        $this->assertInstanceOf(Response::class, $response);
//        $this->assertTrue($response->isSuccessful());
//        $this->assertFalse($response->isRedirect());
//    }
//
//    public function testCaptureFail()
//    {
//        $captureRequest = new CaptureRequest();
//
//        $captureRequest->setTransactionReference('0000000041P0502141');
//        $captureRequest->setAmount($this->amount);
//        $captureRequest->setInstallment(1);
//
//        $response = $this->vPos->capture($captureRequest);
//
//        $this->assertInstanceOf(Response::class, $response);
//        $this->assertFalse($response->isSuccessful());
//        $this->assertFalse($response->isRedirect());
//        $this->assertSame('0123', $response->getErrorCode());
//    }
//
////    Dont refund before 24 hours
////    /**
////     * @depends testPurchase
////     * @param $params
////     */
////    public function testRefund($params)
////    {
////        $refundRequest = new RefundRequest();
////        $refundRequest->setAmount($params['amount'] / 2);
////        $refundRequest->setTransactionReference($params['transactionReference']);
////
////        $response = $this->vPos->refund($refundRequest);
////
////        $this->assertInstanceOf(Response::class, $response);
////        $this->assertTrue($response->isSuccessful());
////        $this->assertFalse($response->isRedirect());
////
////        return $params;
////    }
//
//    /**
//     * @depends testPurchaseForVoid
//     * @param $params
//     */
//    public function testVoid($params)
//    {
//        $voidRequest = new VoidRequest();
//        $voidRequest->setTransactionReference($params['transactionReference']);
//        $voidRequest->setReverseTransaction(ReverseTransaction::SALE);
//
//        $response = $this->vPos->void($voidRequest);
//
//        $this->assertInstanceOf(Response::class, $response);
//        $this->assertTrue($response->isSuccessful());
//        $this->assertFalse($response->isRedirect());
//    }
//
//
//    public function test3DPurchaseFormCreate()
//    {
//        $purchaseRequest = new PurchaseRequest();
//
//        $purchaseRequest->setCard($this->card);
//        $purchaseRequest->setOrderId($this->orderId);
//        $purchaseRequest->setAmount($this->amount);
//        $purchaseRequest->setInstallment($this->installment);
//
//        $response = $this->vPos->purchase3D($purchaseRequest);
//
//
//        /*$input = "";
//
//         foreach ($response->getRedirectData() as $key => $value) {
//             $input .= '<input type="text" name="' . $key . '" value="' . $value . '">';
//         }
//
//
//         echo '<!DOCTYPE html>
// <html>
// <head>
//     <title></title>
// </head>
// <body>
// <form action="'.$response->getRedirectUrl().'" method="'.$response->getRedirectMethod().'">
//     ' . $input . '
//     <input type="submit" name="" value="gönder">
// </form>
// </body>
// </html>';
//         exit();*/
//
//
//        $this->assertInstanceOf(Response::class, $response);
//        $this->assertFalse($response->isSuccessful());
//        $this->assertTrue($response->isRedirect());
//        $this->assertInternalType('array', $response->getRedirectData());
//    }


    public function test3DResponseHandle()
    {
        $threeDResponse = new ThreeDResponse();
        $threeDResponse->setMerchantPacket('CE51B251BD1769BDC1A012605E9422A2D1992C990512A7F5C3D72BF2325541A5DDB8304F68FEE12FF9DD68FD44E168C931729D41232727EE05054F8D48AF005FDAC75B707CC3FF287F1659CB1A1E3D6BD385422BA3D7D56F63B8E587D91ECC2893C7CCBBB410FB85FE84EC7D13E2474F738B2873C64F06F2AF7272B1BF73DF2B84D42C4C2999B24FE82DB8092FF49F0B1C834E61623B5441AA1BE78CAB10739A55E43ACCD67C0F51E36AD2FE2C53CFEC4B39DD69CA0E06701D26F15DC7B71115EBA1A2D6D5400A2D8003E05A');
        $threeDResponse->setBankPacket('007758721563AE59DEF7911DC1EAFF01AC1740DDEF14DCDFB7E93828A76ADBC7D01F0BE78317B6CF144E8785252A984AE262F1D0BD110534CA9D4D04D29507C54B4A7FFFDC884D183AC7E57E18BCA206B405EB0C844DD31E38FCC0521372AAE555AFADE7FCD92C80A6C703D890BE707BA69251BEE124A308378C4A96FBB25ACC85D63199C7CE966C45B3797B');
        $threeDResponse->setSign('063A259AEC04FD97D5F7E022EAB1B912');
        $threeDResponse->setCCPrefix('540061');
        $threeDResponse->setTranType('Sale');
        $threeDResponse->setPosnetAmount('9400');
        $threeDResponse->setXid('00000000MO08b9fdd5e9');
        $threeDResponse->setMerchantId('6706598320');

        $response = $this->vPos->handle3DResponse($threeDResponse);

        print_r($response);
        exit();
    }
}