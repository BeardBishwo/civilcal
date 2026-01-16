<?php
require_once __DIR__ . '/../app/bootstrap.php';

/**
 * Blueprint Education Content Seeder
 * Populates educational content for blueprint revelation system
 */

try {
    $pdo = \App\Core\Database::getInstance()->getPdo();

    echo "🌱 Seeding blueprint education content...\n";

    // Educational content for wrong answers and learning reinforcement
    $educationContent = [
        // Structural Beam Layout - Foundation Section
        [
            'blueprint_id' => 1,
            'section_order' => 1,
            'wrong_answer_type' => 'missing_foundation',
            'educational_content' => 'Foundations are critical for structural integrity. Without proper footings, buildings can settle unevenly or collapse under load. Always verify soil bearing capacity and design foundations accordingly.',
            'reinforcement_hint' => 'Look for concrete footings or piles that transfer building weight to stable soil.',
            'key_learning_points' => '["Foundation design", "Soil bearing capacity", "Load transfer", "Settlement prevention"]'
        ],
        [
            'blueprint_id' => 1,
            'section_order' => 1,
            'wrong_answer_type' => 'incorrect_foundation_size',
            'educational_content' => 'Foundation size must match structural loads. Undersized foundations can lead to excessive settlement. Calculate footing area based on allowable soil pressure and total building load.',
            'reinforcement_hint' => 'Foundation elements should be proportional to the structure above them.',
            'key_learning_points' => '["Load calculations", "Soil mechanics", "Foundation sizing", "Safety factors"]'
        ],

        // Structural Beam Layout - Column Section
        [
            'blueprint_id' => 1,
            'section_order' => 2,
            'wrong_answer_type' => 'missing_columns',
            'educational_content' => 'Columns are primary load-bearing elements that transfer gravity loads from beams to foundations. Without columns, the structure cannot stand. They must be designed for axial compression and buckling.',
            'reinforcement_hint' => 'Vertical structural elements that carry compressive loads from above.',
            'key_learning_points' => '["Axial compression", "Buckling resistance", "Load paths", "Structural hierarchy"]'
        ],
        [
            'blueprint_id' => 1,
            'section_order' => 2,
            'wrong_answer_type' => 'incorrect_column_placement',
            'educational_content' => 'Column placement affects load distribution and building functionality. Columns should align with load-bearing walls and be spaced to support beam spans economically.',
            'reinforcement_hint' => 'Columns should be positioned to create efficient load transfer paths.',
            'key_learning_points' => '["Load distribution", "Building layout", "Span limitations", "Economic design"]'
        ],

        // Structural Beam Layout - Beam Section
        [
            'blueprint_id' => 1,
            'section_order' => 3,
            'wrong_answer_type' => 'missing_beams',
            'educational_content' => 'Beams resist bending moments and shear forces from applied loads. They span between supports and distribute loads to columns. Proper beam design prevents deflection and structural failure.',
            'reinforcement_hint' => 'Horizontal members that span between vertical supports and carry transverse loads.',
            'key_learning_points' => '["Bending moments", "Shear forces", "Deflection limits", "Load distribution"]'
        ],
        [
            'blueprint_id' => 1,
            'section_order' => 3,
            'wrong_answer_type' => 'incorrect_beam_size',
            'educational_content' => 'Beam size depends on span length, load magnitude, and deflection requirements. Larger beams increase material costs but smaller beams may deflect excessively or fail.',
            'reinforcement_hint' => 'Beam depth typically ranges from 1/10 to 1/15 of the span length.',
            'key_learning_points' => '["Span-depth ratios", "Load calculations", "Deflection criteria", "Material optimization"]'
        ],

        // Structural Beam Layout - Reinforcement Section
        [
            'blueprint_id' => 1,
            'section_order' => 4,
            'wrong_answer_type' => 'missing_reinforcement',
            'educational_content' => 'Concrete has high compressive strength but low tensile strength. Steel reinforcement provides the tensile capacity concrete lacks, working together in composite action.',
            'reinforcement_hint' => 'Steel bars or mesh embedded in concrete to resist tensile forces.',
            'key_learning_points' => '["Composite action", "Tensile strength", "Concrete properties", "Reinforcement ratios"]'
        ],
        [
            'blueprint_id' => 1,
            'section_order' => 4,
            'wrong_answer_type' => 'insufficient_reinforcement',
            'educational_content' => 'Minimum reinforcement ratios prevent sudden brittle failure. Maximum ratios ensure proper concrete placement and bonding. Reinforcement must be properly anchored and spliced.',
            'reinforcement_hint' => 'Reinforcement should be continuous through load transfer regions.',
            'key_learning_points' => '["Minimum reinforcement", "Development length", "Splice requirements", "Bond strength"]'
        ],

        // Reinforced Concrete Slab - Concrete Section
        [
            'blueprint_id' => 2,
            'section_order' => 1,
            'wrong_answer_type' => 'missing_concrete_cover',
            'educational_content' => 'Concrete cover protects reinforcement from corrosion and fire. Insufficient cover leads to premature deterioration. Cover requirements vary by exposure conditions and fire rating.',
            'reinforcement_hint' => 'The distance from concrete surface to nearest reinforcing bar.',
            'key_learning_points' => '["Corrosion protection", "Fire resistance", "Cover requirements", "Durability design"]'
        ],
        [
            'blueprint_id' => 2,
            'section_order' => 1,
            'wrong_answer_type' => 'incorrect_concrete_strength',
            'educational_content' => 'Concrete strength affects design calculations and construction requirements. Higher strength concrete allows more efficient designs but requires better quality control.',
            'reinforcement_hint' => 'Concrete compressive strength is specified in MPa or psi (e.g., C25, f\'c = 25 MPa).',
            'key_learning_points' => '["Compressive strength", "Mix design", "Quality control", "Strength testing"]'
        ],

        // Reinforced Concrete Slab - Reinforcement Section
        [
            'blueprint_id' => 2,
            'section_order' => 2,
            'wrong_answer_type' => 'incorrect_rebar_spacing',
            'educational_content' => 'Rebar spacing affects crack control and load distribution. Closer spacing provides better crack control but increases costs. Maximum spacing limits crack widths under service loads.',
            'reinforcement_hint' => 'Typical spacing ranges from 100-300mm depending on design requirements.',
            'key_learning_points' => '["Crack control", "Load distribution", "Spacing limits", "Construction tolerances"]'
        ],
        [
            'blueprint_id' => 2,
            'section_order' => 2,
            'wrong_answer_type' => 'missing_stirrups',
            'educational_content' => 'Stirrups resist shear forces and confine concrete. Without adequate shear reinforcement, beams and slabs can fail suddenly in shear. Stirrups also prevent buckling of compression reinforcement.',
            'reinforcement_hint' => 'Closed loops of steel that encircle longitudinal reinforcement.',
            'key_learning_points' => '["Shear resistance", "Confinement", "Stirrup spacing", "Shear design"]'
        ]
    ];

    $stmt = $pdo->prepare("
        INSERT INTO blueprint_education (
            blueprint_id, section_order, wrong_answer_type, educational_content,
            reinforcement_hint, key_learning_points
        ) VALUES (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            educational_content = VALUES(educational_content),
            reinforcement_hint = VALUES(reinforcement_hint)
    ");

    foreach ($educationContent as $content) {
        $stmt->execute([
            $content['blueprint_id'],
            $content['section_order'],
            $content['wrong_answer_type'],
            $content['educational_content'],
            $content['reinforcement_hint'],
            $content['key_learning_points']
        ]);
    }

    echo "✅ Successfully seeded " . count($educationContent) . " educational content entries\n";

    echo "🎉 Blueprint education content seeding completed!\n";

} catch (Exception $e) {
    echo "❌ Error seeding blueprint education content: " . $e->getMessage() . "\n";
    exit(1);
}