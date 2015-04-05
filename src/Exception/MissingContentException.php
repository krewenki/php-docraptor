<?php namespace DocRaptor\Exception;

class MissingContentException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct("You must provide either content or an url as a source.");
    }
}