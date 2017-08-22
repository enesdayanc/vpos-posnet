<?php
/**
 * Created by PhpStorm.
 * User: enesdayanc
 * Date: 22/08/2017
 * Time: 10:18
 */

namespace PaymentGateway\VPosPosnet;


use Exception;
use GuzzleHttp\Client;
use PaymentGateway\VPosPosnet\Exception\CurlException;
use PaymentGateway\VPosPosnet\Helper\Helper;
use PaymentGateway\VPosPosnet\Request\RequestInterface;
use PaymentGateway\VPosPosnet\Setting\Setting;

class HttpClient
{
    private $setting;

    /**
     * HttpClient constructor.
     * @param $setting
     */
    public function __construct(Setting $setting)
    {
        $this->setting = $setting;
    }

    /**
     * @param RequestInterface $requestElements
     * @param $url
     * @return Response\Response
     * @throws CurlException
     */
    public function send(RequestInterface $requestElements, $url)
    {
        $documentString = $requestElements->toXmlString($this->setting);

        $client = new Client();

        try {
            $clientResponse = $client->post($url, [
                'form_params' => [
                    'xmldata' => $documentString,
                ]
            ]);
        } catch (Exception $exception) {
            throw new CurlException('Connection Error', $exception->getMessage());
        }

        return Helper::getResponseByXML($clientResponse->getBody()->getContents());
    }
}