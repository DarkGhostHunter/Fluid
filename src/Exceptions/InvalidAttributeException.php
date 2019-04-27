<?php

namespace DarkGhostHunter\Fluid\Exceptions;

use DarkGhostHunter\Fluid\Fluid;
use Exception;

class InvalidAttributeException extends Exception
{
    public function __construct(string $attribute, Fluid $instance)
    {
        $this->message = "Attribute [$attribute] in not set as fillable in " .
            substr(strrchr(get_class($instance), "\\"), 1) . '.';

        parent::__construct();
    }
}