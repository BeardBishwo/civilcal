<?php

namespace App\Controllers;

use App\Core\Controller;

class HomeController extends Controller
{
    public function index()
    {
        $user = null; // Simple user check
        $isNepal = false; // Default to false

        $stats = $this->getSystemStats();
        $featuredCalculators = $this->getFeaturedCalculators();
        $testimonials = $this->getTestimonials();

        // Make the View object available in templates
        $data = [
            'user' => $user,
            'isNepal' => $isNepal,
            'stats' => $stats,
            'featuredCalculators' => $featuredCalculators,
            'testimonials' => $testimonials,
            'viewHelper' => $this->view // Pass View object to templates
        ];

        $this->view->render('index', $data);
    }


    public function features()
    {
        $this->view->render('home/features', [
            'user' => null,
            'viewHelper' => $this->view
        ]);
    }


    public function pricing()
    {
        $plans = $this->getPricingPlans();

        $this->view->render('home/pricing', [
            'user' => null,
            'plans' => $plans,
            'viewHelper' => $this->view
        ]);
    }


    public function about()
    {
        $this->view->render('home/about', [
            'user' => null,
            'viewHelper' => $this->view
        ]);
    }


    public function contact()
    {
        if ($_POST) {
            $result = $this->handleContactForm($_POST);
            echo json_encode($result);
            return;
        }

        $this->view->render('home/contact', [
            'user' => null,
            'viewHelper' => $this->view
        ]);
    }


    private function getSystemStats()
    {
        return [
            'calculators' => 56,
            'users' => 1234,
            'calculations' => 15420,
            'countries' => 25
        ];
    }

    private function getFeaturedCalculators()
    {
        return [
            [
                'category' => 'civil',
                'tool' => 'concrete-volume',
                'name' => 'Concrete Volume',
                'description' => 'Calculate concrete volume for construction projects',
                'icon' => 'bi-cone',
                'color' => 'primary'
            ],
            [
                'category' => 'electrical',
                'tool' => 'load-calculation',
                'name' => 'Electrical Load',
                'description' => 'Calculate electrical load for buildings',
                'icon' => 'bi-lightning-charge',
                'color' => 'warning'
            ],
            [
                'category' => 'structural',
                'tool' => 'beam-design',
                'name' => 'Beam Design',
                'description' => 'Design and analyze structural beams',
                'icon' => 'bi-bricks',
                'color' => 'danger'
            ],
            [
                'category' => 'hvac',
                'tool' => 'cooling-load',
                'name' => 'Cooling Load',
                'description' => 'Calculate HVAC cooling load requirements',
                'icon' => 'bi-thermometer-snow',
                'color' => 'info'
            ]
        ];
    }

    private function getTestimonials()
    {
        return [
            [
                'name' => 'John Smith',
                'role' => 'Civil Engineer',
                'company' => 'ABC Construction',
                'content' => 'Bishwo Calculator has saved me countless hours on project estimations. The concrete and rebar calculators are incredibly accurate.',
                'avatar' => '/assets/images/avatars/1.jpg',
                'rating' => 5
            ],
            [
                'name' => 'Sarah Johnson',
                'role' => 'Electrical Designer',
                'company' => 'XYZ Engineering',
                'content' => 'As an electrical designer, I use the load calculation and voltage drop tools daily. They\'re reliable and easy to use.',
                'avatar' => '/assets/images/avatars/2.jpg',
                'rating' => 5
            ],
            [
                'name' => 'Mike Chen',
                'role' => 'Structural Engineer',
                'company' => 'Structural Solutions Inc.',
                'content' => 'The beam and column design calculators have become essential tools in our design workflow. Highly recommended!',
                'avatar' => '/assets/images/avatars/3.jpg',
                'rating' => 4
            ]
        ];
    }

    private function getPricingPlans()
    {
        return [
            [
                'name' => 'Free',
                'description' => 'For students and hobbyists',
                'price_monthly' => 0,
                'price_yearly' => 0,
                'features' => [
                    '5 calculations per day',
                    'Basic calculators',
                    'Standard support',
                    'Community access'
                ],
                'button_text' => 'Get Started Free',
                'button_class' => 'btn-outline-primary',
                'popular' => false
            ],
            [
                'name' => 'Professional',
                'description' => 'For individual engineers',
                'price_monthly' => 9.99,
                'price_yearly' => 99.99,
                'features' => [
                    'Unlimited calculations',
                    'All calculators',
                    'Priority support',
                    'Export features',
                    'Advanced formulas'
                ],
                'button_text' => 'Start Professional',
                'button_class' => 'btn-primary',
                'popular' => true
            ],
            [
                'name' => 'Enterprise',
                'description' => 'For teams and companies',
                'price_monthly' => 29.99,
                'price_yearly' => 299.99,
                'features' => [
                    'Everything in Professional',
                    'Team management',
                    'API access',
                    'Custom calculators',
                    'White-label options',
                    'Dedicated support'
                ],
                'button_text' => 'Contact Sales',
                'button_class' => 'btn-outline-primary',
                'popular' => false
            ]
        ];
    }

    private function handleContactForm($data)
    {
        // Validate form data
        $errors = [];

        if (empty($data['name'])) {
            $errors[] = 'Name is required';
        }

        if (empty($data['email']) || !filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Valid email is required';
        }

        if (empty($data['message'])) {
            $errors[] = 'Message is required';
        }

        if (!empty($errors)) {
            return [
                'success' => false,
                'message' => 'Please fix the following errors:',
                'errors' => $errors
            ];
        }

        // Send email (implementation would go here)
        $emailSent = $this->sendContactEmail($data);

        if ($emailSent) {
            return [
                'success' => true,
                'message' => 'Thank you for your message! We\'ll get back to you soon.'
            ];
        } else {
            return [
                'success' => false,
                'message' => 'Failed to send message. Please try again later.'
            ];
        }
    }

    private function sendContactEmail($data)
    {
        // Email sending implementation
        // This would typically use PHPMailer or similar
        return true; // Simulate success
    }

    public function pagePreview($id)
    {
        $pageModel = new \App\Models\Page();
        $page = $pageModel->find($id);

        if (!$page) {
            http_response_code(404);
            echo "Page not found";
            return;
        }

        // Use the 'pages/page' view
        $this->view->render('pages/page', [
            'user' => null,
            'page' => $page,
            'viewHelper' => $this->view,
            'page_title' => $page['title'],
            'meta_description' => $page['meta_description']
        ]);
    }
}
