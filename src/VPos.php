<?php

namespace PaymentGateway\VPosPosnet;

use Exception;
use GuzzleHttp\Client;
use PaymentGateway\VPosPosnet\Request\AuthorizeRequest;
use PaymentGateway\VPosPosnet\Request\CaptureRequest;
use PaymentGateway\VPosPosnet\Request\PurchaseRequest;
use PaymentGateway\VPosPosnet\Request\RequestInterface;
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
}