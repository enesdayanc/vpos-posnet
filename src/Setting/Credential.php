<?php
/**
 * Created by PhpStorm.
 * User: enesdayanc
 * Date: 22/08/2017
 * Time: 10:29
 */

namespace PaymentGateway\VPosPosnet\Setting;


use PaymentGateway\VPosPosnet\Helper\Validator;

class Credential
{
    private $merchantId;
    private $terminalId;
    private $posnetId;

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
     * @return mixed
     */
    public function getTerminalId()
    {
        return $this->terminalId;
    }

    /**
     * @param mixed $terminalId
     */
    public function setTerminalId($terminalId)
    {
        $this->terminalId = $terminalId;
    }

    /**
     * @return mixed
     */
    public function getPosnetId()
    {
        return $this->posnetId;
    }

    /**
     * @param mixed $posnetId
     */
    public function setPosnetId($posnetId)
    {
        $this->posnetId = $posnetId;
    }

    public function validate()
    {
        Validator::validateNotEmpty('merchantId', $this->getMerchantId());
        Validator::validateNotEmpty('terminalId', $this->getTerminalId());
        Validator::validateNotEmpty('posnetId', $this->getPosnetId());
    }
}