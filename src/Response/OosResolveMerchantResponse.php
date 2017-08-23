<?php
/**
 * Created by PhpStorm.
 * User: enesdayanc
 * Date: 23/08/2017
 * Time: 14:34
 */

namespace PaymentGateway\VPosPosnet\Response;


class OosResolveMerchantResponse
{
    /** @var bool */
    private $valid = false;

    private $posnetAmount;
    private $currency;
    private $installment;
    private $orderId;
    private $mdStatus;
    private $status;
    private $errorMessage;
    private $rawData;
    private $requestRawData;

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * @param bool $valid
     */
    public function setValid(bool $valid)
    {
        $this->valid = $valid;
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
    public function getCurrency()
    {
        return $this->currency;
    }

    /**
     * @param mixed $currency
     */
    public function setCurrency($currency)
    {
        $this->currency = $currency;
    }

    /**
     * @return mixed
     */
    public function getInstallment()
    {
        return $this->installment;
    }

    /**
     * @param mixed $installment
     */
    public function setInstallment($installment)
    {
        $this->installment = $installment;
    }

    /**
     * @return mixed
     */
    public function getOrderId()
    {
        return $this->orderId;
    }

    /**
     * @param mixed $orderId
     */
    public function setOrderId($orderId)
    {
        $this->orderId = $orderId;
    }

    /**
     * @return mixed
     */
    public function getMdStatus()
    {
        return $this->mdStatus;
    }

    /**
     * @param mixed $mdStatus
     */
    public function setMdStatus($mdStatus)
    {
        $this->mdStatus = $mdStatus;
    }

    /**
     * @return mixed
     */
    public function getStatus()
    {
        return $this->status;
    }

    /**
     * @param mixed $status
     */
    public function setStatus($status)
    {
        $this->status = $status;
    }

    /**
     * @return mixed
     */
    public function getErrorMessage()
    {
        return $this->errorMessage;
    }

    /**
     * @param mixed $errorMessage
     */
    public function setErrorMessage($errorMessage)
    {
        $this->errorMessage = $errorMessage;
    }

    /**
     * @return mixed
     */
    public function getRawData()
    {
        return $this->rawData;
    }

    /**
     * @param mixed $rawData
     */
    public function setRawData($rawData)
    {
        $this->rawData = $rawData;
    }

    /**
     * @return mixed
     */
    public function getRequestRawData()
    {
        return $this->requestRawData;
    }

    /**
     * @param mixed $requestRawData
     */
    public function setRequestRawData($requestRawData)
    {
        $this->requestRawData = $requestRawData;
    }
}