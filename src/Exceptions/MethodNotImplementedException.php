<?php

namespace SchemaManager\Exceptions;

use Exception;

class MethodNotImplementedException extends Exception
{
    public function __construct($method)
    {
        parent::__construct("The method \"$method\" has not been implemented.");
    }
}
