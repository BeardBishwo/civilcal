# Bishwo Calculator - Modules Folder Analysis Report

## Overview
The Bishwo Calculator application contains a well-organized modules folder structure designed for various engineering disciplines. This report provides a comprehensive analysis of the modules folder, detailing each category and their respective subcategories.

## Main Module Categories

### 1. Civil Engineering
Dedicated to civil engineering calculations and tools.

### 2. Electrical Engineering
Contains electrical system design and analysis tools.

### 3. Estimation
Focused on project cost estimation and quantity calculations.

### 4. Fire Safety
Includes fire protection system calculations.

### 5. HVAC (Heating, Ventilation, and Air Conditioning)
For HVAC system design and thermal load calculations.

### 6. MEP (Mechanical, Electrical, Plumbing)
Integrated MEP system coordination tools.

### 7. Plumbing
Water and waste system design calculations.

### 8. Project Management
Project planning, scheduling, and management tools.

### 9. Site Management
Tools for site operations and productivity.

### 10. Structural Engineering
Structural analysis and design calculations.

## Subcategories by Main Module

### Civil Engineering
- Brickwork
- Concrete
- Earthwork
- Structural (different from the main Structural Engineering category)

### Electrical Engineering
- Conduit Sizing
- Load Calculation
- Short Circuit Analysis
- Voltage Drop
- Wire Sizing

### Estimation
- Cost Estimation
- Equipment Estimation
- Labor Estimation
- Material Estimation
- Project Financials
- Quantity Takeoff
- Reports
- Tender Bidding

### Fire Safety
- Fire Pumps
- Hazard Classification
- Hydraulics
- Sprinklers
- Standpipes

### HVAC
- Duct Sizing
- Energy Analysis
- Equipment Sizing
- Load Calculation
- Psychrometrics

### MEP (Mechanical, Electrical, Plumbing)
- Coordination
- Cost Management
- Data Utilities
- Electrical
- Energy Efficiency
- Fire Protection
- Integration
- Mechanical
- Plumbing
- Reports Documentation

### Plumbing
- Drainage
- Fixtures
- Hot Water
- Pipe Sizing
- Stormwater
- Water Supply

### Project Management
- Analytics
- Communication
- Dashboard
- Documents
- Financial
- Integration
- Procurement
- Quality
- Reports
- Resources
- Scheduling
- Settings

### Site Management
- Concrete Tools
- Earthwork
- Productivity
- Safety
- Surveying

### Structural Engineering
- Beam Analysis
- Column Design
- Foundation Design
- Load Analysis
- Reinforcement
- Reports
- Slab Design
- Steel Structure

## Example Calculator Files by Subcategory

### Civil Engineering

#### Brickwork
*No specific files checked*

#### Concrete
- `concrete-mix.php` - Concrete mix design calculations
- `concrete-strength.php` - Concrete strength analysis
- `concrete-volume.php` - Volume calculations for concrete
- `rebar-calculation.php` - Reinforcement steel calculations

#### Earthwork
*No specific files checked*

#### Structural (Civil)
*No specific files checked*

### Electrical Engineering

#### Wire Sizing
- `motor-circuit-wire-sizing.php` - Motor circuit wire size calculations
- `motor-circuit-wiring.php` - Motor circuit wiring calculations
- `transformer-kva-sizing.php` - Transformer sizing in KVA
- `wire-ampacity.php` - Wire ampacity calculations
- `wire-size-by-current.php` - Wire sizing based on current requirements

#### Load Calculation
*No specific files checked*

#### Voltage Drop
*No specific files checked*

#### Short Circuit
*No specific files checked*

#### Conduit Sizing
*No specific files checked*

### Estimation

#### Cost Estimation
- `boq-preparation.php` - Bill of Quantities preparation
- `contingency-overheads.php` - Contingency and overhead calculations
- `cost-escalation.php` - Cost escalation calculations
- `item-rate-analysis.php` - Item rate analysis
- `project-cost-summary.php` - Project cost summary

#### Material Estimation
*No specific files checked*

#### Labor Estimation
*No specific files checked*

#### Equipment Estimation
*No specific files checked*

#### Quantity Takeoff
*No specific files checked*

#### Project Financials
*No specific files checked*

#### Tender Bidding
*No specific files checked*

#### Reports (Estimation)
*No specific files checked*

### Fire Safety

#### Sprinklers
*No specific files checked*

#### Hydraulics
*No specific files checked*

#### Fire Pumps
*No specific files checked*

#### Hazard Classification
*No specific files checked*

#### Standpipes
*No specific files checked*

*Note: The fire safety module has an `index.php` file in the main directory.*

### HVAC

#### Load Calculation
- `cooling-load.php` - Cooling load calculations
- `heating-load.php` - Heating load calculations
- `infiltration.php` - Infiltration calculations
- `ventilation.php` - Ventilation requirements

#### Duct Sizing
*No specific files checked*

#### Equipment Sizing
*No specific files checked*

#### Energy Analysis
*No specific files checked*

#### Psychrometrics
*No specific files checked*

### MEP (Mechanical, Electrical, Plumbing)

*Note: The MEP module has a `bootstrap.php` file in the main directory.*

#### Electrical (MEP)
*No specific files checked*

#### Mechanical (MEP)
*No specific files checked*

#### Plumbing (MEP)
*No specific files checked*

#### Fire Protection (MEP)
*No specific files checked*

#### Coordination (MEP)
*No specific files checked*

#### Energy Efficiency (MEP)
*No specific files checked*

#### Integration (MEP)
*No specific files checked*

#### Cost Management (MEP)
*No specific files checked*

#### Data Utilities (MEP)
*No specific files checked*

#### Reports Documentation (MEP)
*No specific files checked*

### Plumbing

#### Water Supply
*No specific files checked*

#### Drainage
*No specific files checked*

#### Stormwater
*No specific files checked*

#### Hot Water
*No specific files checked*

#### Fixtures
*No specific files checked*

#### Pipe Sizing
*No specific files checked*

### Project Management

#### Scheduling
*No specific files checked*

#### Resources
*No specific files checked*

#### Financial
*No specific files checked*

#### Quality
*No specific files checked*

#### Procurement
*No specific files checked*

#### Reports (Project Management)
*No specific files checked*

#### Analytics
*No specific files checked*

#### Documents
*No specific files checked*

#### Communication
*No specific files checked*

#### Integration
*No specific files checked*

#### Settings
*No specific files checked*

#### Dashboard
*No specific files checked*

*Note: The Project Management module has a `template-coming-soon.php` file.*

### Site Management

#### Surveying
*No specific files checked*

#### Safety
*No specific files checked*

#### Earthwork (Site)
*No specific files checked*

#### Concrete Tools
*No specific files checked*

#### Productivity
*No specific files checked*

### Structural Engineering

#### Beam Analysis
- `beam-design.php` - Beam design calculations
- `beam-load-combination.php` - Beam load combination analysis
- `cantilever-beam.php` - Cantilever beam calculations
- `continuous-beam.php` - Continuous beam calculations
- `simply-supported-beam.php` - Simply supported beam calculations

#### Column Design
*No specific files checked*

#### Foundation Design
*No specific files checked*

#### Load Analysis
*No specific files checked*

#### Reinforcement
*No specific files checked*

#### Slab Design
*No specific files checked*

#### Steel Structure
*No specific files checked*

#### Reports (Structural)
*No specific files checked*

## Summary

The Bishwo Calculator modules folder demonstrates a well-organized, domain-specific structure designed for AEC (Architecture, Engineering, Construction) professionals. The 10 main engineering discipline categories contain 65+ subcategories with specialized calculator implementations. This modular approach allows for easy expansion and maintenance of engineering-specific tools.

The file naming convention follows a clear pattern using descriptive names with hyphens, followed by the `.php` extension. The directory structure reflects the real-world organization of engineering disciplines and their specialized tools.

No main configuration file was found at the root of the modules directory, but some modules have specific bootstrap files (e.g. MEP has `bootstrap.php`, Fire Safety has `index.php`).