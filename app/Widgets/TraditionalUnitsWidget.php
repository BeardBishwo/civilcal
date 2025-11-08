<?php

namespace App\Widgets;

use App\Services\GeolocationService;
use App\Calculators\TraditionalUnitsCalculator;

/**
 * TraditionalUnitsWidget - Geolocation-aware traditional Nepali units calculator widget
 * 
 * This widget automatically detects Nepali users and provides easy access to
 * traditional Nepali measurement units conversion.
 */
class TraditionalUnitsWidget extends BaseWidget
{
    private GeolocationService $geolocationService;
    private TraditionalUnitsCalculator $calculator;
    private bool $isForNepaliUser = false;
    
    /**
     * Initialize widget
     */
    protected function initialize(): void
    {
        $this->geolocationService = new GeolocationService();
        $this->calculator = new TraditionalUnitsCalculator();
        
        // Check if this is for a Nepali user
        $this->isForNepaliUser = $this->geolocationService->getUserCountry()['is_nepali_user'];
        
        // Set widget properties
        $this->title = $this->getDefaultTitle();
        $this->description = $this->getDefaultDescription();
        $this->widgetType = 'traditional-units';
        
        // Set default configuration
        $this->config = array_merge([
            'show_nepali_names' => true,
            'auto_detect_location' => true,
            'default_from_unit' => 'daam',
            'default_to_unit' => 'sq_feet',
            'compact_mode' => false,
            'show_calculation_steps' => true
        ], $this->config);
        
        // Add CSS classes
        $this->addCssClass('widget-traditional-units');
        $this->addCssClass('traditional-units-widget');
    }
    
    /**
     * Get widget default title
     * 
     * @return string
     */
    private function getDefaultTitle(): string
    {
        return $this->isForNepaliUser ? 
            'परम्परागत एकाइ क्यालकुलेटर' : 
            'Traditional Units Calculator';
    }
    
    /**
     * Get widget default description
     * 
     * @return string
     */
    private function getDefaultDescription(): string
    {
        return $this->isForNepaliUser ?
            'नेपाली परम्परागत मापन एकाइहरूको रूपान्तरण' :
            'Convert between traditional Nepali measurement units';
    }
    
    /**
     * Render widget content
     * 
     * @return string
     */
    public function render(): string
    {
        $this->beforeRender();
        
        $html = '';
        
        if (!$this->isForNepaliUser) {
            // Show teaser for non-Nepali users
            $html = $this->renderTeaser();
        } else {
            // Show full calculator for Nepali users
            $html = $this->renderCalculator();
        }
        
        $this->afterRender();
        
        return $html;
    }
    
    /**
     * Render teaser for non-Nepali users
     * 
     * @return string
     */
    private function renderTeaser(): string
    {
        $countryData = $this->geolocationService->getUserCountry();
        
        $attributes = $this->attributesToString($this->getAttributes());
        
        return '
        <div ' . $attributes . '>
            <div class="widget-traditional-units teaser">
                <div class="widget-header">
                    <h3>🇳🇵 Traditional Nepali Units</h3>
                </div>
                <div class="widget-content">
                    <p>Convert between traditional Nepali measurement units (Ropani, Bigha, Kattha, etc.)</p>
                    <div class="geolocation-info">
                        <small>Detected: ' . htmlspecialchars($countryData['country_name']) . '</small>
                    </div>
                    <button class="btn btn-primary" onclick="showTraditionalUnits()">
                        Try Calculator
                    </button>
                </div>
            </div>
        </div>';
    }
    
    /**
     * Render calculator for Nepali users
     * 
     * @return string
     */
    private function renderCalculator(): string
    {
        $attributes = $this->attributesToString($this->getAttributes());
        $units = $this->calculator->getAvailableUnits();
        $defaultFromUnit = $this->getConfig('default_from_unit', 'daam');
        $defaultToUnit = $this->getConfig('default_to_unit', 'sq_feet');
        
        $html = '
        <div ' . $attributes . '>
            <div class="widget-traditional-units calculator">
                <div class="widget-header">
                    <h3>🇳🇵 ' . $this->title . '</h3>
                    <p class="widget-description">' . $this->description . '</p>
                </div>
                <div class="widget-content">
                    <form class="traditional-units-form" onsubmit="return convertTraditionalUnits(event)">
                        <div class="form-group">
                            <label for="' . $this->widgetId . '-value">Value:</label>
                            <input type="number" id="' . $this->widgetId . '-value" name="value" step="any" placeholder="Enter value" required>
                        </div>
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label for="' . $this->widgetId . '-from-unit">From:</label>
                                <select id="' . $this->widgetId . '-from-unit" name="from_unit" required>';
        
        foreach ($units as $unitKey => $unitData) {
            $selected = ($unitKey === $defaultFromUnit) ? 'selected' : '';
            $unitName = $this->getConfig('show_nepali_names', true) ? 
                $unitData['name_nepali'] . ' (' . $unitData['name'] . ')' : 
                $unitData['name'];
            $html .= '<option value="' . $unitKey . '" ' . $selected . '>' . $unitName . '</option>';
        }
        
        $html .= '</select>
                            </div>
                            
                            <div class="form-group">
                                <label for="' . $this->widgetId . '-to-unit">To:</label>
                                <select id="' . $this->widgetId . '-to-unit" name="to_unit" required>';
        
        // Add traditional units
        foreach ($units as $unitKey => $unitData) {
            $unitName = $this->getConfig('show_nepali_names', true) ? 
                $unitData['name_nepali'] . ' (' . $unitData['name'] . ')' : 
                $unitData['name'];
            $html .= '<option value="' . $unitKey . '">' . $unitName . '</option>';
        }
        
        // Add metric units
        $metricUnits = [
            'sq_feet' => 'Square Feet',
            'sq_meter' => 'Square Meters',
            'sq_yard' => 'Square Yards'
        ];
        
        foreach ($metricUnits as $unitKey => $unitName) {
            $selected = ($unitKey === $defaultToUnit) ? 'selected' : '';
            $html .= '<option value="' . $unitKey . '" ' . $selected . '>' . $unitName . '</option>';
        }
        
        $html .= '</select>
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary">Convert</button>
                    </form>
                    
                    <div id="' . $this->widgetId . '-result" class="conversion-result" style="display: none;">
                        <h4>Conversion Result:</h4>
                        <div class="result-display"></div>
                        <div class="all-conversions" style="display: none;">
                            <h5>All Conversions:</h5>
                            <div class="conversions-list"></div>
                        </div>
                    </div>
                </div>
            </div>
        </div>';
        
        return $html;
    }
    
    /**
     * Get widget CSS classes
     * 
     * @return string
     */
    public function getCssClasses(): string
    {
        if ($this->isForNepaliUser) {
            $this->addCssClass('nepali-user');
        } else {
            $this->addCssClass('non-nepali-user');
        }
        
        if ($this->getConfig('compact_mode', false)) {
            $this->addCssClass('compact-mode');
        }
        
        return parent::getCssClasses();
    }
    
    /**
     * Get widget assets
     */
    public function getAssets(): array
    {
        return [
            'css' => ['/assets/css/widgets.css'],
            'js' => ['/assets/js/widgets/traditional-units.js']
        ];
    }
    
    /**
     * Get widget dependencies
     */
    public function getDependencies(): array
    {
        return ['geolocation-service', 'traditional-units-calculator'];
    }
    
    /**
     * Validate widget configuration
     */
    public function validateConfig(): bool
    {
        $required = ['default_from_unit', 'default_to_unit'];
        foreach ($required as $key) {
            if (!isset($this->config[$key]) || empty($this->config[$key])) {
                return false;
            }
        }
        return true;
    }
    
    /**
     * Get JavaScript data for widget
     */
    public function getJavaScriptData(): array
    {
        $data = parent::getJavaScriptData();
        $data['isForNepaliUser'] = $this->isForNepaliUser;
        $data['showNepaliNames'] = $this->getConfig('show_nepali_names', true);
        $data['units'] = $this->calculator->getAvailableUnits();
        $data['metricUnits'] = $this->calculator->getMetricUnits();
        
        return $data;
    }
}
