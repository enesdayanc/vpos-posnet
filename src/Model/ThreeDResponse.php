<?php
/**
 * Created by PhpStorm.
 * User: enesdayanc
 * Date: 23/08/2017
 * Time: 14:18
 */

namespace PaymentGateway\VPosPosnet\Model;


use PaymentGateway\VPosPosnet\Constant\MdStatus;
use PaymentGateway\VPosPosnet\Exception\ValidationException;
use PaymentGateway\VPosPosnet\Helper\Validator;
use PaymentGateway\VPosPosnet\HttpClient;
use PaymentGateway\VPosPosnet\Request\OosResolveMerchantRequest;
use PaymentGateway\VPosPosnet\Request\OosTranRequest;
use PaymentGateway\VPosPosnet\Response\OosResolveMerchantResponse;
use PaymentGateway\VPosPosnet\Response\Response;
use PaymentGateway\VPosPosnet\Setting\Setting;

class ThreeDResponse
{
    private $merchantPacket;
    private $bankPacket;
    private $sign;
    private $cCPrefix;
    private $tranType;
    private $posnetAmount;
    private $xid;
    private $merchantId;

    /**
     * @return mixed
     */
    public function getMerchantPacket()
    {
        return $this->merchantPacket;
    }

    /**
     * @param mixed $merchantPacket
     */
    public function setMerchantPacket($merchantPacket)
    {
        $this->merchantPacket = $merchantPacket;
    }

    /**
     * @return mixed
     */
    public function getBankPacket()
    {
        return $this->bankPacket;
    }

    /**
     * @param mixed $bankPacket
     */
    public function setBankPacket($bankPacket)
    {
        $this->bankPacket = $bankPacket;
    }

    /**
     * @return mixed
     */
    public function getSign()
    {
        return $this->sign;
    }

    /**
     * @param mixed $sign
     */
    public function setSign($sign)
    {
        $this->sign = $sign;
    }

    /**
     * @return mixed
     */
    public function getCCPrefix()
    {
        return $this->cCPrefix;
    }

    /**
     * @param mixed $cCPrefix
     */
    public function setCCPrefix($cCPrefix)
    {
        $this->cCPrefix = $cCPrefix;
    }

    /**
     * @return mixed
     */
    public function getTranType()
    {
        return $this->tranType;
    }

    /**
     * @param mixed $tranType
     */
    public function setTranType($tranType)
    {
        $this->tranType = $tranType;
    }

    /**
     * @return mixed
     */
    public function getPosnetAmount()
    {
        return $this->posnetAmount;
    }

    /**
     * @param mixed $posnetAmount
     */
    public function setPosnetAmount($posnetAmount)
    {
        $this->posnetAmount = $posnetAmount;
    }

    /**
     * @return mixed
     */
    public function getXid()
    {
        return $this->xid;
    }

    /**
     * @param mixed $xid
     */
    public function setXid($xid)
    {
        $this->xid = $xid;
    }

    /**
     * @return mixed
     */
    public function getMerchantId()
    {
        return $this->merchantId;
    }

    /**
     * @param mixed $merchantId
     */
    public function setMerchantId($merchantId)
    {
        $this->merchantId = $merchantId;
    }

    /**
     * @param Setting $setting
     * @return Response
     * @throws ValidationException
     */
    public function getResponseClass(Setting $setting)
    {
        $this->validate();

        $oosResolveMerchantResponse = $this->getOosResolveMerchantResponse($setting);

        $validSignature = $this->isValidSignature($setting, $oosResolveMerchantResponse);

        $responseClass = new Response();

        if ($validSignature) {

            if (in_array($oosResolveMerchantResponse->getMdStatus(), $setting->getAllowedThreeDMdStatus())) {
                $httpClient = new HttpClient($setting);

                $oosTranRequest = new OosTranRequest();
                $oosTranRequest->setBankPacket($this->getBankPacket());

                return $httpClient->sendOosTrans($oosTranRequest, $setting->getOosUrl());
            } else {
                $responseClass->setErrorMessage('Invalid Md Status');
            }
        } else {
            $responseClass->setErrorMessage('Invalid Signature');
        }

        return $responseClass;
    }


    private function isValidSignature(Setting $setting, OosResolveMerchantResponse $oosResolveMerchantResponse)
    {
        if ($this->getMerchantId() != $setting->getCredential()->getMerchantId()) {
            return false;
        }

        if (!$oosResolveMerchantResponse->isValid()) {
            return false;
        }

        if ($this->getPosnetAmount() != $oosResolveMerchantResponse->getPosnetAmount()) {
            return false;
        }

        if ($this->getXid() != $oosResolveMerchantResponse->getOrderId()) {
            return false;
        }

        return true;
    }

    private function getOosResolveMerchantResponse(Setting $setting)
    {
        $oosResolveMerchantRequest = new OosResolveMerchantRequest();

        $oosResolveMerchantRequest->setMerchantPacket($this->getMerchantPacket());
        $oosResolveMerchantRequest->setBankPacket($this->getBankPacket());
        $oosResolveMerchantRequest->setSign($this->getSign());

        $httpClient = new HttpClient($setting);

        return $httpClient->sendOosResolveMerchant($oosResolveMerchantRequest, $setting->getOosUrl());
    }

    private function validate()
    {
        Validator::validateNotEmpty('merchantPacket', $this->getMerchantPacket());
        Validator::validateNotEmpty('bankPacket', $this->getBankPacket());
        Validator::validateNotEmpty('sign', $this->getSign());
        Validator::validateNotEmpty('cCPrefix', $this->getCCPrefix());
        Validator::validateNotEmpty('tranType', $this->getTranType());
        Validator::validateNotEmpty('posnetAmount', $this->getPosnetAmount());
        Validator::validateNotEmpty('xid', $this->getXid());
        Validator::validateNotEmpty('merchantId', $this->getMerchantId());
    }
}