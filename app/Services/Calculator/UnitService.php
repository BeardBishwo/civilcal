<?php

namespace App\Services\Calculator;

/**
 * Unit Service
 * 
 * Handles all unit conversions (Length, Area, Volume).
 */
class UnitService
{
    public function toMeters($value, $fromUnit)
    {
        switch (strtolower($fromUnit)) {
            case 'mm': return $value / 1000;
            case 'cm': return $value / 100;
            case 'm': return $value;
            case 'in': return $value * 0.0254;
            case 'ft': return $value * 0.3048;
            default: return $value;
        }
    }

    public function toSquareMeters($value, $fromUnit)
    {
        switch (strtolower($fromUnit)) {
            case 'sqmm': return $value / 1000000;
            case 'sqcm': return $value / 10000;
            case 'sqm':  return $value;
            case 'sqin': return $value * 0.00064516;
            case 'sqft': return $value * 0.092903;
            default: return $value;
        }
    }

    public function toCubicMeters($value, $fromUnit)
    {
        switch (strtolower($fromUnit)) {
            case 'mm3': return $value / 1000000000;
            case 'cm3': return $value / 1000000;
            case 'm3':  return $value;
            case 'in3': return $value * 0.000016387;
            case 'ft3': return $value * 0.0283168;
            default: return $value;
        }
    }
}
