<?php
/**
 * Created by PhpStorm.
 * User: enesdayanc
 * Date: 22/08/2017
 * Time: 10:27
 */

namespace PaymentGateway\VPosPosnet\Request;


use PaymentGateway\VPosPosnet\Setting\Setting;

interface RequestInterface
{
    public function getType();

    public function validate();

    public function toXmlString(Setting $setting);

}