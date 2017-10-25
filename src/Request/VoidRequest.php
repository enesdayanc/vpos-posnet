<?php
/**
 * Created by PhpStorm.
 * User: enesdayanc
 * Date: 22/08/2017
 * Time: 16:24
 */

namespace PaymentGateway\VPosPosnet\Request;


use PaymentGateway\VPosPosnet\Helper\Helper;
use PaymentGateway\VPosPosnet\Helper\Validator;
use PaymentGateway\VPosPosnet\Setting\Setting;

class VoidRequest implements RequestInterface
{
    private $orderId;
    private $reverseTransaction;

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
    public function getReverseTransaction()
    {
        return $this->reverseTransaction;
    }

    /**
     * @param mixed $reverseTransaction
     */
    public function setReverseTransaction($reverseTransaction)
    {
        $this->reverseTransaction = $reverseTransaction;
    }

    public function validate()
    {
        Validator::validateNotEmpty('orderId', $this->getOrderId());
        Validator::validateReverseTransaction($this->getReverseTransaction());
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
            "reverse" => array(
                "transaction" => $this->getReverseTransaction(),
                "orderID" => Helper::orderIdParser($this->getOrderId()),
            ),
        );

        return Helper::arrayToXmlString($elements);
    }
}