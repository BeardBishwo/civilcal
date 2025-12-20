<?php

namespace App\Controllers;

use App\Core\Controller;

/**
 * Developer Controller
 * Handles API documentation, developer resources, and SDK information
 */
class DeveloperController extends Controller
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Show developer documentation main page
     */
    public function index()
    {
        $data = [
            'title' => 'API Reference for Developers - ' . \App\Services\SettingsService::get('site_name', 'Engineering Calculator Pro'),
            'description' => 'Complete API documentation for integrating engineering calculations into your applications',
            'category' => 'developer',
            'page_title' => 'API Reference for Developers',
            'api_sections' => $this->getApiSections(),
            'quick_start' => $this->getQuickStartGuide(),
            'featured_endpoints' => $this->getFeaturedEndpoints(),
            'code_examples' => $this->getCodeExamples(),
            'sdk_info' => $this->getSdkInfo()
        ];

        $this->view->render('developer/index', $data);
    }

    /**
     * Show specific API endpoint documentation
     */
    public function endpoint($category, $endpoint = null)
    {
        if (!$endpoint) {
            // Show category overview
            return $this->category($category);
        }

        $endpointData = $this->getEndpointData($category, $endpoint);
        
        if (!$endpointData) {
            $this->redirect('/developers');
            return;
        }

        $data = [
            'title' => $endpointData['title'] . ' - API Documentation',
            'description' => $endpointData['description'],
            'category' => 'developer',
            'page_title' => $endpointData['title'],
            'endpoint' => $endpointData,
            'related_endpoints' => $this->getRelatedEndpoints($category)
        ];

        $this->view->render('developer/endpoint', $data);
    }

    /**
     * Show API category
     */
    public function category($category)
    {
        $categoryInfo = $this->getCategoryInfo($category);
        
        if (!$categoryInfo) {
            $this->redirect('/developers');
            return;
        }

        $data = [
            'title' => $categoryInfo['name'] . ' API - Developer Documentation',
            'description' => 'API documentation for ' . $categoryInfo['name'],
            'section' => 'developer',
            'page_title' => $categoryInfo['name'] . ' API',
            'category' => $categoryInfo,
            'endpoints' => $this->getEndpointsByCategory($category)
        ];

        $this->view->render('developer/category', $data);
    }

    /**
     * Show SDK documentation
     */
    public function sdk($language = null)
    {
        if (!$language) {
            // Show SDK overview
            $data = [
                'page_title' => 'SDKs and Libraries',
                'sdks' => $this->getAllSdks()
            ];
            $this->view->render('developer/sdk-overview', $data);
            return;
        }

        $sdkData = $this->getSdkData($language);
        
        if (!$sdkData) {
            $this->redirect('/developers/sdk');
            return;
        }

        $data = [
            'title' => $sdkData['name'] . ' SDK - Developer Documentation',
            'category' => 'developer',
            'page_title' => $sdkData['name'] . ' SDK',
            'sdk' => $sdkData
        ];

        $this->view->render('developer/sdk', $data);
    }

    /**
     * API playground/tester
     */
    public function playground()
    {
        $data = [
            'title' => 'API Playground - Test Engineering Calculator APIs',
            'category' => 'developer',
            'page_title' => 'API Playground',
            'endpoints' => $this->getPlaygroundEndpoints()
        ];

        $this->view->render('developer/playground', $data);
    }

    /**
     * Get API sections for navigation
     */
    private function getApiSections()
    {
        return [
            [
                'slug' => 'getting-started',
                'name' => 'Getting Started',
                'description' => 'Authentication, rate limits, and basic usage',
                'icon' => 'fas fa-play-circle',
                'color' => '#10b981',
                'endpoints' => 4
            ],
            [
                'slug' => 'calculations',
                'name' => 'Calculations',
                'description' => 'Perform engineering calculations via API',
                'icon' => 'fas fa-calculator',
                'color' => '#3b82f6',
                'endpoints' => 12
            ],
            [
                'slug' => 'civil-engineering',
                'name' => 'Civil Engineering',
                'description' => 'Concrete, steel, and structural calculations',
                'icon' => 'fas fa-hard-hat',
                'color' => '#8b5cf6',
                'endpoints' => 8
            ],
            [
                'slug' => 'electrical',
                'name' => 'Electrical',
                'description' => 'Load calculations and electrical design',
                'icon' => 'fas fa-bolt',
                'color' => '#f59e0b',
                'endpoints' => 6
            ],
            [
                'slug' => 'account',
                'name' => 'Account',
                'description' => 'User management and account operations',
                'icon' => 'fas fa-user-cog',
                'color' => '#ef4444',
                'endpoints' => 5
            ],
            [
                'slug' => 'webhooks',
                'name' => 'Webhooks',
                'description' => 'Real-time notifications and events',
                'icon' => 'fas fa-webhook',
                'color' => '#6b7280',
                'endpoints' => 3
            ]
        ];
    }

    /**
     * Get quick start guide
     */
    private function getQuickStartGuide()
    {
        return [
            'steps' => [
                [
                    'title' => 'Get API Key',
                    'description' => 'Sign up and generate your API key from the developer dashboard',
                    'code' => 'curl -X POST https://api.example.com/auth/register \\\n  -H "Content-Type: application/json" \\\n  -d \'{"email": "your@email.com", "password": "secure_password"}\''
                ],
                [
                    'title' => 'Make First Request',
                    'description' => 'Test your API key with a simple calculation request',
                    'code' => 'curl -X POST https://api.example.com/v1/calculations/concrete/volume \\\n  -H "Authorization: Bearer YOUR_API_KEY" \\\n  -H "Content-Type: application/json" \\\n  -d \'{"length": 10, "width": 5, "height": 0.2, "unit": "m"}\''
                ],
                [
                    'title' => 'Handle Response',
                    'description' => 'Process the JSON response in your application',
                    'code' => '{\n  "success": true,\n  "result": {\n    "volume": 10.0,\n    "unit": "m³",\n    "calculation_id": "calc_123456"\n  }\n}'
                ]
            ]
        ];
    }

    /**
     * Get featured endpoints
     */
    private function getFeaturedEndpoints()
    {
        return [
            [
                'name' => 'Concrete Volume Calculator',
                'method' => 'POST',
                'endpoint' => '/v1/calculations/concrete/volume',
                'description' => 'Calculate concrete volume for slabs, beams, and columns',
                'category' => 'Civil Engineering'
            ],
            [
                'name' => 'Electrical Load Calculator',
                'method' => 'POST',
                'endpoint' => '/v1/calculations/electrical/load',
                'description' => 'Calculate electrical load requirements and sizing',
                'category' => 'Electrical'
            ],
            [
                'name' => 'Steel Beam Analysis',
                'method' => 'POST',
                'endpoint' => '/v1/calculations/structural/beam',
                'description' => 'Analyze steel beam capacity and deflection',
                'category' => 'Structural'
            ]
        ];
    }

    /**
     * Get code examples
     */
    private function getCodeExamples()
    {
        return [
            'javascript' => [
                'name' => 'JavaScript/Node.js',
                'code' => 'const response = await fetch(\'https://api.example.com/v1/calculations/concrete/volume\', {\n  method: \'POST\',\n  headers: {\n    \'Authorization\': \'Bearer YOUR_API_KEY\',\n    \'Content-Type\': \'application/json\'\n  },\n  body: JSON.stringify({\n    length: 10,\n    width: 5,\n    height: 0.2,\n    unit: \'m\'\n  })\n});\n\nconst result = await response.json();\nconsole.log(result);'
            ],
            'python' => [
                'name' => 'Python',
                'code' => 'import requests\n\nurl = "https://api.example.com/v1/calculations/concrete/volume"\nheaders = {\n    "Authorization": "Bearer YOUR_API_KEY",\n    "Content-Type": "application/json"\n}\ndata = {\n    "length": 10,\n    "width": 5,\n    "height": 0.2,\n    "unit": "m"\n}\n\nresponse = requests.post(url, headers=headers, json=data)\nresult = response.json()\nprint(result)'
            ],
            'php' => [
                'name' => 'PHP',
                'code' => '<?php\n$url = "https://api.example.com/v1/calculations/concrete/volume";\n$headers = [\n    "Authorization: Bearer YOUR_API_KEY",\n    "Content-Type: application/json"\n];\n$data = json_encode([\n    "length" => 10,\n    "width" => 5,\n    "height" => 0.2,\n    "unit" => "m"\n]);\n\n$ch = curl_init();\ncurl_setopt($ch, CURLOPT_URL, $url);\ncurl_setopt($ch, CURLOPT_POST, true);\ncurl_setopt($ch, CURLOPT_POSTFIELDS, $data);\ncurl_setopt($ch, CURLOPT_HTTPHEADER, $headers);\ncurl_setopt($ch, CURLOPT_RETURNTRANSFER, true);\n\n$result = curl_exec($ch);\ncurl_close($ch);\n\necho $result;\n?>'
            ]
        ];
    }

    /**
     * Get SDK information
     */
    private function getSdkInfo()
    {
        return [
            [
                'name' => 'JavaScript SDK',
                'language' => 'javascript',
                'version' => '2.1.0',
                'description' => 'Official JavaScript SDK for web and Node.js applications',
                'install' => 'npm install @engicalc/sdk',
                'github' => 'https://github.com/engicalc/javascript-sdk'
            ],
            [
                'name' => 'Python SDK',
                'language' => 'python',
                'version' => '1.8.2',
                'description' => 'Python SDK for data science and engineering applications',
                'install' => 'pip install engicalc-python',
                'github' => 'https://github.com/engicalc/python-sdk'
            ],
            [
                'name' => 'PHP SDK',
                'language' => 'php',
                'version' => '1.5.1',
                'description' => 'PHP SDK for web applications and WordPress plugins',
                'install' => 'composer require engicalc/php-sdk',
                'github' => 'https://github.com/engicalc/php-sdk'
            ]
        ];
    }

    /**
     * Get endpoint data by category and endpoint
     */
    private function getEndpointData($category, $endpoint)
    {
        $endpoints = [
            'calculations' => [
                'concrete-volume' => [
                    'title' => 'Concrete Volume Calculator',
                    'method' => 'POST',
                    'endpoint' => '/v1/calculations/concrete/volume',
                    'description' => 'Calculate concrete volume for various structural elements',
                    'parameters' => [
                        [
                            'name' => 'length',
                            'type' => 'number',
                            'required' => true,
                            'description' => 'Length of the concrete element'
                        ],
                        [
                            'name' => 'width',
                            'type' => 'number',
                            'required' => true,
                            'description' => 'Width of the concrete element'
                        ],
                        [
                            'name' => 'height',
                            'type' => 'number',
                            'required' => true,
                            'description' => 'Height/thickness of the concrete element'
                        ],
                        [
                            'name' => 'unit',
                            'type' => 'string',
                            'required' => false,
                            'description' => 'Unit of measurement (m, ft, in)',
                            'default' => 'm'
                        ]
                    ],
                    'response_example' => [
                        'success' => true,
                        'result' => [
                            'volume' => 10.0,
                            'unit' => 'm³',
                            'calculation_id' => 'calc_123456',
                            'timestamp' => '2025-11-14T10:30:00Z'
                        ]
                    ],
                    'code_examples' => $this->getEndpointCodeExamples('concrete-volume')
                ]
            ]
        ];

        return $endpoints[$category][$endpoint] ?? null;
    }

    /**
     * Get code examples for specific endpoint
     */
    private function getEndpointCodeExamples($endpoint)
    {
        return [
            'curl' => 'curl -X POST https://api.example.com/v1/calculations/concrete/volume \\\n  -H "Authorization: Bearer YOUR_API_KEY" \\\n  -H "Content-Type: application/json" \\\n  -d \'{"length": 10, "width": 5, "height": 0.2, "unit": "m"}\'',
            'javascript' => 'const result = await engicalc.calculations.concrete.volume({\n  length: 10,\n  width: 5,\n  height: 0.2,\n  unit: "m"\n});',
            'python' => 'result = client.calculations.concrete.volume(\n    length=10,\n    width=5,\n    height=0.2,\n    unit="m"\n)'
        ];
    }

    /**
     * Get all SDKs
     */
    private function getAllSdks()
    {
        return $this->getSdkInfo();
    }

    /**
     * Get SDK data by language
     */
    private function getSdkData($language)
    {
        $sdks = [
            'javascript' => [
                'name' => 'JavaScript SDK',
                'language' => 'JavaScript',
                'version' => '2.1.0',
                'description' => 'Official JavaScript SDK for web browsers and Node.js applications',
                'install_command' => 'npm install @engicalc/sdk',
                'github_url' => 'https://github.com/engicalc/javascript-sdk',
                'documentation' => $this->getJavaScriptSdkDocs()
            ],
            'python' => [
                'name' => 'Python SDK',
                'language' => 'Python',
                'version' => '1.8.2',
                'description' => 'Python SDK for data science and engineering applications',
                'install_command' => 'pip install vendor-python',
                'github_url' => 'https://github.com/vendor/python-sdk',
                'documentation' => $this->getPythonSdkDocs()
            ]
        ];

        return $sdks[$language] ?? null;
    }

    /**
     * Get JavaScript SDK documentation
     */
    private function getJavaScriptSdkDocs()
    {
        return [
            'installation' => 'npm install @engicalc/sdk',
            'quick_start' => 'import EngiCalc from \'@engicalc/sdk\';\n\nconst client = new EngiCalc({\n  apiKey: \'your-api-key\'\n});\n\nconst result = await client.calculations.concrete.volume({\n  length: 10,\n  width: 5,\n  height: 0.2\n});',
            'examples' => [
                [
                    'title' => 'Basic Usage',
                    'code' => 'const client = new EngiCalc({ apiKey: "your-key" });\nconst volume = await client.calculations.concrete.volume({...});'
                ],
                [
                    'title' => 'Error Handling',
                    'code' => 'try {\n  const result = await client.calculations.concrete.volume({...});\n} catch (error) {\n  console.error("Calculation failed:", error.message);\n}'
                ]
            ]
        ];
    }

    /**
     * Get Python SDK documentation
     */
    private function getPythonSdkDocs()
    {
        return [
            'installation' => 'pip install engicalc-python',
            'quick_start' => 'from engicalc import EngiCalc\n\nclient = EngiCalc(api_key="your-api-key")\n\nresult = client.calculations.concrete.volume(\n    length=10,\n    width=5,\n    height=0.2\n)',
            'examples' => [
                [
                    'title' => 'Basic Usage',
                    'code' => 'client = EngiCalc(api_key="your-key")\nvolume = client.calculations.concrete.volume(...)'
                ],
                [
                    'title' => 'Batch Processing',
                    'code' => 'results = client.batch_calculate([\n    {"type": "concrete.volume", "params": {...}},\n    {"type": "steel.weight", "params": {...}}\n])'
                ]
            ]
        ];
    }

    /**
     * Get category info
     */
    private function getCategoryInfo($slug)
    {
        $sections = $this->getApiSections();
        
        foreach ($sections as $section) {
            if ($section['slug'] === $slug) {
                return $section;
            }
        }
        
        return null;
    }

    /**
     * Get endpoints by category
     */
    private function getEndpointsByCategory($category)
    {
        // Mock data - in real app, query database
        return [
            [
                'name' => 'Concrete Volume',
                'method' => 'POST',
                'endpoint' => '/v1/calculations/concrete/volume',
                'description' => 'Calculate concrete volume for slabs, beams, columns'
            ],
            [
                'name' => 'Steel Weight',
                'method' => 'POST',
                'endpoint' => '/v1/calculations/steel/weight',
                'description' => 'Calculate steel weight and material quantities'
            ]
        ];
    }

    /**
     * Get related endpoints
     */
    private function getRelatedEndpoints($category)
    {
        return $this->getEndpointsByCategory($category);
    }

    /**
     * Get playground endpoints
     */
    private function getPlaygroundEndpoints()
    {
        return [
            [
                'name' => 'Concrete Volume',
                'method' => 'POST',
                'endpoint' => '/v1/calculations/concrete/volume',
                'category' => 'Civil Engineering'
            ],
            [
                'name' => 'Electrical Load',
                'method' => 'POST',
                'endpoint' => '/v1/calculations/electrical/load',
                'category' => 'Electrical'
            ]
        ];
    }
}
