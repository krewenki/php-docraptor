<?php namespace DocRaptor\Exception;

class BadRequestException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('There was an error performing your request.', 400);
    }
}