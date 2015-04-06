<?php namespace DocRaptor\Exception;

class ForbiddenException extends RuntimeException
{
    public function __construct()
    {
        parent::__construct('The server refuses to respond. Are you making too much simulataneous requests?', 403);
    }
}
