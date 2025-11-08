

## 🏗️ **ARCHITECTURE DIAGRAM**

```
┌─────────────────────────────────────────────────────────────┐
│                    CLIENT REQUESTS                          │
│  GET /calculators/civil/concrete-volume                     │
│  POST /api/calculate                                        │
│  GET /admin/users                                           │
└─────────────────────────────────────────────────────────────┘
                               │
┌─────────────────────────────────────────────────────────────┐
│                    PUBLIC/INDEX.PHP                         │
│  • Front Controller                                         │
│  • Bootstrap Application                                   │
│  • Handle All Requests                                     │
└─────────────────────────────────────────────────────────────┘
                               │
┌─────────────────────────────────────────────────────────────┐
│                    APP/CORE/ROUTER.PHP                      │
│  • Route Matching                                          │
│  • Middleware Execution                                    │
│  • Controller Dispatching                                  │
└─────────────────────────────────────────────────────────────┘
                               │
┌─────────────────────────────────────────────────────────────┐
│                    MIDDLEWARE STACK                         │
│  • CORS Handling                                           │
│  • Authentication                                          │
│  • Authorization                                           │
│  • CSRF Protection                                         │
└─────────────────────────────────────────────────────────────┘
                               │
┌─────────────────────────────────────────────────────────────┐
│                    CONTROLLERS                              │
│  • Handle HTTP Requests                                    │
│  • Validate Input                                          │
│  • Call Services/Models                                    │
│  • Return Responses                                        │
└─────────────────────────────────────────────────────────────┘
                               │
┌─────────────────────────────────────────────────────────────┐
│                    SERVICES & MODELS                       │
│  • Business Logic                                          │
│  • Data Manipulation                                       │
│  • Database Operations                                     │
│  • Calculator Engines                                      │
└─────────────────────────────────────────────────────────────┘
                               │
┌─────────────────────────────────────────────────────────────┐
│                    VIEWS (TEMPLATES)                       │
│  • HTML Rendering                                          │
│  • Data Presentation                                       │
│  • Layout Management                                       │
└─────────────────────────────────────────────────────────────┘
```

