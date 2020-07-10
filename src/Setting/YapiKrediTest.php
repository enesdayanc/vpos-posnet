<?php
/**
 * Created by PhpStorm.
 * User: enesdayanc
 * Date: 22/08/2017
 * Time: 11:44
 */

namespace PaymentGateway\VPosPosnet\Setting;


use PaymentGateway\VPosPosnet\Constant\MdStatus;

class YapiKrediTest extends Setting
{

    public function getThreeDPostUrl()
    {
        return 'https://setmpos.ykb.com/3DSWebService/YKBPaymentService';
    }

    public function getAuthorizeUrl()
    {
        return 'https://setmpos.ykb.com/PosnetWebService/XML';
    }

    public function getCaptureUrl()
    {
        return 'https://setmpos.ykb.com/PosnetWebService/XML';
    }

    public function getPurchaseUrl()
    {
        return 'https://setmpos.ykb.com/PosnetWebService/XML';
    }

    public function getRefundUrl()
    {
        return 'https://setmpos.ykb.com/PosnetWebService/XML';
    }

    public function getVoidUrl()
    {
        return 'https://setmpos.ykb.com/PosnetWebService/XML';
    }

    public function getOosUrl()
    {
        return 'https://setmpos.ykb.com/PosnetWebService/XML';
    }


    public function getAllowedThreeDMdStatus(): array
    {
        return array(
            MdStatus::ONE,
            MdStatus::TWO,
            MdStatus::THREE,
            MdStatus::FOUR,
            MdStatus::NINE, //only for test env
        );
    }
}