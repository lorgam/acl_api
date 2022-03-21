<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;

/**
 * @Annotation
 */
class ValidCurrency extends Constraint
{
    public $message = 'Currency not valid';
    public $mode = 'strict';
}
