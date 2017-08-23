<?php
/**
 * Created by PhpStorm.
 * User: enesdayanc
 * Date: 23/08/2017
 * Time: 14:54
 */

namespace PaymentGateway\VPosPosnet\Constant;


class MdStatus
{
    const ZERO = 0; // Risk Owner: Merchant
    const ONE = 1; // Risk Owner: Card Owner or Bank
    const TWO = 2; // Risk Owner: Card Owner or Bank
    const THREE = 3; // Risk Owner: Card Owner or Bank
    const FOUR = 4; // Risk Owner: Card Owner or Bank
    const FIVE = 5; // Risk Owner: Merchant
    const SIX = 6; // Risk Owner: Merchant
    const SEVEN = 7; // Risk Owner: Merchant
    const EIGHTH = 8; // Risk Owner: Merchant
    const NINE = 9; // Risk Owner: Merchant or Card Owner or Bank (Without 3D rules)
}