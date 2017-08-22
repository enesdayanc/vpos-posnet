<?php
/**
 * Created by PhpStorm.
 * User: enesdayanc
 * Date: 22/08/2017
 * Time: 10:18
 */

namespace PaymentGateway\VPosPosnet\Request;

use PaymentGateway\VPosPosnet\Constant\RequestCurrencyCode;
use PaymentGateway\VPosPosnet\Helper\Helper;
use PaymentGateway\VPosPosnet\Helper\Validator;
use PaymentGateway\VPosPosnet\Model\Card;
use PaymentGateway\VPosPosnet\Setting\Setting;

class PurchaseRequest implements RequestInterface
{
    /** @var  Card */
    private $card;
    private $orderId;
    private $amount;
    private $installment;
    /** @var  bool */
    private $useKoi = false;

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
     * @return bool
     */
    public function isUseKoi(): bool
    {
        return $this->useKoi;
    }

    /**
     * @param bool $useKoi
     */
    public function setUseKoi(bool $useKoi)
    {
        $this->useKoi = $useKoi;
    }

    public function validate()
    {
        Validator::validateNotEmpty('card', $this->getCard());
        $this->getCard()->validate();
        Validator::validateAmount($this->getAmount());
        Validator::validateInstallment($this->getInstallment());
        Validator::validateOrderId($this->getOrderId());
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

        /*
         * Create sale
         */
        $sale = array(
            "ccno" => $card->getCreditCardNumber(),
            "cvc" => $card->getCvv(),
            "expDate" => $card->getExpires(),
            "amount" => Helper::amountParser($this->getAmount()),
            "currencyCode" => RequestCurrencyCode::YT,
            "orderID" => Helper::orderIdParser($this->getOrderId()),
        );

        /*
         * Check Installment
         */
        if ($this->getInstallment() > 1) {
            $sale['installment'] = $this->getInstallment();
        }

        /*
         * Check Use Koi
         */
        if ($this->isUseKoi()) {
            $sale['koiCode'] = 1;
        }

        /*
         * Add sale to element
         */
        $elements['sale'] = $sale;

        return Helper::arrayToXmlString($elements);
    }
}