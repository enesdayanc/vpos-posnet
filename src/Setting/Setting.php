<?php
/**
 * Created by PhpStorm.
 * User: enesdayanc
 * Date: 22/08/2017
 * Time: 10:28
 */

namespace PaymentGateway\VPosPosnet\Setting;


use PaymentGateway\VPosPosnet\Helper\Validator;

abstract class Setting
{
    /** @var  Credential $credential */
    private $credential;
    private $threeDReturnUrl;

    /**
     * @return Credential
     */
    public function getCredential()
    {
        return $this->credential;
    }

    /**
     * @param Credential $credential
     */
    public function setCredential($credential)
    {
        $this->credential = $credential;
    }

    /**
     * @return mixed
     */
    public function getThreeDReturnUrl()
    {
        return $this->threeDReturnUrl;
    }

    /**
     * @param mixed $threeDReturnUrl
     */
    public function setThreeDReturnUrl($threeDReturnUrl)
    {
        $this->threeDReturnUrl = $threeDReturnUrl;
    }

    public function validate()
    {
        Validator::validateNotEmpty('credential', $this->getCredential());
        $this->getCredential()->validate();
        Validator::validateNotEmpty('purchaseUrl', $this->getPurchaseUrl());
        Validator::validateNotEmpty('authorizeUrl', $this->getAuthorizeUrl());
        Validator::validateNotEmpty('captureUrl', $this->getCaptureUrl());
        Validator::validateNotEmpty('voidUrl', $this->getVoidUrl());
        Validator::validateNotEmpty('refundUrl', $this->getRefundUrl());
        Validator::validateNotEmpty('threeDReturnUrl', $this->getThreeDReturnUrl());
    }

    public abstract function getThreeDPostUrl();

    public abstract function getAuthorizeUrl();

    public abstract function getCaptureUrl();

    public abstract function getPurchaseUrl();

    public abstract function getRefundUrl();

    public abstract function getVoidUrl();

    public abstract function getOosUrl();

    public abstract function getAllowedThreeDMdStatus() : array;
}