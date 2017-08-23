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
use PaymentGateway\VPosPosnet\Request\OosRequest;
use PaymentGateway\VPosPosnet\Request\OosResolveMerchantRequest;
use PaymentGateway\VPosPosnet\Request\OosTranRequest;
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

        return Helper::getResponseByXML($clientResponse->getBody()->getContents(), $documentString);
    }

    public function sendOos(OosRequest $oosRequest, $url)
    {
        $documentString = $oosRequest->toXmlString($this->setting);

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

        return Helper::getOosResponseByXML($clientResponse->getBody()->getContents(), $documentString);
    }

    /**
     * @param OosResolveMerchantRequest $oosResolveMerchantRequest
     * @param $url
     * @return Response\OosResolveMerchantResponse
     * @throws CurlException
     */
    public function sendOosResolveMerchant(OosResolveMerchantRequest $oosResolveMerchantRequest, $url)
    {
        $documentString = $oosResolveMerchantRequest->toXmlString($this->setting);

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

        return Helper::getOosResolveMerchantResponseByXML($clientResponse->getBody()->getContents(), $documentString);
    }


    /**
     * @param OosTranRequest $oosTranRequest
     * @param $url
     * @return Response\Response
     * @throws CurlException
     */
    public function sendOosTrans(OosTranRequest $oosTranRequest, $url)
    {
        $documentString = $oosTranRequest->toXmlString($this->setting);

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

        return Helper::getResponseByXML($clientResponse->getBody()->getContents(), $documentString);
    }
}