<?php

namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\Exception\UnexpectedTypeException;
use Symfony\Component\Validator\Exception\UnexpectedValueException;

class ValidCurrencyValidator extends ConstraintValidator
{
    private array $currencies;

    public function __construct(array $currencies)
    {
        $this->currencies = $currencies;
    }

    public function validate($value, Constraint $constraint)
    {
        if (!$constraint instanceof ValidCurrency) {
            throw new UnexpectedTypeException($constraint, ValidCurrency::class);
        }

        if (null === $value || '' === $value) {
            return;
        }

        if (!is_string($value)) {
            throw new UnexpectedValueException($value, 'string');
        }
        
        if (!in_array($value, $this->currencies)) {
            $this->context->buildViolation($constraint->message)->addViolation();
        }
    }
}
