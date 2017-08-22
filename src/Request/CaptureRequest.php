<?php
/**
 * Created by PhpStorm.
 * User: enesdayanc
 * Date: 22/08/2017
 * Time: 15:24
 */

namespace PaymentGateway\VPosPosnet\Request;

use PaymentGateway\VPosPosnet\Constant\RequestCurrencyCode;
use PaymentGateway\VPosPosnet\Helper\Helper;
use PaymentGateway\VPosPosnet\Helper\Validator;
use PaymentGateway\VPosPosnet\Setting\Setting;

class CaptureRequest implements RequestInterface
{
    private $amount;
    private $transactionReference;
    private $installment;

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

    public function validate()
    {
        Validator::validateAmount($this->getAmount());
        Validator::validateNotEmpty('transactionReference', $this->getTransactionReference());
        Validator::validateInstallment($this->getInstallment());
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
            "currencyCode" => RequestCurrencyCode::YT,
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