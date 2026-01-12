<?php

namespace App\Calculators;

/**
 * Enterprise Pipeline (Formerly EnterpriseCalculator)
 * 
 * Renamed to force cache invalidation.
 * The foundation of the Unified Calculator Engine.
 * Enforces a strict pipeline: Geometry -> Materials -> Cost -> Related Items.
 */
abstract class EnterprisePipeline
{
    protected $materialService;
    protected $costService;
    protected $unitService;

    public function __construct(
        \App\Services\Calculator\MaterialService $materialService = null,
        \App\Services\Calculator\CostService $costService = null,
        \App\Services\Calculator\UnitService $unitService = null
    ) {
        // Allow external injection (Testability) or Default to Service (Convenience)
        $this->materialService = $materialService ?? new \App\Services\Calculator\MaterialService();
        $this->costService = $costService ?? new \App\Services\Calculator\CostService();
        $this->unitService = $unitService ?? new \App\Services\Calculator\UnitService();
    }

    /**
     * Validate inputs
     * 
     * Default implementation. Concrete classes can override this.
     */
    public function validate($inputs)
    {
        return [
            'valid' => is_array($inputs) && !empty($inputs),
            'errors' => []
        ];
    }

    /**
     * Execute the Full Calculation Pipeline
     * Alias for execute() to match expected Interface
     */
    public function calculate($inputs)
    {
        return $this->execute($inputs);
    }

    /**
     * Execute the Full Calculation Pipeline
     */
    public function execute($inputs)
    {
        // SECURITY & STABILITY: Enforce mandatory validation before processing
        $validation = $this->validate($inputs);
        if (!$validation['valid']) {
            $errorMsg = !empty($validation['errors']) ? implode(', ', $validation['errors']) : 'Invalid inputs provided';
            throw new \Exception("Calculation Pipeline Error in " . static::class . ": " . $errorMsg);
        }

        // Step 1: Geometry (Volume, Area, etc.)
        $geometry = $this->calculateGeometry($inputs);

        // Step 2: Enrich with Materials (Count -> Cement/Sand)
        $materials = $this->enrichMaterials($geometry);

        // Step 3: Enrich with Cost (Materials -> $$$)
        $cost = $this->enrichCost($materials);

        // Step 4: Check for Related Items (e.g., Wall triggers Plaster)
        $related = $this->getRelatedItems($geometry);

        return [
            'geometry' => $geometry,
            'materials' => $materials,
            'cost' => $cost,
            'related_items' => $related,
            'meta' => [
                'calculator' => static::class,
                'timestamp' => date('c')
            ]
        ];
    }

    /**
     * Calculate core physical dimensions (must be implemented by concrete class)
     */
    abstract protected function calculateGeometry(array $inputs): array;

    /**
     * Convert physical dimensions to material quantities
     */
    abstract protected function enrichMaterials(array $geometry): array;

    /**
     * Apply current market rates to materials
     */
    protected function enrichCost(array $materials): array
    {
        return $this->costService->calculateCost($materials);
    }

    /**
     * Identify related calculators to run (Optional)
     */
    protected function getRelatedItems(array $geometry): array
    {
        return [];
    }
}
