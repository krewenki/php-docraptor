<?php namespace DocRaptor\Exception;

class MissingAPIKeyException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct("No API Key was provided.");
    }
}