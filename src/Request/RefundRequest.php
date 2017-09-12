<?php
/**
 * Created by PhpStorm.
 * User: enesdayanc
 * Date: 22/08/2017
 * Time: 15:58
 */

namespace PaymentGateway\VPosPosnet\Request;


use PaymentGateway\ISO4217\Model\Currency;
use PaymentGateway\VPosPosnet\Helper\Helper;
use PaymentGateway\VPosPosnet\Helper\Validator;
use PaymentGateway\VPosPosnet\Setting\Setting;

class RefundRequest implements RequestInterface
{
    private $amount;
    private $transactionReference;
    /** @var  Currency */
    private $currency;

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

    /**
     * @return Currency
     */
    public function getCurrency(): Currency
    {
        return $this->currency;
    }

    /**
     * @param Currency $currency
     */
    public function setCurrency(Currency $currency)
    {
        $this->currency = $currency;
    }

    public function validate()
    {
        Validator::validateAmount($this->getAmount());
        Validator::validateNotEmpty('transactionReference', $this->getTransactionReference());
        Validator::validateCurrency($this->getCurrency());
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
                "currencyCode" => Helper::getRequestCurrencyCodeFromISO4217($this->getCurrency()),
            ),
        );

        return Helper::arrayToXmlString($elements);
    }
}