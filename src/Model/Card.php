<?php
/**
 * Created by PhpStorm.
 * User: enesdayanc
 * Date: 02/08/2017
 * Time: 14:32
 */

namespace PaymentGateway\VPosPosnet\Model;


use PaymentGateway\VPosPosnet\Helper\Validator;

class Card
{
    private $creditCardNumber;
    private $expiryMonth;
    private $expiryYear;
    private $cvv;

    /**
     * @return mixed
     */
    public function getCreditCardNumber()
    {
        return $this->creditCardNumber;
    }

    /**
     * @param mixed $creditCardNumber
     */
    public function setCreditCardNumber($creditCardNumber)
    {
        $this->creditCardNumber = $creditCardNumber;
    }

    /**
     * @return mixed
     */
    public function getExpiryMonth()
    {
        return $this->expiryMonth;
    }

    /**
     * @param mixed $expiryMonth
     */
    public function setExpiryMonth($expiryMonth)
    {
        $this->expiryMonth = $expiryMonth;
    }

    /**
     * @return mixed
     */
    public function getExpiryYear()
    {
        return $this->expiryYear;
    }

    /**
     * @param mixed $expiryYear
     */
    public function setExpiryYear($expiryYear)
    {
        $this->expiryYear = $expiryYear;
    }

    /**
     * @return mixed
     */
    public function getCvv()
    {
        return $this->cvv;
    }

    /**
     * @param mixed $cvv
     */
    public function setCvv($cvv)
    {
        $this->cvv = $cvv;
    }

    public function getExpires()
    {
        return $this->getExpiryYear() . $this->getExpiryMonth();
    }

    public function validate()
    {
        Validator::validateCardNumber($this->getCreditCardNumber());
        Validator::validateExpiryMonth($this->getExpiryMonth());
        Validator::validateExpiryYear($this->getExpiryYear());
        Validator::validateCvv($this->getCvv());
    }
}