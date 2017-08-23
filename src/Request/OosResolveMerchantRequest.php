<?php
/**
 * Created by PhpStorm.
 * User: enesdayanc
 * Date: 23/08/2017
 * Time: 14:28
 */

namespace PaymentGateway\VPosPosnet\Request;


use PaymentGateway\VPosPosnet\Helper\Helper;
use PaymentGateway\VPosPosnet\Helper\Validator;
use PaymentGateway\VPosPosnet\Setting\Setting;

class OosResolveMerchantRequest implements RequestInterface
{
    private $merchantPacket;
    private $bankPacket;
    private $sign;

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

    public function validate()
    {
        Validator::validateNotEmpty('merchantPacket', $this->getMerchantPacket());
        Validator::validateNotEmpty('bankPacket', $this->getBankPacket());
        Validator::validateNotEmpty('sign', $this->getSign());
    }

    public function toXmlString(Setting $setting)
    {
        $this->validate();

        $credential = $setting->getCredential();

        /*
         * Create element
         */
        $elements = array(
            "mid" => $credential->getMerchantId(),
            "tid" => $credential->getTerminalId(),
            "oosResolveMerchantData" => array(
                "bankData" => $this->getBankPacket(),
                "merchantData" => $this->getMerchantPacket(),
                "sign" => $this->getSign(),
            )
        );

        return Helper::arrayToXmlString($elements);
    }
}