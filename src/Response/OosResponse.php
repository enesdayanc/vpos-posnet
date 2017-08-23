<?php
/**
 * Created by PhpStorm.
 * User: enesdayanc
 * Date: 23/08/2017
 * Time: 10:40
 */

namespace PaymentGateway\VPosPosnet\Response;


class OosResponse
{
    private $data1;
    private $data2;
    private $sign;
    /** @var  bool */
    private $valid = false;
    private $requestRawData;
    private $rawData;

    /**
     * @return mixed
     */
    public function getData1()
    {
        return $this->data1;
    }

    /**
     * @param mixed $data1
     */
    public function setData1($data1)
    {
        $this->data1 = $data1;
    }

    /**
     * @return mixed
     */
    public function getData2()
    {
        return $this->data2;
    }

    /**
     * @param mixed $data2
     */
    public function setData2($data2)
    {
        $this->data2 = $data2;
    }

    /**
     * @return mixed
     */
    public function getSign()
    {
        return $this->sign;
    }

    /**
     * @param mixed $sign
     */
    public function setSign($sign)
    {
        $this->sign = $sign;
    }

    /**
     * @return bool
     */
    public function isValid(): bool
    {
        return $this->valid;
    }

    /**
     * @param bool $valid
     */
    public function setValid(bool $valid)
    {
        $this->valid = $valid;
    }

    /**
     * @return mixed
     */
    public function getRequestRawData()
    {
        return $this->requestRawData;
    }

    /**
     * @param mixed $requestRawData
     */
    public function setRequestRawData($requestRawData)
    {
        $this->requestRawData = $requestRawData;
    }

    /**
     * @return mixed
     */
    public function getRawData()
    {
        return $this->rawData;
    }

    /**
     * @param mixed $rawData
     */
    public function setRawData($rawData)
    {
        $this->rawData = $rawData;
    }
}