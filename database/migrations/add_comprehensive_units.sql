-- =====================================================
-- 1. ACCELERATION (Category ID: 1)
-- =====================================================
DELETE FROM calc_units WHERE category_id = 1;
INSERT INTO calc_units (category_id, name, symbol, to_base_multiplier, base_unit, display_order) VALUES
(1, 'Meter per second squared', 'm/s²', 1.000000000000000, TRUE, 1),
(1, 'Foot per second squared', 'ft/s²', 0.304800000000000, FALSE, 2),
(1, 'G-force', 'g', 9.806650000000000, FALSE, 3),
(1, 'Galileo', 'Gal', 0.010000000000000, FALSE, 4),
(1, 'Inch per second squared', 'in/s²', 0.025400000000000, FALSE, 5);

-- =====================================================
-- 2. ANGLES (Category ID: 2)
-- =====================================================
DELETE FROM calc_units WHERE category_id = 2;
INSERT INTO calc_units (category_id, name, symbol, to_base_multiplier, base_unit, display_order) VALUES
(2, 'Degree', '°', 1.000000000000000, TRUE, 1),
(2, 'Radian', 'rad', 57.295779513082300, FALSE, 2),
(2, 'Gradian', 'grad', 0.900000000000000, FALSE, 3),
(2, 'Arcminute', "'", 0.016666666666667, FALSE, 4),
(2, 'Arcsecond', '"', 0.000277777777778, FALSE, 5),
(2, 'Full circle', 'circle', 360.000000000000000, FALSE, 6);

-- =====================================================
-- 3. AREA (Category ID: 3)
-- =====================================================
DELETE FROM calc_units WHERE category_id = 3;
INSERT INTO calc_units (category_id, name, symbol, to_base_multiplier, base_unit, display_order) VALUES
(3, 'Square meter', 'm²', 1.000000000000000, TRUE, 1),
(3, 'Square kilometer', 'km²', 1000000.000000000000000, FALSE, 2),
(3, 'Square centimeter', 'cm²', 0.000100000000000, FALSE, 3),
(3, 'Square millimeter', 'mm²', 0.000001000000000, FALSE, 4),
(3, 'Square mile', 'mi²', 2589988.110336000000000, FALSE, 5),
(3, 'Square yard', 'yd²', 0.836127360000000, FALSE, 6),
(3, 'Square foot', 'ft²', 0.092903040000000, FALSE, 7),
(3, 'Square inch', 'in²', 0.000645160000000, FALSE, 8),
(3, 'Hectare', 'ha', 10000.000000000000000, FALSE, 9),
(3, 'Acre', 'ac', 4046.856422400000000, FALSE, 10);

-- =====================================================
-- 4. CIRCULAR MEASURE (Category ID: 4)
-- =====================================================
DELETE FROM calc_units WHERE category_id = 4;
INSERT INTO calc_units (category_id, name, symbol, to_base_multiplier, base_unit, display_order) VALUES
(4, 'Radius', 'r', 1.000000000000000, TRUE, 1),
(4, 'Diameter', 'd', 0.500000000000000, FALSE, 2),
(4, 'Circumference', 'C', 0.159154943091895, FALSE, 3),
(4, 'Area', 'A', 0.564189583547756, FALSE, 4);

-- =====================================================
-- DENSITY - Category ID: 5
-- Adding all units from Convert123
-- =====================================================
DELETE FROM calc_units WHERE category_id = 5;

INSERT INTO calc_units (category_id, name, symbol, to_base_multiplier, base_unit, display_order) VALUES
-- Base unit (using gram/mm³ as shown in Convert123)
(5, 'Gram per cubic millimeter', 'gram/mm³', 1000000.000000000000000, TRUE, 1),
-- Gram variations
(5, 'Gram per cubic centimeter', 'g/cm³', 1000.000000000000000, FALSE, 2),
(5, 'Gram per cubic meter', 'g/m³', 0.001000000000000, FALSE, 3),
-- Kilogram variations
(5, 'Kilogram per cubic meter', 'kg/m³', 1.000000000000000, FALSE, 4),
(5, 'Kilogram per cubic centimeter', 'kg/cm³', 1000000.000000000000000, FALSE, 5),
-- Pound variations
(5, 'Pound per cubic foot', 'lb/ft³', 16.018463373960000, FALSE, 6),
(5, 'Pound per cubic inch', 'lb/in³', 27679.904710191000, FALSE, 7),
(5, 'Pound per cubic yard', 'lb/yd³', 0.593276421257783, FALSE, 8),
-- CWT variations
(5, 'CWT (short) per cubic foot', 'cwt (short)/ft³', 1601.846337396000000, FALSE, 9),
(5, 'CWT (short) per cubic yard', 'cwt (short)/yd³', 59.327642125778300, FALSE, 10),
(5, 'CWT (long) per cubic foot', 'cwt (long)/ft³', 1793.908217723840000, FALSE, 11),
(5, 'CWT (long) per cubic yard', 'cwt (long)/yd³', 66.441415471253300, FALSE, 12),
-- Ton variations
(5, 'Ton (long) per cubic meter', 'ton (long)/m³', 1.016046908800000, FALSE, 13),
(5, 'Ton (long) per cubic yard', 'ton (long)/yd³', 0.777192181140592, FALSE, 14),
(5, 'Ton (short) per cubic yard', 'ton (short)/yd³', 0.692929473515625, FALSE, 15),
(5, 'Tonne per cubic meter', 'tonne/m³', 1.000000000000000, FALSE, 16),
(5, 'Tonne per cubic yard', 'tonne/yd³', 0.764554857984000, FALSE, 17);

-- =====================================================
-- ENERGY - Category ID: 6
-- Adding all units from Convert123
-- =====================================================
DELETE FROM calc_units WHERE category_id = 6;

INSERT INTO calc_units (category_id, name, symbol, to_base_multiplier, base_unit, display_order) VALUES
-- Base unit
(6, 'Newton meter', 'N⋅m', 1.000000000000000, TRUE, 1),
-- Kilogram-force variations
(6, 'Kilogram-force meter', 'kg-m', 9.806650000000000, FALSE, 2),
-- Pound-force variations
(6, 'Pound-force foot', 'ft-lb', 1.355817948331400, FALSE, 3),
-- British thermal unit
(6, 'British thermal unit', 'Btu', 1055.055852620000000, FALSE, 4),
-- Joule variations
(6, 'Joule', 'J', 1.000000000000000, FALSE, 5),
(6, 'Kilojoule', 'kJ (kilojoule)', 1000.000000000000000, FALSE, 6),
-- Calorie variations
(6, 'Kilocalorie', 'kcal (kilocalorie)', 4184.000000000000000, FALSE, 7);

-- =====================================================
-- FLOW RATE (VOLUME) - Category ID: 7
-- Adding all units from Convert123
-- =====================================================
DELETE FROM calc_units WHERE category_id = 7;

INSERT INTO calc_units (category_id, name, symbol, to_base_multiplier, base_unit, display_order) VALUES
-- Base unit
(7, 'Liter per second', 'l/sec', 1.000000000000000, TRUE, 1),
(7, 'Liter per minute', 'l/min', 0.016666666666667, FALSE, 2),
(7, 'Liter per hour', 'l/hr', 0.000277777777778, FALSE, 3),
-- Cubic meter variations
(7, 'Cubic meter per second', 'mm³/sec', 0.000000001000000, FALSE, 4),
(7, 'Cubic meter per minute', 'mm³/min', 0.000000000016667, FALSE, 5),
(7, 'Cubic meter per hour', 'mm³/hour', 0.000000000000278, FALSE, 6),
-- Centimeter variations
(7, 'Cubic centimeter per second (milliliter/sec)', 'cm³/sec (millilitre/sec)', 0.001000000000000, FALSE, 7),
(7, 'Cubic centimeter per minute (milliliter/min)', 'cm³/min (millilitre/min)', 0.000016666666667, FALSE, 8),
(7, 'Cubic centimeter per hour (milliliter/hour)', 'cm³/hour (millilitre/hour)', 0.000000277777778, FALSE, 9),
-- Meter variations
(7, 'Cubic meter per second', 'm³/sec', 1000.000000000000000, FALSE, 10),
(7, 'Cubic meter per minute', 'm³/min', 16.666666666666667, FALSE, 11),
(7, 'Cubic meter per hour', 'm³/hour', 0.277777777777778, FALSE, 12),
-- Inch variations
(7, 'Cubic inch per second', 'in³/sec', 0.016387064000000, FALSE, 13),
(7, 'Cubic inch per minute', 'in³/min', 0.000273117733333, FALSE, 14),
(7, 'Cubic inch per hour', 'in³/hour', 0.000004551962222, FALSE, 15),
-- Foot variations
(7, 'Cubic foot per second', 'ft³/sec', 28.316846592000000, FALSE, 16),
(7, 'Cubic foot per minute', 'ft³/min', 0.471947443200000, FALSE, 17),
(7, 'Cubic foot per hour', 'ft³/hour', 0.007865790720000, FALSE, 18),
-- Yard variations
(7, 'Cubic yard per second', 'yd³/sec', 764.554857984000000, FALSE, 19),
(7, 'Cubic yard per minute', 'yd³/min', 12.742580966400000, FALSE, 20),
(7, 'Cubic yard per hour', 'yd³/hour', 0.212376349440000, FALSE, 21),
-- Gallon variations
(7, 'Gallon (US) per second', 'gal (US)/sec', 3.785411784000000, FALSE, 22),
(7, 'Gallon (US) per minute', 'gal (US)/min', 0.063090196400000, FALSE, 23),
(7, 'Gallon (US) per hour', 'gal (US)/hour', 0.001051503273333, FALSE, 24),
(7, 'Gallon (UK) per second', 'gal (UK)/sec', 4.546090000000000, FALSE, 25),
(7, 'Gallon (UK) per minute', 'gal (UK)/min', 0.075768166666667, FALSE, 26),
(7, 'Gallon (UK) per hour', 'gal (UK)/hour', 0.001262802777778, FALSE, 27),
-- Barrel variations
(7, 'Barrel (oil) per second', 'Barrels/sec (oil)', 158.987294928000000, FALSE, 28),
(7, 'Barrel (oil) per minute', 'Barrels/min (oil)', 2.649788248800000, FALSE, 29),
(7, 'Barrel (oil) per hour', 'Barrels/hour (oil)', 0.044163137480000, FALSE, 30),
(7, 'Barrel (oil) per day', 'Barrels/day (oil)', 0.001840130728333, FALSE, 31);

-- =====================================================
-- FORCE - Category ID: 9
-- Adding all units from Convert123
-- =====================================================
DELETE FROM calc_units WHERE category_id = 9;

INSERT INTO calc_units (category_id, name, symbol, to_base_multiplier, base_unit, display_order) VALUES
-- Base unit
(9, 'Newton', 'Newton', 1.000000000000000, TRUE, 1),
-- Kilogram-force
(9, 'Kilogram-force', 'kg-force', 9.806650000000000, FALSE, 2),
-- Pound-force
(9, 'Pound-force', 'lb-force', 4.448221615255000, FALSE, 3),
-- Dyne
(9, 'Dyne', 'dyne', 0.000010000000000, FALSE, 4),
-- Ton-force variations
(9, 'Ton-force (short) USA', 'ton-force USA', 8896.443230510000000, FALSE, 5),
(9, 'Ton-force (long) UK', 'ton-force UK', 9964.016418400000000, FALSE, 6),
(9, 'Tonne-force', 'tonne-force', 9806.650000000000000, FALSE, 7);

-- =====================================================
-- FLOW RATE (MASS) - Category ID: 8
-- Adding all missing units from Convert123
-- =====================================================
DELETE FROM calc_units WHERE category_id = 8;

INSERT INTO calc_units (category_id, name, symbol, to_base_multiplier, base_unit, display_order) VALUES
-- Base unit
(8, 'Gram per second', 'gram/sec', 1.000000000000000, TRUE, 1),
-- Gram variations
(8, 'Gram per minute', 'gram/min', 0.016666666666667, FALSE, 2),
(8, 'Gram per hour', 'gram/hour', 0.000277777777778, FALSE, 3),
-- Kilogram variations
(8, 'Kilogram per second', 'kilogram/sec', 1000.000000000000000, FALSE, 4),
(8, 'Kilogram per minute', 'kilogram/min', 16.666666666666667, FALSE, 5),
(8, 'Kilogram per hour', 'kilogram/hour', 0.277777777777778, FALSE, 6),
-- Ounce variations
(8, 'Ounce per second', 'ounce/sec', 28.349523125000000, FALSE, 7),
(8, 'Ounce per minute', 'ounce/min', 0.472492051875000, FALSE, 8),
(8, 'Ounce per hour', 'ounce/hour', 0.007874867531250, FALSE, 9),
-- Pound variations
(8, 'Pound per second', 'lb/sec', 453.592370000000000, FALSE, 10),
(8, 'Pound per minute', 'lb/min', 7.559872833333333, FALSE, 11),
(8, 'Pound per hour', 'lb/hour', 0.125997880555556, FALSE, 12),
-- CWT (hundredweight) variations - US
(8, 'CWT (short) USA per second', 'cwt USA/sec', 45359.237000000000000, FALSE, 13),
(8, 'CWT (short) UK per second', 'cwt UK/sec', 50802.345440000000000, FALSE, 14),
(8, 'CWT (short) USA per minute', 'cwt USA/min', 755.987283333333000, FALSE, 15),
(8, 'CWT (short) UK per minute', 'cwt UK/min', 846.705757333333000, FALSE, 16),
(8, 'CWT (short) USA per hour', 'cwt USA/hour', 12.599788055555600, FALSE, 17),
(8, 'CWT (short) UK per hour', 'cwt UK/hour', 14.111762622222200, FALSE, 18),
-- Ton variations - long (UK)
(8, 'Ton (long) UK per hour', 'ton (long) UK/hour', 282.235129600000000, FALSE, 19),
(8, 'Ton (long) UK per day', 'ton (long) UK/day', 11.759797066666700, FALSE, 20),
-- Ton variations - short (USA)
(8, 'Ton (short) USA per hour', 'ton (short) USA/hour', 251.997761111111000, FALSE, 21),
(8, 'Ton (short) USA per day', 'ton (short) USA/day', 10.499906712962900, FALSE, 22),
-- Ton variations - metric
(8, 'Tonne (metric) per hour', 'tonne (metric)/hour', 277.777777777778000, FALSE, 23),
(8, 'Tonne (metric) per day', 'tonne (metric)/day', 11.574074074074100, FALSE, 24);

-- =====================================================
-- FREQUENCY - Category ID: 10
-- Adding all missing units from Convert123
-- =====================================================
DELETE FROM calc_units WHERE category_id = 10;

INSERT INTO calc_units (category_id, name, symbol, to_base_multiplier, base_unit, display_order) VALUES
-- Base unit
(10, 'Hertz', 'Hz', 1.000000000000000, TRUE, 1),
-- Hertz variations
(10, 'Kilohertz', 'kHz', 1000.000000000000000, FALSE, 2),
(10, 'Megahertz', 'MHz', 1000000.000000000000000, FALSE, 3),
-- Cycles
(10, 'Cycles per second', 'cycles/sec', 1.000000000000000, FALSE, 4),
(10, 'Cycles per minute', 'cycles/min', 0.016666666666667, FALSE, 5),
(10, 'Cycles per hour', 'cycles/hour', 0.000277777777778, FALSE, 6),
-- Revolutions
(10, 'Revolutions per second', 'revolutions/sec', 1.000000000000000, FALSE, 7),
(10, 'Revolutions per minute (RPM)', 'revolutions/min (RPM)', 0.016666666666667, FALSE, 8),
(10, 'Revolutions per hour', 'revolutions/hour', 0.000277777777778, FALSE, 9),
-- Radians
(10, 'Radians per second', 'radians/sec', 0.159154943091895, FALSE, 10),
(10, 'Radians per minute', 'radians/min', 0.002652582384865, FALSE, 11),
(10, 'Radians per hour', 'radians/hour', 0.000044209706414, FALSE, 12),
-- Degrees
(10, 'Degrees per second', 'degrees/sec', 0.002777777777778, FALSE, 13),
(10, 'Degrees per minute', 'degrees/min', 0.000046296296296, FALSE, 14),
(10, 'Degrees per hour', 'degrees/hour', 0.000000771604938, FALSE, 15);

-- =====================================================
-- FUEL CONSUMPTION - Category ID: 11
-- Adding all missing units from Convert123
-- =====================================================
DELETE FROM calc_units WHERE category_id = 11;

INSERT INTO calc_units (category_id, name, symbol, to_base_multiplier, base_unit, display_order) VALUES
-- Base unit (km/litre is easier for conversion)
(11, 'Kilometer per liter', 'km/litre', 1.000000000000000, TRUE, 1),
-- Kilometer variations
(11, 'Kilometer per gallon USA', 'km/gallon USA', 0.264172052358148, FALSE, 2),
(11, 'Kilometer per gallon UK', 'km/gallon UK', 0.219969157332418, FALSE, 3),
-- Mile variations
(11, 'Mile per liter', 'mile/litre', 1.609344000000000, FALSE, 4),
(11, 'Mile per gallon USA', 'mile/gallon USA', 0.425143707430272, FALSE, 5),
(11, 'Mile per gallon UK', 'mile/gallon UK', 0.354006189934715, FALSE, 6),
-- Liter per km
(11, 'Liter per kilometer', 'litre/km', 1.000000000000000, FALSE, 7),
-- Gallon variations
(11, 'Gallon USA per kilometer', 'gallon USA/km', 3.785411784000000, FALSE, 8),
(11, 'Gallon UK per kilometer', 'gallon UK/km', 4.546090000000000, FALSE, 9),
(11, 'Liter per mile', 'litre/mile', 0.621371192237334, FALSE, 10),
(11, 'Gallon USA per mile', 'gallon USA/mile', 2.352145833333330, FALSE, 11),
(11, 'Gallon UK per mile', 'gallon UK/mile', 2.824809363319670, FALSE, 12);

-- =====================================================
-- LIGHTING - Category ID: 13
-- Adding all missing units from Convert123
-- =====================================================
DELETE FROM calc_units WHERE category_id = 13;

INSERT INTO calc_units (category_id, name, symbol, to_base_multiplier, base_unit, display_order) VALUES
-- Base unit
(13, 'Lumen per square meter', 'Lumen/cm²', 1.000000000000000, TRUE, 1),
(13, 'Lumen per square meter (Lux)', 'Lumen/m² (Lux)', 0.000100000000000, FALSE, 2),
(13, 'Lumen per square inch', 'Lumen/in²', 0.155000310000620, FALSE, 3),
(13, 'Lumen per square foot', 'Lumen/ft²', 0.001076391041671, FALSE, 4),
(13, 'Foot-candle', 'foot-candle', 0.001076391041671, FALSE, 5);

-- =====================================================
-- LIQUID MEASURE - Category ID: 14
-- Adding comprehensive liquid measure units
-- =====================================================
DELETE FROM calc_units WHERE category_id = 14;

INSERT INTO calc_units (category_id, name, symbol, to_base_multiplier, base_unit, display_order) VALUES
-- Base unit
(14, 'Liter', 'L', 1.000000000000000, TRUE, 1),
(14, 'Milliliter', 'mL', 0.001000000000000, FALSE, 2),
(14, 'Cubic centimeter', 'cm³', 0.001000000000000, FALSE, 3),
-- US Liquid measures
(14, 'Gallon (US)', 'gal (US)', 3.785411784000000, FALSE, 4),
(14, 'Quart (US)', 'qt (US)', 0.946352946000000, FALSE, 5),
(14, 'Pint (US)', 'pt (US)', 0.473176473000000, FALSE, 6),
(14, 'Cup (US)', 'cup', 0.236588236500000, FALSE, 7),
(14, 'Fluid ounce (US)', 'fl oz (US)', 0.029573529563000, FALSE, 8),
(14, 'Tablespoon (US)', 'tbsp', 0.014786764781500, FALSE, 9),
(14, 'Teaspoon (US)', 'tsp', 0.004928921593833, FALSE, 10),
-- UK Liquid measures
(14, 'Gallon (UK)', 'gal (UK)', 4.546090000000000, FALSE, 11),
(14, 'Quart (UK)', 'qt (UK)', 1.136522500000000, FALSE, 12),
(14, 'Pint (UK)', 'pt (UK)', 0.568261250000000, FALSE, 13),
(14, 'Fluid ounce (UK)', 'fl oz (UK)', 0.028413062500000, FALSE, 14),
-- Other
(14, 'Barrel (oil)', 'bbl', 158.987294928000000, FALSE, 15),
(14, 'Barrel (US)', 'bbl (US)', 119.240471196000000, FALSE, 16),
(14, 'Hectoliter', 'hL', 100.000000000000000, FALSE, 17),
(14, 'Kiloliter', 'kL', 1000.000000000000000, FALSE, 18);

-- =====================================================
-- TORQUE - Category ID: 20
-- Adding comprehensive torque units
-- =====================================================
DELETE FROM calc_units WHERE category_id = 20;

INSERT INTO calc_units (category_id, name, symbol, to_base_multiplier, base_unit, display_order) VALUES
-- Base unit
(20, 'Newton meter', 'N⋅m', 1.000000000000000, TRUE, 1),
(20, 'Newton centimeter', 'N⋅cm', 0.010000000000000, FALSE, 2),
(20, 'Dyne meter', 'dyn⋅m', 0.000010000000000, FALSE, 3),
(20, 'Dyne centimeter', 'dyn⋅cm', 0.000000100000000, FALSE, 4),
(20, 'Kilogram-force meter', 'kg-m', 9.806650000000000, FALSE, 5),
(20, 'Kilogram-force centimeter', 'kgf⋅cm', 0.098066500000000, FALSE, 6),
(20, 'Gram-force meter', 'gf⋅m', 0.009806650000000, FALSE, 7),
(20, 'Gram-force centimeter', 'gf⋅cm', 0.000098066500000, FALSE, 8),
(20, 'Pound-force foot', 'lb-foot', 1.355817948331400, FALSE, 9),
(20, 'Pound-force inch', 'lb-inch', 0.112984829027617, FALSE, 10),
(20, 'Ounce-force foot', 'ounce-foot', 0.084738621770712, FALSE, 11),
(20, 'Ounce-force inch', 'ounce-inch', 0.007061551814226, FALSE, 12),
-- Power/Speed based units (approximate)
(20, 'Kilowatt per RPM', 'kW/rpm', 9549.296585513700000, FALSE, 13),
(20, 'Horsepower per RPM', 'HP/rpm', 7120.899616239700000, FALSE, 14);

-- =====================================================
-- VISCOSITY DYNAMIC - Category ID: 22
-- Adding comprehensive viscosity units
-- =====================================================
DELETE FROM calc_units WHERE category_id = 22;

INSERT INTO calc_units (category_id, name, symbol, to_base_multiplier, base_unit, display_order) VALUES
-- Base unit
(22, 'Pascal second', 'Pa⋅s', 1.000000000000000, TRUE, 1),
(22, 'Millipascal second', 'mPa⋅s', 0.001000000000000, FALSE, 2),
(22, 'Poise', 'P', 0.100000000000000, FALSE, 3),
(22, 'Centipoise', 'cP', 0.001000000000000, FALSE, 4),
(22, 'Kilogram per meter second', 'kg/(m⋅s)', 1.000000000000000, FALSE, 5),
(22, 'Gram per centimeter second', 'g/(cm⋅s)', 0.100000000000000, FALSE, 6),
(22, 'Pound per foot second', 'lb/(ft⋅s)', 1.488163943856500, FALSE, 7),
(22, 'Pound per foot hour', 'lb/(ft⋅h)', 0.000413378873293, FALSE, 8),
(22, 'Poundal second per square foot', 'pdl⋅s/ft²', 1.488163943856500, FALSE, 9);

-- =====================================================
-- VISCOSITY KINEMATIC - Category ID: 23
-- Adding comprehensive kinematic viscosity units
-- =====================================================
DELETE FROM calc_units WHERE category_id = 23;

INSERT INTO calc_units (category_id, name, symbol, to_base_multiplier, base_unit, display_order) VALUES
-- Base unit
(23, 'Square meter per second', 'm²/s', 1.000000000000000, TRUE, 1),
(23, 'Square centimeter per second', 'cm²/s', 0.000100000000000, FALSE, 2),
(23, 'Square millimeter per second', 'mm²/s', 0.000001000000000, FALSE, 3),
(23, 'Stokes', 'St', 0.000100000000000, FALSE, 4),
(23, 'Centistokes', 'cSt', 0.000001000000000, FALSE, 5),
(23, 'Square foot per second', 'ft²/s', 0.092903040000000, FALSE, 6),
(23, 'Square foot per hour', 'ft²/h', 0.000025806400000, FALSE, 7),
(23, 'Square inch per second', 'in²/s', 0.000645160000000, FALSE, 8);

-- =====================================================
-- LENGTH - Category ID: 12
-- Adding all missing units from Convert123
-- =====================================================
DELETE FROM calc_units WHERE category_id = 12;

INSERT INTO calc_units (category_id, name, symbol, to_base_multiplier, base_unit, display_order) VALUES
-- Base unit
(12, 'Meter', 'm', 1.000000000000000, TRUE, 1),
-- Metric
(12, 'Kilometer', 'km', 1000.000000000000000, FALSE, 2),
(12, 'Centimeter', 'cm', 0.010000000000000, FALSE, 3),
(12, 'Millimeter', 'mm', 0.001000000000000, FALSE, 4),
(12, 'Micrometer (Micron)', 'µm', 0.000001000000000, FALSE, 5),
(12, 'Nanometer', 'nm', 0.000000001000000, FALSE, 6),
(12, 'Angstrom', 'Å', 0.000000000100000, FALSE, 7),
-- Imperial / US
(12, 'Mile', 'mi', 1609.344000000000000, FALSE, 8),
(12, 'Yard', 'yd', 0.914400000000000, FALSE, 9),
(12, 'Foot', 'ft', 0.304800000000000, FALSE, 10),
(12, 'Inch', 'in', 0.025400000000000, FALSE, 11),
(12, 'Microinch', 'µin', 0.000000025400000, FALSE, 12),
-- Nautical / Marine
(12, 'Nautical mile', 'nmi', 1852.000000000000000, FALSE, 13),
(12, 'Fathom', 'ftm', 1.828800000000000, FALSE, 14),
(12, 'Cable (UK)', 'cable (UK)', 185.318400000000000, FALSE, 15),
(12, 'Cable (USA)', 'cable (USA)', 219.456000000000000, FALSE, 16),
-- Other
(12, 'Hand', 'hand', 0.101600000000000, FALSE, 17),
(12, 'Furlong', 'fur', 201.168000000000000, FALSE, 18),
(12, 'Astronomical unit', 'AU', 149597870700.000000000000000, FALSE, 19),
(12, 'Light year', 'ly', 9460730472580800.000000000000000, FALSE, 20);

-- =====================================================
-- MASS / WEIGHT - Category ID: 15
-- Adding all missing units from Convert123
-- =====================================================
DELETE FROM calc_units WHERE category_id = 15;

INSERT INTO calc_units (category_id, name, symbol, to_base_multiplier, base_unit, display_order) VALUES
-- Base unit
(15, 'Kilogram', 'kg', 1.000000000000000, TRUE, 1),
-- Metric
(15, 'Gram', 'g', 0.001000000000000, FALSE, 2),
(15, 'Milligram', 'mg', 0.000001000000000, FALSE, 3),
(15, 'Tonne (metric)', 't', 1000.000000000000000, FALSE, 4),
-- Imperial / US
(15, 'Pound', 'lb', 0.453592370000000, FALSE, 5),
(15, 'Ounce', 'oz', 0.028349523125000, FALSE, 6),
(15, 'Stone', 'st', 6.350293180000000, FALSE, 7),
(15, 'Slug', 'slug', 14.593902937200000, FALSE, 8),
-- CWT
(15, 'CWT (short) USA', 'cwt (short) USA', 45.359237000000000, FALSE, 9),
(15, 'CWT (long) UK', 'cwt (long) UK', 50.802345440000000, FALSE, 10),
-- Ton
(15, 'Ton (short) USA', 'ton (short) USA', 907.184740000000000, FALSE, 11),
(15, 'Ton (long) UK', 'ton (long) UK', 1016.046908800000000, FALSE, 12);
-- =====================================================
-- POWER - Category ID: 16
-- Adding all missing units from Convert123
-- =====================================================
DELETE FROM calc_units WHERE category_id = 16;

INSERT INTO calc_units (category_id, name, symbol, to_base_multiplier, base_unit, display_order) VALUES
-- Base unit
(16, 'Watt', 'W', 1.000000000000000, TRUE, 1),
-- Metric
(16, 'Kilowatt', 'kW', 1000.000000000000000, FALSE, 2),
(16, 'Megawatt', 'MW', 1000000.000000000000000, FALSE, 3),
(16, 'Joule per second', 'J/s', 1.000000000000000, FALSE, 4),
(16, 'Joule per minute', 'J/min', 0.016666666666667, FALSE, 5),
(16, 'Joule per hour', 'J/h', 0.000277777777778, FALSE, 6),
-- Mechanical / Imperial
(16, 'Horsepower (mechanical)', 'HP', 745.699871582270000, FALSE, 7),
(16, 'Horsepower (metric)', 'HP (metric)', 735.498750000000000, FALSE, 8),
(16, 'Foot-pound per second', 'ft⋅lb/s', 1.355817948331400, FALSE, 9),
(16, 'Foot-pound per minute', 'ft⋅lb/min', 0.022596965805523, FALSE, 10),
(16, 'Foot-pound per hour', 'ft⋅lb/h', 0.000376616096759, FALSE, 11),
-- Heat
(16, 'BTU per hour', 'BTU/h', 0.293071070172222, FALSE, 12),
(16, 'BTU per minute', 'BTU/min', 17.584264210333300, FALSE, 13),
(16, 'BTU per second', 'BTU/s', 1055.055852620000000, FALSE, 14),
(16, 'Kilocalorie per second', 'kcal/s', 4184.000000000000000, FALSE, 15),
(16, 'Kilocalorie per minute', 'kcal/min', 69.733333333333300, FALSE, 16),
(16, 'Kilocalorie per hour', 'kcal/h', 1.162222222222220, FALSE, 17),
-- Refrigeration
(16, 'Ton of refrigeration', 'RT', 3516.852842666670000, FALSE, 18),
(16, 'Kilogram-meter per second', 'kg⋅m/s', 9.806650000000000, FALSE, 19),
(16, 'Newton-meter per second', 'N⋅m/s', 1.000000000000000, FALSE, 20);

-- =====================================================
-- PRESSURE - Category ID: 17
-- Adding all missing units from Convert123
-- =====================================================
DELETE FROM calc_units WHERE category_id = 17;

INSERT INTO calc_units (category_id, name, symbol, to_base_multiplier, base_unit, display_order) VALUES
-- Base unit
(17, 'Pascal', 'Pa', 1.000000000000000, TRUE, 1),
-- Metric
(17, 'Kilopascal', 'kPa', 1000.000000000000000, FALSE, 2),
(17, 'Megapascal', 'MPa', 1000000.000000000000000, FALSE, 3),
(17, 'Bar', 'bar', 100000.000000000000000, FALSE, 4),
(17, 'Millibar', 'mbar', 100.000000000000000, FALSE, 5),
(17, 'Atmosphere (std)', 'atm', 101325.000000000000000, FALSE, 6),
(17, 'Newton per square meter', 'N/m²', 1.000000000000000, FALSE, 7),
(17, 'Kilogram-force per sq cm', 'kgf/cm²', 98066.500000000000000, FALSE, 8),
(17, 'Kilogram-force per sq meter', 'kgf/m²', 9.806650000000000, FALSE, 9),
-- Imperial
(17, 'PSI (Pound per sq inch)', 'psi', 6894.757293168360000, FALSE, 10),
(17, 'Pound per square foot', 'psf', 47.880258980335800, FALSE, 11),
(17, 'Ton-force (short) per sq ft', 'tonf(US)/ft²', 95760.517960671600000, FALSE, 12),
(17, 'Ton-force (short) per sq in', 'tonf(US)/in²', 13789514.586336700000000, FALSE, 13),
(17, 'Ton-force (short) per sq m', 'tonf(US)/m²', 8896.443230521000000, FALSE, 14),
(17, 'Ton-force (long) per sq in', 'tonf(UK)/in²', 15444256.336657000000000, FALSE, 15),
(17, 'Ton-force (long) per sq ft', 'tonf(UK)/ft²', 107251.780115674000000, FALSE, 16),
(17, 'Ton-force (long) per sq m', 'tonf(UK)/m²', 9964.016418183500000, FALSE, 17),
(17, 'Tonne-force per sq in', 'tf/in²', 15200377.340000000000000, FALSE, 18),
(17, 'Tonne-force per sq ft', 'tf/ft²', 105558.176000000000000, FALSE, 19),
(17, 'Tonne-force per sq m', 'tf/m²', 9806.650000000000000, FALSE, 20),
-- Mercury
(17, 'Torr (mmHg)', 'Torr', 133.322368421053000, FALSE, 21),
(17, 'Inch of Mercury', 'inHg', 3386.388666666670000, FALSE, 22),
-- Water
(17, 'Meter of Water (4°C)', 'mH2O', 9806.380000000000000, FALSE, 23),
(17, 'Centimeter of Water (4°C)', 'cmH2O', 98.063800000000000, FALSE, 24),
(17, 'Millimeter of Water (4°C)', 'mmH2O', 9.806380000000000, FALSE, 25),
(17, 'Inch of Water (4°C)', 'inH2O', 249.082000000000000, FALSE, 26),
(17, 'Foot of Water (4°C)', 'ftH2O', 2988.980000000000000, FALSE, 27);

-- =====================================================
-- TEMPERATURE - Category ID: 18
-- Adding all missing units from Convert123
-- Note: Logic handled in CalculatorEngine.php
-- =====================================================
DELETE FROM calc_units WHERE category_id = 18;

INSERT INTO calc_units (category_id, name, symbol, to_base_multiplier, base_unit, display_order) VALUES
(18, 'Celsius', '°C', 1.0, TRUE, 1),
(18, 'Fahrenheit', '°F', 1.0, FALSE, 2),
(18, 'Kelvin', 'K', 1.0, FALSE, 3),
(18, 'Rankine', '°R', 1.0, FALSE, 4);

-- =====================================================
-- 19. TIME (Category ID: 19)
-- =====================================================
DELETE FROM calc_units WHERE category_id = 19;
INSERT INTO calc_units (category_id, name, symbol, to_base_multiplier, base_unit, display_order) VALUES
(19, 'Second', 's', 1.000000000000000, TRUE, 1),
(19, 'Millisecond', 'ms', 0.001000000000000, FALSE, 2),
(19, 'Microsecond', 'µs', 0.000001000000000, FALSE, 3),
(19, 'Nanosecond', 'ns', 0.000000001000000, FALSE, 4),
(19, 'Minute', 'min', 60.000000000000000, FALSE, 5),
(19, 'Hour', 'h', 3600.000000000000000, FALSE, 6),
(19, 'Day', 'd', 86400.000000000000000, FALSE, 7),
(19, 'Week', 'wk', 604800.000000000000000, FALSE, 8),
(19, 'Month', 'mo', 2629746.000000000000000, FALSE, 9),
(19, 'Year', 'yr', 31556952.000000000000000, FALSE, 10),
(19, 'Leap year', 'leap year', 31622400.000000000000000, FALSE, 11);

-- =====================================================
-- 21. VELOCITY (Category ID: 21)
-- =====================================================
DELETE FROM calc_units WHERE category_id = 21;
INSERT INTO calc_units (category_id, name, symbol, to_base_multiplier, base_unit, display_order) VALUES
-- Metric (Millimeter)
(21, 'Millimeter per second', 'mm/sec', 0.001000000000000, FALSE, 1),
(21, 'Millimeter per minute', 'mm/min', 0.000016666666667, FALSE, 2),
(21, 'Millimeter per hour', 'mm/hour', 0.000000277777778, FALSE, 3),
-- Metric (Centimeter)
(21, 'Centimeter per second', 'cm/sec', 0.010000000000000, FALSE, 4),
(21, 'Centimeter per minute', 'cm/min', 0.000166666666667, FALSE, 5),
(21, 'Centimeter per hour', 'cm/hour', 0.000002777777778, FALSE, 6),
-- Metric (Meter)
(21, 'Meter per second', 'm/sec', 1.000000000000000, TRUE, 7),
(21, 'Meter per minute', 'm/min', 0.016666666666667, FALSE, 8),
(21, 'Meter per hour', 'm/hour', 0.000277777777778, FALSE, 9),
-- Metric (Kilometer)
(21, 'Kilometer per second', 'km/sec', 1000.000000000000000, FALSE, 10),
(21, 'Kilometer per minute', 'km/min', 16.666666666666667, FALSE, 11),
(21, 'Kilometer per hour', 'km/hour', 0.277777777777778, FALSE, 12),
-- Imperial (Inch)
(21, 'Inch per second', 'inch/sec', 0.025400000000000, FALSE, 13),
(21, 'Inch per minute', 'inch/min', 0.000423333333333, FALSE, 14),
(21, 'Inch per hour', 'inch/hour', 0.000007055555556, FALSE, 15),
-- Imperial (Foot)
(21, 'Foot per second', 'ft/sec', 0.304800000000000, FALSE, 16),
(21, 'Foot per minute', 'ft/min', 0.005080000000000, FALSE, 17),
(21, 'Foot per hour', 'ft/hour', 0.000084666666667, FALSE, 18),
-- Imperial (Mile)
(21, 'Mile per second', 'mile/sec', 1609.344000000000000, FALSE, 19),
(21, 'Mile per minute', 'mile/min', 26.822400000000000, FALSE, 20),
(21, 'Mile per hour', 'mile/hour', 0.447040000000000, FALSE, 21),
-- Other
(21, 'Knot', 'knot', 0.514444444444444, FALSE, 22),
(21, 'Mach (sea level)', 'Mach (sea level)', 340.290000000000000, FALSE, 23);

-- =====================================================
-- VISCOSITY DYNAMIC - Category ID: 22
-- Adding all units from Convert123
-- =====================================================
DELETE FROM calc_units WHERE category_id = 22;
INSERT INTO calc_units (category_id, name, symbol, to_base_multiplier, base_unit, display_order) VALUES
(22, 'Centipoise', 'centipoise', 0.001000000000000, FALSE, 1),
(22, 'Poise', 'Poise', 0.100000000000000, FALSE, 2),
(22, 'Pascal-second', 'Pascal-sec', 1.000000000000000, TRUE, 3),
(22, 'Newton second per sq meter', 'N-sec/m²', 1.000000000000000, FALSE, 4),
(22, 'Gram second per sq meter', 'gram-sec/m²', 0.009806650000000, FALSE, 5),
(22, 'Gram second per sq foot', 'gram-sec/ft²', 0.105558000000000, FALSE, 6),
(22, 'Kilogram second per sq meter', 'kg-sec/m²', 9.806650000000000, FALSE, 7),
(22, 'Kilogram second per sq foot', 'kg-sec/ft²', 105.558000000000000, FALSE, 8),
(22, 'Pound-force second per sq meter', 'lbf-sec/m²', 4.448221615255000, FALSE, 9),
(22, 'Pound-force second per sq foot', 'lbf-sec/ft² (Slug/ft-sec)', 47.880258980335800, FALSE, 10);

-- =====================================================
-- VISCOSITY KINEMATIC - Category ID: 23
-- Adding all units from Convert123
-- =====================================================
DELETE FROM calc_units WHERE category_id = 23;
INSERT INTO calc_units (category_id, name, symbol, to_base_multiplier, base_unit, display_order) VALUES
(23, 'Pound-force second per sq meter', 'lbf-sec/m²', 1.000000000000000, TRUE, 1),
(23, 'Pound-force second per sq foot', 'lbf-sec/ft² (Slug/ft-sec)', 0.092903040000000, FALSE, 2),
(23, 'Centistokes (sq mm per second)', 'centistokes (mm²/sec)', 0.000001000000000, FALSE, 3),
(23, 'Stokes (sq cm per second)', 'Stokes (cm²/sec)', 0.000100000000000, FALSE, 4),
(23, 'Square centimeter per second', 'cm²/sec', 0.000100000000000, FALSE, 5),
(23, 'Square meter per second', 'm²/sec', 1.000000000000000, TRUE, 6);

-- =====================================================
-- 24. VOLUME / CAPACITY (Category ID: 24)
-- Adding all units from Convert123 (US & UK variations)
-- =====================================================
DELETE FROM calc_units WHERE category_id = 24;
INSERT INTO calc_units (category_id, name, symbol, to_base_multiplier, base_unit, display_order) VALUES
-- Metric (Cubic)
(24, 'Cubic millimeter', 'mm³', 0.000000001000000, FALSE, 1),
(24, 'Cubic centimeter', 'cm³', 0.000001000000000, FALSE, 2),
(24, 'Cubic meter', 'm³', 1.000000000000000, TRUE, 3),
-- Imperial (Cubic)
(24, 'Cubic inch', 'in³', 0.000016387064000, FALSE, 4),
(24, 'Cubic foot', 'ft³', 0.028316846592000, FALSE, 5),
(24, 'Cubic yard', 'yd³', 0.764554857984000, FALSE, 6),
-- Metric (Liquid)
(24, 'Millilitre', 'mL', 0.000001000000000, FALSE, 7),
(24, 'Litre', 'L', 0.001000000000000, FALSE, 8),
(24, 'Hectolitre', 'hL', 0.100000000000000, FALSE, 9),
(24, 'Kilolitre', 'kL', 1.000000000000000, FALSE, 10),
-- USA Liquid
(24, 'Gallon (USA)', 'gal (US)', 0.003785411784000, FALSE, 11),
(24, 'Quart (USA)', 'qt (US)', 0.000946352946000, FALSE, 12),
(24, 'Pint (USA)', 'pt (US)', 0.000473176473000, FALSE, 13),
(24, 'Fluid Ounce (USA)', 'fl oz (US)', 0.000029573529563, FALSE, 14),
-- UK Liquid
(24, 'Gallon (UK)', 'gal (UK)', 0.004546090000000, FALSE, 15),
(24, 'Quart (UK)', 'qt (UK)', 0.001136522500000, FALSE, 16),
(24, 'Pint (UK)', 'pt (UK)', 0.000568261250000, FALSE, 17),
(24, 'Fluid Ounce (UK)', 'fl oz (UK)', 0.000028413062500, FALSE, 18);
