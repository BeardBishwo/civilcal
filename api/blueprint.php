<?php

/**
 * Blueprint API Controller
 * Handles dynamic SVG rendering and blueprint data retrieval
 */

if (!defined('BISHWO_CALCULATOR')) {
    header('HTTP/1.1 403 Forbidden');
    exit('Direct access not allowed.');
}

class BlueprintApiController
{
    private $blueprintModel;

    public function __construct()
    {
        // Start session if not already started
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
        $this->blueprintModel = new \App\Models\Blueprint();
    }

    /**
     * Render blueprint SVG with revealed layers based on user progress
     * GET /api/blueprint/render/{id}?user_id={user_id}
     */
    public function render($blueprintId)
    {
        try {
            $userId = $_SESSION['user_id'] ?? null;

            if (!$userId) {
                \App\Core\Logger::security("Unauthorized blueprint render attempt", ['blueprint_id' => $blueprintId]);
                $this->jsonResponse(['error' => 'Authentication required'], 401);
                return;
            }

            // Fail-fast: Check prerequisites
            if (!$this->blueprintModel->checkPrerequisites($userId, $blueprintId)) {
                \App\Core\Logger::security("Prerequisite check failed", ['user_id' => $userId, 'blueprint_id' => $blueprintId]);
                $this->jsonResponse(['error' => 'Prerequisites not met'], 403);
                return;
            }

            // Get blueprint data
            $blueprint = $this->blueprintModel->getBlueprintById($blueprintId);
            if (!$blueprint) {
                $this->jsonResponse(['error' => 'Blueprint not found'], 404);
                return;
            }

            // Get user progress
            $progress = $this->blueprintModel->getUserProgress($userId, $blueprintId);

            // Render SVG with revealed layers
            $svgContent = $this->renderBlueprintWithLayers($blueprint, $progress);

            // Return SVG content
            header('Content-Type: image/svg+xml');
            header('Cache-Control: private, max-age=3600'); // private since it depends on user progress
            echo $svgContent;
        } catch (Exception $e) {
            \App\Core\Logger::error("Blueprint render error: " . $e->getMessage(), ['blueprint_id' => $blueprintId, 'trace' => $e->getTraceAsString()]);
            $this->jsonResponse(['error' => 'Failed to render blueprint'], 500);
        }
    }

    /**
     * Get blueprint metadata
     * GET /api/blueprint/{id}
     */
    public function getBlueprint($blueprintId)
    {
        try {
            $blueprint = $this->blueprintModel->getBlueprintById($blueprintId);
            if (!$blueprint) {
                $this->jsonResponse(['error' => 'Blueprint not found'], 404);
                return;
            }

            // Remove sensitive data
            unset($blueprint['full_svg_content']);

            $this->jsonResponse($blueprint);
        } catch (Exception $e) {
            \App\Core\Logger::error("Get blueprint metadata error: " . $e->getMessage(), ['id' => $blueprintId]);
            $this->jsonResponse(['error' => 'Data retrieval failed'], 500);
        }
    }

    /**
     * Get user's blueprint progress
     * GET /api/blueprint/progress/{blueprintId}?user_id={user_id}
     */
    public function getProgress($blueprintId)
    {
        try {
            $userId = $_SESSION['user_id'] ?? null;

            if (!$userId) {
                $this->jsonResponse(['error' => 'Authentication required'], 401);
                return;
            }

            $progress = $this->blueprintModel->getUserProgress($userId, $blueprintId);
            $this->jsonResponse($progress ?: ['sections_revealed' => 0, 'total_sections' => 5]);
        } catch (Exception $e) {
            \App\Core\Logger::error("Get progress error: " . $e->getMessage(), ['user_id' => $_SESSION['user_id'] ?? 'none']);
            $this->jsonResponse(['error' => 'Failed to fetch progress'], 500);
        }
    }

    /**
     * Update user progress
     * POST /api/blueprint/progress/{blueprintId}
     */
    public function updateProgress($blueprintId)
    {
        try {
            $userId = $_SESSION['user_id'] ?? null;
            if (!$userId) {
                $this->jsonResponse(['error' => 'Authentication required'], 401);
                return;
            }

            // check if blueprint exists
            $blueprint = $this->blueprintModel->getBlueprintById($blueprintId);
            if (!$blueprint) {
                $this->jsonResponse(['error' => 'Blueprint not found'], 404);
                return;
            }

            // Fail-fast: Check prerequisites
            if (!$this->blueprintModel->checkPrerequisites($userId, $blueprintId)) {
                $this->jsonResponse(['error' => 'Prerequisites not met'], 403);
                return;
            }

            $input = json_decode(file_get_contents('php://input'), true);
            $sectionsRevealed = (int)($input['sections_revealed'] ?? 0);

            // Basic validation
            if ($sectionsRevealed < 0) {
                $this->jsonResponse(['error' => 'Invalid section count'], 400);
                return;
            }

            $success = $this->blueprintModel->updateUserProgress($userId, $blueprintId, $sectionsRevealed);

            if ($success) {
                \App\Core\Logger::info("Blueprint progress updated", ['user_id' => $userId, 'blueprint' => $blueprintId, 'count' => $sectionsRevealed]);
            }

            $this->jsonResponse(['success' => $success]);
        } catch (Exception $e) {
            \App\Core\Logger::error("Update progress error: " . $e->getMessage());
            $this->jsonResponse(['error' => 'Update failed'], 500);
        }
    }

    /**
     * Render blueprint SVG with specific layers revealed
     */
    private function renderBlueprintWithLayers($blueprint, $progress)
    {
        $svgContent = $blueprint['full_svg_content'];
        $revealedSections = $progress['sections_revealed'] ?? 0;
        $layerDefinitions = json_decode($blueprint['layer_definitions'], true);

        if (!is_array($layerDefinitions) || empty($svgContent)) {
            return $svgContent;
        }

        // Build a map of layer ID => opacity
        $layerMap = [];
        foreach ($layerDefinitions as $index => $layerId) {
            $layerMap[$layerId] = ($index < $revealedSections) ? '1' : '0';
        }

        /**
         * Robust DOM-based replacement
         * Ensures correct attribute handling and XML compliance
         */
        $dom = new DOMDocument();

        // SECURITY: Prevent XXE (XML External Entity) attacks
        // Only needed for PHP < 8.0, but good for defense-in-depth
        $oldEntityLoader = libxml_disable_entity_loader(true);

        // Use @ to suppress potential malformed SVG warnings
        // LIBXML_NONET prevents network access during parsing
        @$dom->loadXML($svgContent, LIBXML_NOERROR | LIBXML_NOWARNING | LIBXML_NONET);

        $xpath = new DOMXPath($dom);

        foreach ($layerMap as $id => $opacity) {
            // Find element by ID (works even if id is in a namespace)
            $nodes = $xpath->query("//*[@id='$id']");
            foreach ($nodes as $node) {
                if ($node instanceof DOMElement) {
                    $node->setAttribute('opacity', $opacity);
                }
            }
        }

        $result = $dom->saveXML($dom->documentElement);

        // Restore entity loader
        libxml_disable_entity_loader($oldEntityLoader);

        return $result;
    }

    /**
     * Send JSON response
     */
    private function jsonResponse($data, $statusCode = 200)
    {
        http_response_code($statusCode);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }
}
