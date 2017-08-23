<?php
/**
 * Created by PhpStorm.
 * User: enesdayanc
 * Date: 22/08/2017
 * Time: 10:34
 */

namespace PaymentGateway\VPosPosnet\Helper;


use Exception;
use PaymentGateway\VPosPosnet\Exception\ValidationException;
use PaymentGateway\VPosPosnet\Response\OosResponse;
use PaymentGateway\VPosPosnet\Response\Response;
use ReflectionClass;
use SimpleXMLElement;
use Spatie\ArrayToXml\ArrayToXml;
use stdClass;

class Helper
{
    public static function arrayToXmlString(array $array)
    {
        return ArrayToXml::convert($array, 'posnetRequest', true, 'UTF-8');
    }

    public static function orderIdParser($orderId, $length = 24)
    {
        return str_pad($orderId, $length, 0, STR_PAD_LEFT);
    }

    public static function amountParser($amount)
    {
        return (int)number_format($amount, 2, '', '');
    }

    public static function getResponseByXML($xml, $requestRawData)
    {
        $response = new Response();

        $response->setRawData($xml);
        $response->setRequestRawData($requestRawData);

        $data = new stdClass();

        try {
            $data = new SimpleXMLElement($xml);
        } catch (Exception $exception) {
            if (empty($xml)) {
                throw new ValidationException('Invalid Xml Response', 'INVALID_XML_RESPONSE');
            } else {
                $errorData = self::getErrorFromXmlString($xml);

                $response->setErrorCode($errorData['code']);
                $response->setErrorMessage($errorData['message']);
            }
        }

        if (!empty($data->approved)
            && $data->approved >= 1) {
            $response->setIsSuccessful(true);
        }

        if (!empty($data->authCode)) {
            $response->setCode((string)$data->authCode);
        }

        if (!empty($data->respCode)) {
            $response->setErrorCode((string)$data->respCode);
        }

        if (!empty($data->respText)) {
            $response->setErrorMessage((string)$data->respText);
        }

        if (!empty($data->hostlogkey)) {
            $response->setTransactionReference((string)$data->hostlogkey);
        }

        return $response;
    }

    private static function getErrorFromXmlString($xml)
    {
        if (empty($xml) || !is_string($xml)) {
            throw new ValidationException('Invalid Xml String', 'INVALID_XML_STRING');
        }

        $dataArray = explode(':', $xml);

        $code = null;

        if (isset($dataArray[7])) {
            $code = $dataArray[7];
        }

        $message = null;

        if (isset($dataArray[count($dataArray) - 1])) {
            $message = $dataArray[count($dataArray) - 1];
        }

        return array(
            'code' => $code,
            'message' => $message,
        );
    }

    public static function getConstants($class)
    {
        $oClass = new ReflectionClass ($class);
        return $oClass->getConstants();
    }

    public static function getOosResponseByXML($xml, $requestRawData)
    {
        try {
            $data = new SimpleXMLElement($xml);
        } catch (Exception $exception) {
            throw new ValidationException('Invalid Oos Xml Response', 'INVALID_OOS_XML_RESPONSE');
        }

        $response = new OosResponse();

        $response->setRawData($xml);
        $response->setRequestRawData($requestRawData);

        if (!empty($data->approved)
            && $data->approved >= 1) {
            $response->setValid(true);
        }

        if (!empty($data->oosRequestDataResponse->data1)) {
            $response->setData1((string)$data->oosRequestDataResponse->data1);
        }

        if (!empty($data->oosRequestDataResponse->data2)) {
            $response->setData2((string)$data->oosRequestDataResponse->data2);
        }

        if (!empty($data->oosRequestDataResponse->sign)) {
            $response->setSign((string)$data->oosRequestDataResponse->sign);
        }


        return $response;
    }
}