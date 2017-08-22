<?php

namespace PaymentGateway\VPosPosnet;

use Exception;
use GuzzleHttp\Client;
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