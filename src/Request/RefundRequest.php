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
    private $orderId;
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
        Validator::validateNotEmpty('orderId', $this->getOrderId());
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
                "orderID" => Helper::orderIdParser($this->getOrderId()),
                "currencyCode" => Helper::getRequestCurrencyCodeFromISO4217($this->getCurrency()),
            ),
        );

        return Helper::arrayToXmlString($elements);
    }
}