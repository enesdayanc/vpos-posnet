<?php
/**
 * Created by PhpStorm.
 * User: enesdayanc
 * Date: 22/08/2017
 * Time: 15:24
 */

namespace PaymentGateway\VPosPosnet\Request;

use PaymentGateway\ISO4217\Model\Currency;
use PaymentGateway\VPosPosnet\Helper\Helper;
use PaymentGateway\VPosPosnet\Helper\Validator;
use PaymentGateway\VPosPosnet\Setting\Setting;

class CaptureRequest implements RequestInterface
{
    private $amount;
    private $transactionReference;
    private $installment;
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
     * @return Currency
     */
    public function getCurrency()
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
        Validator::validateInstallment($this->getInstallment());
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
        );

        /*
         * Create capt
         */
        $capt = array(
            "amount" => Helper::amountParser($this->getAmount()),
            "currencyCode" => Helper::getRequestCurrencyCodeFromISO4217($this->getCurrency()),
            "hostLogKey" => $this->getTransactionReference(),
        );

        /*
         * Check Installment
         */
        if ($this->getInstallment() > 1) {
            $capt['installment'] = $this->getInstallment();
        }

        /*
         * Add sale to element
         */
        $elements['capt'] = $capt;

        return Helper::arrayToXmlString($elements);
    }
}