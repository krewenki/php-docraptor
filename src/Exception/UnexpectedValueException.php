<?php namespace DocRaptor\Exception;

class UnexpectedValueException extends \UnexpectedValueException implements DocRaptorException
{
    public function __construct($code = 0)
    {
        parent::__construct('DocRaptor returned an unexpected response ('.$code.')', $code);
    }
}