<?php
/**
 * Created by PhpStorm.
 * User: enesdayanc
 * Date: 22/08/2017
 * Time: 10:34
 */

namespace PaymentGateway\VPosPosnet\Helper;


use Exception;
use PaymentGateway\ISO4217\Model\Currency;
use PaymentGateway\VPosPosnet\Constant\BankType;
use PaymentGateway\VPosPosnet\Constant\RequestCurrencyCode;
use PaymentGateway\VPosPosnet\Exception\NotFoundException;
use PaymentGateway\VPosPosnet\Exception\ValidationException;
use PaymentGateway\VPosPosnet\Model\ThreeDResponse;
use PaymentGateway\VPosPosnet\Response\OosResolveMerchantResponse;
use PaymentGateway\VPosPosnet\Response\OosResponse;
use PaymentGateway\VPosPosnet\Response\Response;
use PaymentGateway\VPosPosnet\Setting\MockBank;
use PaymentGateway\VPosPosnet\Setting\Setting;
use PaymentGateway\VPosPosnet\Setting\YapiKredi;
use PaymentGateway\VPosPosnet\Setting\YapiKrediTest;
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

        $data = @simplexml_load_string($xml);

        if (!$data) {
            if (empty($xml)) {
                throw new ValidationException('Invalid Xml Response', 'INVALID_XML_RESPONSE');
            } else {
                $errorData = self::getErrorFromXmlString($xml);

                $response->setErrorCode($errorData['code']);
                $response->setErrorMessage($errorData['message']);
            }
        }

        if (!empty($data->approved)
            && $data->approved == 1) {
            $response->setSuccessful(true);
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
            && $data->approved == 1) {
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


    public static function getOosResolveMerchantResponseByXML($xml, $requestRawData)
    {
        try {
            $data = new SimpleXMLElement($xml);
        } catch (Exception $exception) {
            throw new ValidationException('Invalid Oos Xml Response', 'INVALID_OOS_XML_RESPONSE');
        }

        $response = new OosResolveMerchantResponse();

        $response->setRawData($xml);
        $response->setRequestRawData($requestRawData);


        if (!empty($data->approved)
            && $data->approved == 1) {
            $response->setValid(true);
        }

        if (!empty($data->oosResolveMerchantDataResponse->amount)) {
            $response->setPosnetAmount($data->oosResolveMerchantDataResponse->amount);
        }


        if (!empty($data->oosResolveMerchantDataResponse->currency)) {
            $response->setCurrency($data->oosResolveMerchantDataResponse->currency);
        }

        if (!empty($data->oosResolveMerchantDataResponse->installment)) {
            $response->setInstallment($data->oosResolveMerchantDataResponse->installment);
        }


        if (!empty($data->oosResolveMerchantDataResponse->xid)) {
            $response->setOrderId($data->oosResolveMerchantDataResponse->xid);
        }


        if (!empty($data->oosResolveMerchantDataResponse->txStatus)) {
            $response->setStatus($data->oosResolveMerchantDataResponse->txStatus);
        }

        if (!empty($data->oosResolveMerchantDataResponse->mdStatus)) {
            $response->setMdStatus($data->oosResolveMerchantDataResponse->mdStatus);
        }

        if (!empty($data->oosResolveMerchantDataResponse->mdErrorMessage)) {
            $response->setErrorMessage($data->oosResolveMerchantDataResponse->mdErrorMessage);
        }

        return $response;
    }

    public static function maskValue($value, $takeStart = 0, $takeStop = 0, $maskingCharacter = '*')
    {
        return substr($value, $takeStart, $takeStop) . str_repeat($maskingCharacter, strlen($value) - ($takeStop - $takeStart));
    }

    /**
     * @param $bankType
     * @return Setting
     * @throws NotFoundException
     */
    public static function getSettingClassByBankType($bankType)
    {
        Validator::validateBankType($bankType);

        switch ($bankType) {
            case BankType::YAPI_KREDI:
                $setting = new YapiKredi();
                break;
            case BankType::YAPI_KREDI_TEST:
                $setting = new YapiKrediTest();
                break;
            case BankType::MOCKBANK:
                $setting = new MockBank();
                break;
        }

        if (!isset($setting) || !$setting instanceof Setting) {
            $userMessage = $bankType . ' not found';
            $internalMessage = 'BANK_TYPE_NOT_FOUND';
            throw new NotFoundException($userMessage, $internalMessage);
        }

        return $setting;
    }

    public static function getValueFromArray(array $array, $key, $default = null)
    {
        if (array_key_exists($key, $array)) {
            return $array[$key];
        }

        return $default;
    }

    /**
     * @param array $request
     * @return ThreeDResponse
     */
    public static function getThreeDResponseFromRequest(array $request)
    {
        $threeDResponse = new ThreeDResponse();
        $threeDResponse->setMerchantPacket(self::getValueFromArray($request, 'MerchantPacket'));
        $threeDResponse->setBankPacket(self::getValueFromArray($request, 'BankPacket'));
        $threeDResponse->setSign(self::getValueFromArray($request, 'Sign'));
        $threeDResponse->setCCPrefix(self::getValueFromArray($request, 'CCPrefix'));
        $threeDResponse->setTranType(self::getValueFromArray($request, 'TranType'));
        $threeDResponse->setPosnetAmount(self::getValueFromArray($request, 'Amount'));
        $threeDResponse->setXid(self::getValueFromArray($request, 'Xid'));
        $threeDResponse->setMerchantId(self::getValueFromArray($request, 'MerchantId'));

        return $threeDResponse;
    }

    public static function getRequestCurrencyCodeFromISO4217(Currency $currency)
    {
        Validator::validateCurrency($currency);

        switch ($currency->getAlpha3()) {
            case \PaymentGateway\VPosPosnet\Constant\Currency::TL:
                $requestCurrencyCode = RequestCurrencyCode::YT;
                break;
            case \PaymentGateway\VPosPosnet\Constant\Currency::EUR:
                $requestCurrencyCode = RequestCurrencyCode::EU;
                break;
            case \PaymentGateway\VPosPosnet\Constant\Currency::USD:
                $requestCurrencyCode = RequestCurrencyCode::US;
                break;
        }

        if (empty($requestCurrencyCode)) {
            $userMessage = $currency->getAlpha3() . ' not found';
            $internalMessage = 'CURRENCY_NOT_FOUND';
            throw new NotFoundException($userMessage, $internalMessage);
        }

        return $requestCurrencyCode;
    }
}