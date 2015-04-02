<?php namespace DocRaptor\Exception;


class UnprocessableEntityException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('Your document contains syntax errors and DocRaptor cannot process it.', 433);
    }
}