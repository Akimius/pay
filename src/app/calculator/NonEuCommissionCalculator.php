<?php

declare(strict_types=1);

namespace App\app\calculator;

class NonEuCommissionCalculator implements CalculatorInterface
{
    private const RATE =  0.02;

    public function calculate(float $amount, float $exchangeRate): float
    {
        return round($amount * $this->getRate() / $exchangeRate, static::PRECISION); // TODO: Division by zero was ignored as the valid rates are always greater than "0" (TBD)
    }

    public function getRate(): float
    {
        return static::RATE;
    }
}
