<?php
require_once 'app/bootstrap.php';
$db = App\Core\Database::getInstance();

// Configuration
$position_level_id = 2; // Sub-Engineer
$course_id = 295; // Civil Engineering
$edu_level_id = 302; // Level 5 Sub-Engineer
$questions_per_type_node = 10;
$stream_slug = 'civil';

// Templates for different types
$typeTemplates = [
    'TF' => [
        ["q" => "Is {node_title} mandatory for all high-rise buildings in Nepal?", "ans" => true, "ex" => "Nepal's building code specifically requires {node_title} for safety in seismic zones."],
        ["q" => "Does concrete reach its maximum strength within 7 days of {node_title}?", "ans" => false, "ex" => "Concrete typically reaches 70% strength in 7 days but takes 28 days for full design strength in {node_title}."],
        ["q" => "Can {node_title} be performed without a certified engineer if the budget is low?", "ans" => false, "ex" => "Safety and legal standards require professional oversight for all {node_title} works."],
        ["q" => "Is 'Total Station' the most legacy tool used for {node_title} today?", "ans" => false, "ex" => "Total Station is a modern electronic instrument, not a legacy one, for {node_title}."],
        ["q" => "Does {node_title} significantly impact the carbon footprint of a project?", "ans" => true, "ex" => "Construction activities and material choices in {node_title} are major contributors to carbon emissions."],
        ["q" => "Is the 'Slump Test' used to measure the tensile strength of {node_title}?", "ans" => false, "ex" => "The Slump Test measures workability/consistency, not tensile strength."],
        ["q" => "Are fly ash bricks heavier than traditional clay bricks in {node_title}?", "ans" => false, "ex" => "Fly ash bricks are usually lighter and more uniform."],
        ["q" => "Is water-cement ratio the most critical factor in {node_title} strength?", "ans" => true, "ex" => "According to Abram's law, the water-cement ratio is primary for {node_title} strength."],
        ["q" => "Does {node_title} exclude the use of digital automation?", "ans" => false, "ex" => "Digital twins and automation are becoming standard in modern {node_title}."],
        ["q" => "Is seismic resistance a secondary concern in {node_title} for Nepal?", "ans" => false, "ex" => "In a high-risk zone like Nepal, seismic resistance is a primary requirement for {node_title}."]
    ],
    'MULTI' => [
        ["q" => "Select the key components of a successful {node_title} plan:", "options" => ["Risk Assessment", "Stakeholder Buy-in", "Budget Padding", "Technical Viability"], "correct" => [0, 1, 3]],
        ["q" => "Which materials are sustainable choices for {node_title}?", "options" => ["Bambo", "Recycled Steel", "Asbestos", "Rammed Earth"], "correct" => [0, 1, 3]],
        ["q" => "Identify the professional roles involved in {node_title}:", "options" => ["Structural Engineer", "Surveyor", "Legal Advisor", "Site Supervisor"], "correct" => [0, 1, 3]],
        ["q" => "Which factors affect the durability of {node_title} structures?", "options" => ["Corrosion", "Weathering", "Paint Color", "Chemical Attack"], "correct" => [0, 1, 3]],
        ["q" => "What are the common non-destructive tests for {node_title}?", "options" => ["Rebound Hammer", "UPV", "Cylinder Crush", "Dye Penetrant"], "correct" => [0, 1, 3]],
        ["q" => "Select the stages of the {node_title} project lifecycle:", "options" => ["Feasibility", "Design", "Procurement", "Celebration"], "correct" => [0, 1, 2]],
        ["q" => "Which software tools are used for {node_title} calculation?", "options" => ["ETABS", "STAAD Pro", "Photoshop", "MS Project"], "correct" => [0, 1, 3]],
        ["q" => "What are the advantages of pre-cast {node_title}?", "options" => ["Speed", "Quality Control", "Luxury", "Less On-site Waste"], "correct" => [0, 1, 3]],
        ["q" => "Identify safety gear essential for {node_title} site work:", "options" => ["Helmet", "Steel-toe Boots", "Sunglasses", "High-viz jacket"], "correct" => [0, 1, 3]],
        ["q" => "Which of these are types of shallow foundations in {node_title}?", "options" => ["Isolated Footing", "Raft", "Pile", "Strip Footing"], "correct" => [0, 1, 3]]
    ],
    'ORDER' => [
        ["q" => "Arrange the steps of {node_title} construction in order:", "options" => ["Excavation", "Concreting", "Formwork", "Curing"], "order" => [1, 3, 2, 4]],
        ["q" => "Sort the phases of {node_title} project management:", "options" => ["Initiation", "Planning", "Execution", "Closing"], "order" => [1, 2, 3, 4]],
        ["q" => "Rank the concrete grades by strength for {node_title}:", "options" => ["M15", "M20", "M25", "M30"], "order" => [1, 2, 3, 4]],
        ["q" => "Order the soil layers for foundation in {node_title}:", "options" => ["Top Soil", "Sub Soil", "Weathered Rock", "Bedrock"], "order" => [1, 2, 3, 4]],
        ["q" => "Sequence the testing procedure for {node_title} materials:", "options" => ["Sampling", "Preparation", "Testing", "Reporting"], "order" => [1, 2, 3, 4]]
    ],
    'THEORY' => [
        ["q" => "Explain the significance of {node_title} in the context of urban development in Nepal.", "type" => "short", "marks" => 5.0],
        ["q" => "Discuss the challenges and solutions for sustainable {node_title} in rural areas.", "type" => "long", "marks" => 10.0],
        ["q" => "What are the primary factors that cause failure in {node_title} structures? List at least five.", "type" => "short", "marks" => 5.0],
        ["q" => "Describe the impact of the 2015 earthquake on {node_title} standards in Nepal.", "type" => "long", "marks" => 15.0],
        ["q" => "Compare and contrast traditional and modern {node_title} techniques.", "type" => "short", "marks" => 5.0]
    ]
];

// Fetch all nodes
$nodes = $db->query("SELECT id, title, type, parent_id FROM syllabus_nodes WHERE type IN ('category', 'sub_category')")->fetchAll();

echo "Starting massive seeding for " . count($nodes) . " nodes and 5 types...\n";

$db->beginTransaction();

try {
    $insertedCount = 0;
    foreach ($nodes as $node) {
        $node_id = $node['id'];
        $node_title = $node['title'];
        $node_type = $node['type'];
        
        $cat_id = ($node_type == 'category') ? $node_id : $node['parent_id'];
        $sub_cat_id = ($node_type == 'sub_category') ? $node_id : null;

        // Iterate through requested types
        foreach (['TF', 'MULTI', 'ORDER', 'THEORY_SHORT', 'THEORY_LONG'] as $targetType) {
            
            for ($i = 0; $i < $questions_per_type_node; $i++) {
                $type = $targetType;
                $theoryType = null;
                $marks = 1.0;
                $q_options = [];
                $q_text = "";
                $ex = "";

                if ($targetType == 'TF') {
                    $tmpl = $typeTemplates['TF'][$i % count($typeTemplates['TF'])];
                    $q_text = str_replace("{node_title}", $node_title, $tmpl['q']);
                    $ex = str_replace("{node_title}", $node_title, $tmpl['ex']);
                    $q_options = [
                        ['id' => 1, 'text' => 'True', 'is_correct' => ($tmpl['ans'] ? 1 : 0)],
                        ['id' => 2, 'text' => 'False', 'is_correct' => ($tmpl['ans'] ? 0 : 1)]
                    ];
                } elseif ($targetType == 'MULTI') {
                    $tmpl = $typeTemplates['MULTI'][$i % count($typeTemplates['MULTI'])];
                    $q_text = str_replace("{node_title}", $node_title, $tmpl['q']);
                    foreach ($tmpl['options'] as $idx => $optT) {
                        $q_options[] = [
                            'id' => $idx + 1,
                            'text' => $optT,
                            'is_correct' => (in_array($idx, $tmpl['correct']) ? 1 : 0)
                        ];
                    }
                } elseif ($targetType == 'ORDER') {
                    $tmpl = $typeTemplates['ORDER'][$i % (count($typeTemplates['ORDER']) )];
                    $q_text = str_replace("{node_title}", $node_title, $tmpl['q']);
                    foreach ($tmpl['options'] as $idx => $optT) {
                        $q_options[] = [
                            'id' => $idx + 1,
                            'text' => $optT,
                            'is_correct' => 0 // In Order, we might use priority or just the sequence
                        ];
                    }
                    $marks = 2.0;
                } elseif (strpos($targetType, 'THEORY') !== false) {
                    $isLong = ($targetType == 'THEORY_LONG');
                    $type = 'THEORY';
                    $theoryType = $isLong ? 'long' : 'short';
                    $tmpl = $typeTemplates['THEORY'][$i % count($typeTemplates['THEORY'])];
                    
                    // Filter template type match if possible, otherwise pick any
                    if (($isLong && $tmpl['type'] != 'long') || (!$isLong && $tmpl['type'] == 'long')) {
                         // Find another one
                         foreach($typeTemplates['THEORY'] as $tt) {
                             if(($isLong && $tt['type'] == 'long') || (!$isLong && $tt['type'] == 'short')) {
                                 $tmpl = $tt; break;
                             }
                         }
                    }
                    
                    $q_text = str_replace("{node_title}", $node_title, $tmpl['q']);
                    $marks = $tmpl['marks'];
                }

                $data = [
                    'unique_code' => 'SEED-' . $targetType . '-' . $node_id . '-' . $i . '-' . time() . '-' . rand(1000,9999),
                    'topic_id' => null,
                    'course_id' => $course_id,
                    'edu_level_id' => $edu_level_id,
                    'category_id' => $cat_id,
                    'sub_category_id' => $sub_cat_id,
                    'type' => $type,
                    'theory_type' => $theoryType,
                    'content' => json_encode(['text' => $q_text, 'image' => null]),
                    'options' => json_encode($q_options),
                    'answer_explanation' => $ex,
                    'difficulty_level' => ($i % 3) + 1,
                    'tags' => json_encode(["seeded", strtolower($targetType), strtolower($node_title)]),
                    'default_marks' => $marks,
                    'default_negative_marks' => ($type == 'THEORY' ? 0.0 : 0.2), 
                    'status' => 'approved',
                    'is_active' => 1,
                    'created_by' => 1,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'slug' => strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $q_text . '-' . $targetType), '-')) . '-' . substr(md5(uniqid()), 0, 8)
                ];

                if ($db->insert('quiz_questions', $data)) {
                    $questionId = $db->lastInsertId();
                    $db->insert('question_position_levels', ['question_id' => $questionId, 'position_level_id' => $position_level_id]);
                    $db->insert('question_stream_map', [
                        'question_id' => $questionId, 'stream' => $stream_slug, 'syllabus_node_id' => $node_id,
                        'difficulty_in_stream' => $data['difficulty_level'], 'priority' => 1, 'is_primary' => 1
                    ]);
                    $insertedCount++;
                }
            }
        }
    }
    
    $db->commit();
    echo "Successfully seeded $insertedCount new questions of mixed types!\n";

} catch (Exception $e) {
    if ($db->inTransaction()) { try { $db->rollBack(); } catch(Exception $rex) {} }
    echo "Error: " . $e->getMessage() . "\n";
}
