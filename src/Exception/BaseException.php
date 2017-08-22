<?php
/**
 * Created by PhpStorm.
 * User: enesdayanc
 * Date: 28/07/2017
 * Time: 15:06
 */

namespace PaymentGateway\VPosPosnet\Exception;

use Exception;
use Throwable;

abstract class BaseException extends Exception
{
    private $userMessage;
    private $internalMessage;

    public function __construct($userMessage, $internalMessage, $code = 0, Throwable $previous = null)
    {
        parent::__construct($userMessage, $code, $previous);
        $this->setUserMessage($userMessage);
        $this->setInternalMessage($internalMessage);
    }

    /**
     * @return mixed
     */
    public function getUserMessage()
    {
        return $this->userMessage;
    }

    /**
     * @param mixed $userMessage
     */
    public function setUserMessage($userMessage)
    {
        $this->userMessage = $userMessage;
    }

    /**
     * @return mixed
     */
    public function getInternalMessage()
    {
        return $this->internalMessage;
    }

    /**
     * @param mixed $internalMessage
     */
    public function setInternalMessage($internalMessage)
    {
        $this->internalMessage = $internalMessage;
    }
}