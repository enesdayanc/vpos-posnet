<?php
/**
 * Created by PhpStorm.
 * User: enesdayanc
 * Date: 22/08/2017
 * Time: 15:18
 */

namespace PaymentGateway\VPosPosnet\Request;


use PaymentGateway\VPosPosnet\Constant\RequestCurrencyCode;
use PaymentGateway\VPosPosnet\Helper\Helper;
use PaymentGateway\VPosPosnet\Setting\Setting;

class AuthorizeRequest extends PurchaseRequest
{
    public function toXmlString(Setting $setting, bool $maskCardData = false)
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
         * Create auth
         */
        $auth = array(
            "ccno" => $card->getCreditCardNumber($maskCardData),
            "cvc" => $card->getCvv($maskCardData),
            "expDate" => $card->getExpires($maskCardData),
            "amount" => Helper::amountParser($this->getAmount()),
            "currencyCode" => RequestCurrencyCode::YT,
            "orderID" => Helper::orderIdParser($this->getOrderId()),
        );

        /*
         * Check Installment
         */
        if ($this->getInstallment() > 1) {
            $auth['installment'] = $this->getInstallment();
        }

        /*
         * Check Use Koi
         */
        if ($this->isUseKoi()) {
            $auth['koiCode'] = 1;
        }

        /*
         * Add sale to element
         */
        $elements['auth'] = $auth;

        return Helper::arrayToXmlString($elements);
    }
}