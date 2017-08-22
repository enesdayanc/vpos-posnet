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
    private $transactionReference;
    private $reverseTransaction;

    /**
     * @return mixed
     */
    public function getTransactionReference()
    {
        return $this->transactionReference;
    }

    /**
     * @param mixed $transactionReference
     */
    public function setTransactionReference($transactionReference)
    {
        $this->transactionReference = $transactionReference;
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
        Validator::validateNotEmpty('transactionReference', $this->getTransactionReference());
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
                "hostLogKey" => $this->getTransactionReference(),
            ),
        );

        return Helper::arrayToXmlString($elements);
    }
}