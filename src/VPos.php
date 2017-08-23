<?php

namespace PaymentGateway\VPosPosnet;

use Exception;
use GuzzleHttp\Client;
use PaymentGateway\VPosPosnet\Constant\OosRequestDataType;
use PaymentGateway\VPosPosnet\Model\ThreeDResponse;
use PaymentGateway\VPosPosnet\Request\AuthorizeRequest;
use PaymentGateway\VPosPosnet\Request\CaptureRequest;
use PaymentGateway\VPosPosnet\Request\PurchaseRequest;
use PaymentGateway\VPosPosnet\Request\RefundRequest;
use PaymentGateway\VPosPosnet\Request\RequestInterface;
use PaymentGateway\VPosPosnet\Request\VoidRequest;
use PaymentGateway\VPosPosnet\Response\Response;
use PaymentGateway\VPosPosnet\Setting\Setting;

class VPos
{
    /** @var  Setting $setting */
    private $setting;

    public function __construct(Setting $setting)
    {
        $this->setting = $setting;
        $this->setting->validate();
    }

    public function authorize(AuthorizeRequest $authorizeRequest)
    {
        return $this->send($authorizeRequest, $this->setting->getAuthorizeUrl());
    }

    public function capture(CaptureRequest $captureRequest)
    {
        return $this->send($captureRequest, $this->setting->getCaptureUrl());
    }

    public function purchase(PurchaseRequest $purchaseRequest)
    {
        return $this->send($purchaseRequest, $this->setting->getPurchaseUrl());
    }

    public function refund(RefundRequest $refundRequest)
    {
        return $this->send($refundRequest, $this->setting->getRefundUrl());
    }

    public function void(VoidRequest $voidRequest)
    {
        return $this->send($voidRequest, $this->setting->getVoidUrl());
    }

    public function purchase3D(PurchaseRequest $purchaseRequest)
    {
        $redirectForm = $purchaseRequest->get3DRedirectForm($this->setting, OosRequestDataType::SALE);

        $response = new Response();

        $response->setIsRedirect(true);
        $response->setRedirectMethod($redirectForm->getMethod());
        $response->setRedirectUrl($redirectForm->getAction());
        $response->setRedirectData($redirectForm->getParameters());

        return $response;
    }

    /**
     * @param RequestInterface $requestElements
     * @param $url
     * @return Response
     */
    private function send(RequestInterface $requestElements, $url)
    {
        $httpClient = new HttpClient($this->setting);

        return $httpClient->send($requestElements, $url);
    }

    public function handle3DResponse(ThreeDResponse $threeDResponse)
    {
        return $threeDResponse->getResponseClass($this->setting);
    }
}