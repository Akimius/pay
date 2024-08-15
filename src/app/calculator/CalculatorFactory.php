<?php

namespace App\app\calculator;

use App\app\enums\EuCountry;

final class CalculatorFactory
{
    public static function make(string $countryCode): CalculatorInterface
    {
        return match (true) {
            EuCountry::isEuCountry($countryCode) => new EuCommissionCalculator(),
            default                              => new NonEuCommissionCalculator(),
        };
    }
}
