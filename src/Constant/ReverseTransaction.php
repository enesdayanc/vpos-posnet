<?php
/**
 * Created by PhpStorm.
 * User: enesdayanc
 * Date: 22/08/2017
 * Time: 16:20
 */

namespace PaymentGateway\VPosPosnet\Constant;


class ReverseTransaction
{
    const SALE = 'sale';
    const AUTH = 'auth';
    const CAPT = 'capt';
    const RETURN = 'return';
}