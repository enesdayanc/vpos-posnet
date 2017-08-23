<?php
/**
 * Created by PhpStorm.
 * User: enesdayanc
 * Date: 23/08/2017
 * Time: 16:26
 */

namespace PaymentGateway\VPosPosnet\Setting;


use PaymentGateway\VPosPosnet\Constant\MdStatus;

class YapiKredi extends Setting
{

    public function getThreeDPostUrl()
    {
        return 'https://www.posnet.ykb.com/3DSWebService/YKBPaymentService';
    }

    public function getAuthorizeUrl()
    {
        return 'https://www.posnet.ykb.com/PosnetWebService/XML';
    }

    public function getCaptureUrl()
    {
        return 'https://www.posnet.ykb.com/PosnetWebService/XML';
    }

    public function getPurchaseUrl()
    {
        return 'https://www.posnet.ykb.com/PosnetWebService/XML';
    }

    public function getRefundUrl()
    {
        return 'https://www.posnet.ykb.com/PosnetWebService/XML';
    }

    public function getVoidUrl()
    {
        return 'https://www.posnet.ykb.com/PosnetWebService/XML';
    }

    public function getOosUrl()
    {
        return 'https://www.posnet.ykb.com/PosnetWebService/XML';
    }

    public function getAllowedThreeDMdStatus(): array
    {
        return array(
            MdStatus::ONE,
            MdStatus::TWO,
            MdStatus::THREE,
            MdStatus::FOUR,
        );
    }
}