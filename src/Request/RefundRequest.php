<?php
/**
 * Created by PhpStorm.
 * User: enesdayanc
 * Date: 22/08/2017
 * Time: 15:58
 */

namespace PaymentGateway\VPosPosnet\Request;


use PaymentGateway\VPosPosnet\Constant\RequestCurrencyCode;
use PaymentGateway\VPosPosnet\Helper\Helper;
use PaymentGateway\VPosPosnet\Helper\Validator;
use PaymentGateway\VPosPosnet\Setting\Setting;

class RefundRequest implements RequestInterface
{
    private $amount;
    private $transactionReference;

    /**
     * @return mixed
     */
    public function getAmount()
    {
        return $this->amount;
    }

    /**
     * @param mixed $amount
     */
    public function setAmount($amount)
    {
        $this->amount = $amount;
    }

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

    public function validate()
    {
        Validator::validateAmount($this->getAmount());
        Validator::validateNotEmpty('transactionReference', $this->getTransactionReference());
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
            "return" => array(
                "amount" => Helper::amountParser($this->getAmount()),
                "hostLogKey" => $this->getTransactionReference(),
                "currencyCode" => RequestCurrencyCode::YT,
            ),
        );

        return Helper::arrayToXmlString($elements);
    }
}