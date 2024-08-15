<?php

namespace App\app\calculator;

interface CalculatorInterface
{
    public const PRECISION = 2;

    public function calculate(float $amount, float $exchangeRate): float;
    public function getRate(): float;
}