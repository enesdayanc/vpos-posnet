<?php
/**
 * Created by PhpStorm.
 * User: enesdayanc
 * Date: 22/08/2017
 * Time: 10:18
 */

namespace PaymentGateway\VPosPosnet\Request;

use PaymentGateway\ISO4217\Model\Currency;
use PaymentGateway\VPosPosnet\Constant\Language;
use PaymentGateway\VPosPosnet\Constant\RedirectFormMethod;
use PaymentGateway\VPosPosnet\Helper\Helper;
use PaymentGateway\VPosPosnet\Helper\Validator;
use PaymentGateway\VPosPosnet\HttpClient;
use PaymentGateway\VPosPosnet\Model\Card;
use PaymentGateway\VPosPosnet\Model\RedirectForm;
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
        Validator::validateCurrency($this->getCurrency());
    }

    public function toXmlString(Setting $setting, bool $maskCardData = false)
    {
        $setting->validate();
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
            "ccno" => $card->getCreditCardNumber($maskCardData),
            "cvc" => $card->getCvv($maskCardData),
            "expDate" => $card->getExpires($maskCardData),
            "amount" => Helper::amountParser($this->getAmount()),
            "currencyCode" => Helper::getRequestCurrencyCodeFromISO4217($this->getCurrency()),
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

    public function get3DRedirectForm(Setting $setting, $oosRequestDataType)
    {
        $this->validate();

        $credential = $setting->getCredential();

        $oosResponse = $this->getOosResponse($setting, $oosRequestDataType);

        $params = array(
            "mid" => $credential->getMerchantId(),
            "posnetID" => $credential->getPosnetId(),
            "posnetData" => $oosResponse->getData1(),
            "posnetData2" => $oosResponse->getData2(),
            "digest" => $oosResponse->getSign(),
            "merchantReturnURL" => $setting->getThreeDReturnUrl(),
            "lang" => Language::TR,
        );

        $redirectForm = new RedirectForm();
        $redirectForm->setAction($setting->getThreeDPostUrl());
        $redirectForm->setMethod(RedirectFormMethod::POST);
        $redirectForm->setParameters($params);

        return $redirectForm;
    }


    private function getOosResponse(Setting $setting, $oosRequestDataType)
    {
        $oosRequest = new OosRequest();

        $oosRequest->setInstallment($this->getInstallment());
        $oosRequest->setAmount($this->getAmount());
        $oosRequest->setOrderId($this->getOrderId());
        $oosRequest->setCard($this->getCard());
        $oosRequest->setOosRequestDataType($oosRequestDataType);
        $oosRequest->setCurrency($this->getCurrency());

        $httpClient = new HttpClient($setting);

        return $httpClient->sendOos($oosRequest, $setting->getOosUrl());
    }
}