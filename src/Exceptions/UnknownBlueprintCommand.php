<?php

namespace SchemaManager\Exceptions;

use Exception;

class UnknownBlueprintCommand extends Exception
{
    public function __construct($command)
    {
        parent::__construct('The '.$command.' method has not been implemented.');
    }
}
