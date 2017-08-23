<?php
/**
 * Created by PhpStorm.
 * User: enesdayanc
 * Date: 23/08/2017
 * Time: 14:59
 */

namespace PaymentGateway\VPosPosnet\Request;


use PaymentGateway\VPosPosnet\Helper\Helper;
use PaymentGateway\VPosPosnet\Helper\Validator;
use PaymentGateway\VPosPosnet\Setting\Setting;

class OosTranRequest implements RequestInterface
{
    private $bankPacket;

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

    public function validate()
    {
        Validator::validateNotEmpty('bankPacket', $this->getBankPacket());
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
            "oosTranData" => array(
                "bankData" => $this->getBankPacket(),
            ),
        );

        return Helper::arrayToXmlString($elements);
    }
}