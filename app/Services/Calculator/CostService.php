<?php

namespace App\Services\Calculator;

/**
 * Cost Service
 * 
 * Manages Rate Analysis and Bill of Quantities (BOQ).
 * Pulls rates from Database (or defaults for MVP).
 */
class CostService
{
    // MVP Hardcoded Rates (Should replace with DB Lookup)
    private $rates = [
        'bricks' => 18,      // NPR per piece
        'cement' => 850,     // NPR per bag
        'sand' => 3500,      // NPR per m3
        'aggregate' => 4500, // NPR per m3
        'paint' => 600,      // NPR per liter
        'primer' => 450,     // NPR per liter
        'plaster_labor' => 250, // NPR per m2
        'conduit' => 150,    // per m
        'wire' => 85         // per m
    ];

    public function calculateCost(array $materials, $location_id = null)
    {
        // $location_id is a placeholder for future Multi-Region Price support
        $boq = [];
        $totalCost = 0;

        foreach ($materials as $key => $item) {
            // Find rate - check exact name, generic key, or item_id/name within the item
            $identifier = is_string($key) ? $key : ($item['item_id'] ?? $item['name'] ?? '');
            $rateKey = strtolower($identifier);
            $rate = 0;
            
            if (isset($this->rates[$rateKey])) {
                $rate = $this->rates[$rateKey];
            }

            $quantity = $item['quantity'] ?? 0;
            $itemCost = $quantity * $rate;
            $totalCost += $itemCost;

            $boq[$identifier ?: $key] = array_merge($item, [
                'rate' => $rate,
                'amount' => $itemCost,
                'currency' => 'NPR'
            ]);
        }

        return [
            'line_items' => $boq,
            'total_amount' => $totalCost,
            'currency' => 'NPR'
        ];
    }
}
