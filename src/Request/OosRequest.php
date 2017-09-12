<?php
/**
 * Created by PhpStorm.
 * User: enesdayanc
 * Date: 23/08/2017
 * Time: 10:43
 */

namespace PaymentGateway\VPosPosnet\Request;


use PaymentGateway\ISO4217\Model\Currency;
use PaymentGateway\VPosPosnet\Helper\Helper;
use PaymentGateway\VPosPosnet\Helper\Validator;
use PaymentGateway\VPosPosnet\Model\Card;
use PaymentGateway\VPosPosnet\Setting\Setting;

class OosRequest implements RequestInterface
{

    /** @var  Card */
    private $card;
    private $amount;
    private $orderId;
    private $installment;
    private $oosRequestDataType;
    /** @var  Currency */
    private $currency;

    /**
     * @return Card
     */
    public function getCard(): Card
    {
        return $this->card;
    }

    /**
     * @param Card $card
     */
    public function setCard(Card $card)
    {
        $this->card = $card;
    }

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
    public function getOosRequestDataType()
    {
        return $this->oosRequestDataType;
    }

    /**
     * @param mixed $oosRequestDataType
     */
    public function setOosRequestDataType($oosRequestDataType)
    {
        $this->oosRequestDataType = $oosRequestDataType;
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
        Validator::validateNotEmpty('card', $this->getCard());
        $this->getCard()->validate();
        Validator::validateAmount($this->getAmount());
        Validator::validateInstallment($this->getInstallment());
        Validator::validateOrderId($this->getOrderId());
        Validator::validateOosRequestDataType($this->getOosRequestDataType());
        Validator::validateCurrency($this->getCurrency());
    }

    public function toXmlString(Setting $setting)
    {
        $this->validate();

        $credential = $setting->getCredential();

        $card = $this->getCard();

        /*
         * Create element
         */
        $elements = array(
            "mid" => $credential->getMerchantId(),
            "tid" => $credential->getTerminalId(),
        );

        $ooRequestData = array(
            "posnetid" => $credential->getPosnetId(),
            "ccno" => $card->getCreditCardNumber(),
            "expDate" => $card->getExpires(),
            "cvc" => $card->getCvv(),
            "amount" => Helper::amountParser($this->getAmount()),
            "currencyCode" => Helper::getRequestCurrencyCodeFromISO4217($this->getCurrency()),
            "XID" => Helper::orderIdParser($this->getOrderId(), 20),
            "cardHolderName" => $card->getFullName(),
            "tranType" => $this->getOosRequestDataType(),
        );

        if ($this->getInstallment() > 1) {
            $ooRequestData['installment'] = $this->getInstallment();
        } else {
            $ooRequestData['installment'] = '00';
        }

        $elements['oosRequestData'] = $ooRequestData;

        return Helper::arrayToXmlString($elements);
    }
}