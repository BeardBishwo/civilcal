/**
 * Premium Calculator Engine for Architecture
 * Real-time calculation system with advanced features
 * $1,500 Value Component
 */

class ArchitectureCalculatorEngine {
  constructor() {
    this.calculators = new Map();
    this.results = new Map();
    this.history = [];
    this.units = {
      length: { m: 1, ft: 3.28084, in: 39.3701, cm: 100 },
      area: { m2: 1, ft2: 10.764, in2: 1550.0031, cm2: 10000 },
      volume: { m3: 1, ft3: 35.3147, in3: 61023.744, cm3: 1000000 },
      weight: { kg: 1, lb: 2.20462, ton: 0.00110231 },
      pressure: { Pa: 1, psi: 0.000145038, kPa: 0.001, MPa: 0.000001 }
    };
    this.init();
  }

  init() {
    this.setupCivilEngineeringCalculators();
    this.setupStructuralCalculators();
    this.setupMEPCalculators();
    this.setupRealTimeValidation();
    console.log('🏗️ Architecture Calculator Engine Initialized');
  }

  // Civil Engineering Calculators
  setupCivilEngineeringCalculators() {
    // Concrete Volume Calculator
    this.calculators.set('concrete-volume', {
      name: 'Concrete Volume Calculator',
      category: 'civil',
      inputs: [
        { name: 'length', label: 'Length', type: 'number', unit: 'length', required: true },
        { name: 'width', label: 'Width', type: 'number', unit: 'length', required: true },
        { name: 'depth', label: 'Depth', type: 'number', unit: 'length', required: true },
        { name: 'waste_factor', label: 'Waste Factor (%)', type: 'number', min: 0, max: 20, default: 5 }
      ],
      calculate: (inputs) => {
        const baseVolume = inputs.length * inputs.width * inputs.depth;
        const wasteVolume = baseVolume * (inputs.waste_factor / 100);
        const totalVolume = baseVolume + wasteVolume;
        
        return {
          base_volume_m3: baseVolume,
          waste_volume_m3: wasteVolume,
          total_volume_m3: totalVolume,
          total_volume_ft3: totalVolume * this.units.volume.ft3,
          cement_bags: Math.ceil(totalVolume * 8), // ~8 bags per m3
          cost_estimate: totalVolume * 120 // $120 per m3 average
        };
      }
    });

    // Steel Reinforcement Calculator
    this.calculators.set('steel-reinforcement', {
      name: 'Steel Reinforcement Calculator',
      category: 'civil',
      inputs: [
        { name: 'area', label: 'Slab Area', type: 'number', unit: 'area', required: true },
        { name: 'thickness', label: 'Slab Thickness', type: 'number', unit: 'length', required: true },
        { name: 'main_bar_diameter', label: 'Main Bar Diameter', type: 'number', unit: 'length', required: true },
        { name: 'distribution_bar_diameter', label: 'Distribution Bar Diameter', type: 'number', unit: 'length', required: true },
        { name: 'main_bar_spacing', label: 'Main Bar Spacing', type: 'number', unit: 'length', required: true },
        { name: 'distribution_bar_spacing', label: 'Distribution Bar Spacing', type: 'number', unit: 'length', required: true }
      ],
      calculate: (inputs) => {
        const mainBarsPerMeter = Math.ceil(100 / inputs.main_bar_spacing);
        const distributionBarsPerMeter = Math.ceil(100 / inputs.distribution_bar_spacing);
        
        const mainSteelLength = inputs.area * mainBarsPerMeter;
        const distributionSteelLength = inputs.area * distributionBarsPerMeter;
        
        const mainBarArea = Math.PI * Math.pow(inputs.main_bar_diameter / 2, 2);
        const distributionBarArea = Math.PI * Math.pow(inputs.distribution_bar_diameter / 2, 2);
        
        const mainSteelWeight = mainSteelLength * mainBarArea * 7850 / 1000000; // kg
        const distributionSteelWeight = distributionSteelLength * distributionBarArea * 7850 / 1000000; // kg
        
        return {
          main_bars_per_m: mainBarsPerMeter,
          distribution_bars_per_m: distributionBarsPerMeter,
          main_steel_length_m: mainSteelLength,
          distribution_steel_length_m: distributionSteelLength,
          main_steel_weight_kg: mainSteelWeight,
          distribution_steel_weight_kg: distributionSteelWeight,
          total_steel_weight_kg: mainSteelWeight + distributionSteelWeight,
          total_cost: (mainSteelWeight + distributionSteelWeight) * 1.2 // $1.2 per kg
        };
      }
    });
  }

  // Structural Engineering Calculators
  setupStructuralCalculators() {
    // Beam Analysis Calculator
    this.calculators.set('beam-analysis', {
      name: 'Structural Beam Analysis',
      category: 'structural',
      inputs: [
        { name: 'span', label: 'Beam Span', type: 'number', unit: 'length', required: true },
        { name: 'load', label: 'Applied Load', type: 'number', unit: 'force', required: true },
        { name: 'e_modulus', label: "Young's Modulus", type: 'number', default: 200000 },
        { name: 'moment_inertia', label: 'Moment of Inertia', type: 'number', unit: 'area' }
      ],
      calculate: (inputs) => {
        const maxMoment = inputs.load * inputs.span / 8; // Simply supported beam
        const maxDeflection = (5 * inputs.load * Math.pow(inputs.span, 4)) / (384 * inputs.e_modulus * inputs.moment_inertia);
        const maxStress = (maxMoment * 0.1) / inputs.moment_inertia; // Assuming section modulus
        
        return {
          max_moment: maxMoment,
          max_deflection: maxDeflection,
          max_stress: maxStress,
          safety_factor: 250 / maxStress, // Assuming fy = 250 MPa
          deflection_ratio: inputs.span / maxDeflection,
          load_capacity: (maxMoment * 8) / inputs.span
        };
      }
    });
  }

  // MEP Calculators
  setupMEPCalculators() {
    // HVAC Load Calculator
    this.calculators.set('hvac-load', {
      name: 'HVAC Load Calculation',
      category: 'mep',
      inputs: [
        { name: 'area', label: 'Room Area', type: 'number', unit: 'area', required: true },
        { name: 'ceiling_height', label: 'Ceiling Height', type: 'number', unit: 'length', required: true },
        { name: 'occupancy', label: 'Occupancy', type: 'number', required: true },
        { name: 'window_area', label: 'Window Area', type: 'number', unit: 'area', required: true },
        { name: 'equipment_load', label: 'Equipment Load (W)', type: 'number', default: 0 }
      ],
      calculate: (inputs) => {
        const volume = inputs.area * inputs.ceiling_height;
        const sensible_load = inputs.area * 150 + inputs.occupancy * 250 + inputs.equipment_load;
        const latent_load = inputs.occupancy * 200;
        const total_load = sensible_load + latent_load;
        
        // Window heat gain (assuming 200 W/m2 for south-facing windows)
        const window_heat_gain = inputs.window_area * 200;
        const total_sensible = sensible_load + window_heat_gain;
        
        return {
          room_volume: volume,
          sensible_load_w: total_sensible,
          latent_load_w: latent_load,
          total_load_w: total_sensible + latent_load,
          cooling_ton: (total_sensible + latent_load) / 3517, // Convert to tons
          airflow_cfm: total_sensible / 1.08, // CFM calculation
          supply_duct_size: Math.sqrt((total_sensible / 1.08) / 500) * 12 // inches
        };
      }
    });

    // Electrical Load Calculator
    this.calculators.set('electrical-load', {
      name: 'Electrical Load Analysis',
      category: 'mep',
      inputs: [
        { name: 'power_kw', label: 'Total Power Load', type: 'number', required: true },
        { name: 'voltage', label: 'Voltage', type: 'number', default: 240 },
        { name: 'power_factor', label: 'Power Factor', type: 'number', min: 0.7, max: 1, default: 0.9 },
        { name: 'motor_count', label: 'Number of Motors', type: 'number', default: 0 }
      ],
      calculate: (inputs) => {
        const apparent_power = inputs.power_kw / inputs.power_factor;
        const current = (inputs.power_kw * 1000) / (inputs.voltage * inputs.power_factor);
        const motor_diversity = 1 + (inputs.motor_count * 0.25); // 25% diversity for motors
        
        return {
          apparent_power_kva: apparent_power,
          current_amps: current,
          full_load_current: current * motor_diversity,
          feeder_size_awg: this.calculateWireSize(current * motor_diversity),
          breaker_size_amps: Math.ceil(current * motor_diversity * 1.25),
          demand_factor: motor_diversity
        };
      }
    });
  }

  // Real-time validation setup
  setupRealTimeValidation() {
    this.validationRules = {
      required: (value) => value !== '' && value !== null && value !== undefined,
      positive: (value) => value > 0,
      range: (value, min, max) => value >= min && value <= max,
      email: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value),
      number: (value) => !isNaN(value) && !isNaN(parseFloat(value))
    };
  }

  // Calculate wire size based on current
  calculateWireSize(current) {
    const wireSizes = [
      { size: 14, ampacity: 15 },
      { size: 12, ampacity: 20 },
      { size: 10, ampacity: 30 },
      { size: 8, ampacity: 40 },
      { size: 6, ampacity: 55 },
      { size: 4, ampacity: 70 },
      { size: 2, ampacity: 95 },
      { size: 1, ampacity: 110 },
      { size: 1/0, ampacity: 125 },
      { size: 2/0, ampacity: 145 },
      { size: 3/0, ampacity: 165 },
      { size: 4/0, ampacity: 195 }
    ];

    for (let i = 0; i < wireSizes.length; i++) {
      if (current <= wireSizes[i].ampacity) {
        return wireSizes[i].size;
      }
    }
    return 250; // Large service
  }

  // Perform calculation
  calculate(calculatorId, inputs) {
    const calculator = this.calculators.get(calculatorId);
    if (!calculator) {
      throw new Error(`Calculator ${calculatorId} not found`);
    }

    // Validate inputs
    const validation = this.validateInputs(calculator.inputs, inputs);
    if (!validation.valid) {
      throw new Error(`Validation failed: ${validation.errors.join(', ')}`);
    }

    // Convert units if needed
    const convertedInputs = this.convertUnits(calculator.inputs, inputs);

    // Perform calculation
    const results = calculator.calculate(convertedInputs);

    // Store results
    this.results.set(calculatorId, {
      inputs: convertedInputs,
      results: results,
      timestamp: new Date(),
      calculator: calculator.name
    });

    // Add to history
    this.history.push({
      id: calculatorId,
      inputs: convertedInputs,
      results: results,
      timestamp: new Date()
    });

    return results;
  }

  // Validate inputs
  validateInputs(inputDefs, inputs) {
    const errors = [];
    
    inputDefs.forEach(def => {
      const value = inputs[def.name];
      
      if (def.required && !this.validationRules.required(value)) {
        errors.push(`${def.label} is required`);
      }
      
      if (value !== undefined && value !== null && value !== '') {
        if (def.type === 'number' && !this.validationRules.number(value)) {
          errors.push(`${def.label} must be a valid number`);
        }
        
        if (def.min !== undefined && !this.validationRules.range(value, def.min, Infinity)) {
          errors.push(`${def.label} must be greater than or equal to ${def.min}`);
        }
        
        if (def.max !== undefined && !this.validationRules.range(value, -Infinity, def.max)) {
          errors.push(`${def.label} must be less than or equal to ${def.max}`);
        }
      }
    });

    return {
      valid: errors.length === 0,
      errors: errors
    };
  }

  // Convert units
  convertUnits(inputDefs, inputs) {
    const converted = { ...inputs };
    
    inputDefs.forEach(def => {
      if (def.unit && this.units[def.unit] && inputs[def.name]) {
        const fromUnit = inputs[`${def.name}_unit`] || 'm';
        const toUnit = 'm'; // Convert to base unit
        
        if (this.units[def.unit][fromUnit] && this.units[def.unit][toUnit]) {
          converted[def.name] = inputs[def.name] * 
            (this.units[def.unit][fromUnit] / this.units[def.unit][toUnit]);
        }
      }
    });
    
    return converted;
  }

  // Get calculation history
  getHistory(limit = 10) {
    return this.history.slice(-limit).reverse();
  }

  // Export results
  exportResults(calculatorId, format = 'json') {
    const result = this.results.get(calculatorId);
    if (!result) {
      throw new Error(`No results found for calculator ${calculatorId}`);
    }

    switch (format) {
      case 'json':
        return JSON.stringify(result, null, 2);
      case 'csv':
        return this.convertToCSV(result);
      case 'pdf':
        return this.generatePDFReport(result);
      default:
        throw new Error(`Unsupported export format: ${format}`);
    }
  }

  // Convert to CSV
  convertToCSV(result) {
    const lines = ['Field,Value'];
    
    Object.entries(result.inputs).forEach(([key, value]) => {
      lines.push(`Input ${key},${value}`);
    });
    
    Object.entries(result.results).forEach(([key, value]) => {
      lines.push(`Result ${key},${value}`);
    });
    
    return lines.join('\n');
  }

  // Generate PDF report (simplified)
  generatePDFReport(result) {
    return {
      title: `Calculation Report - ${result.calculator}`,
      date: result.timestamp.toLocaleDateString(),
      inputs: result.inputs,
      results: result.results,
      format: 'PDF'
    };
  }

  // Get available calculators
  getCalculators() {
    const categories = {};
    this.calculators.forEach((calc, id) => {
      if (!categories[calc.category]) {
        categories[calc.category] = [];
      }
      categories[calc.category].push({
        id: id,
        name: calc.name,
        inputs: calc.inputs
      });
    });
    return categories;
  }
}

// Initialize the calculator engine (browser environment only)
if (typeof window !== 'undefined' && typeof document !== 'undefined') {
  window.ArchitectureCalculatorEngine = ArchitectureCalculatorEngine;
  
  // Auto-initialize if DOM is ready
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', () => {
      window.calcEngine = new ArchitectureCalculatorEngine();
    });
  } else {
    window.calcEngine = new ArchitectureCalculatorEngine();
  }
}

// Export for Node.js environment
if (typeof module !== 'undefined' && module.exports) {
  module.exports = ArchitectureCalculatorEngine;
}
