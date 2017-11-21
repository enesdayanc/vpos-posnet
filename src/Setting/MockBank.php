<?php
/**
 * Created by PhpStorm.
 * User: enesdayanc
 * Date: 21.11.2017
 * Time: 17:14
 */

namespace PaymentGateway\VPosPosnet\Setting;


use PaymentGateway\VPosPosnet\Constant\MdStatus;

class MockBank extends Setting
{
    /** @var  string */
    private $host;

    /**
     * @return string
     */
    public function getHost(): string
    {
        return $this->host;
    }

    /**
     * @param string $host
     */
    public function setHost(string $host)
    {
        $this->host = $host;
    }

    public function getThreeDPostUrl()
    {
        return $this->getHost() . "/three-d-post";
    }

    public function getAuthorizeUrl()
    {
        return $this->getHost() . "/authorize";
    }

    public function getCaptureUrl()
    {
        return $this->getHost() . "/capture";
    }

    public function getPurchaseUrl()
    {
        return $this->getHost() . "/purchase";
    }

    public function getRefundUrl()
    {
        return $this->getHost() . "/refund";
    }

    public function getVoidUrl()
    {
        return $this->getHost() . "/void";
    }

    public function getOosUrl()
    {
        return $this->getHost() . "/oos";
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