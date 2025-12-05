# GEMINI.md - Bishwo Calculator

## Project Overview

Bishwo Calculator is a web-based calculator application built with PHP. It features a modular design that allows for the easy addition of new calculators. The application has a user authentication system, a dashboard for registered users, and a comprehensive admin panel for managing the application. The application follows an MVC (Model-View-Controller) architecture.

**Key Technologies:**

*   **Backend:** PHP
*   **Dependency Management:** Composer
*   **Routing:** Custom router based on `nikic/fast-route`
*   **Database:** Not explicitly defined, but likely a relational database like MySQL.
*   **Frontend:** Not explicitly defined, but likely a mix of HTML, CSS, and JavaScript.

**Architecture:**

*   **Front Controller:** All requests are routed through `public/index.php`.
*   **MVC:** The application is structured into Models, Views, and Controllers.
*   **Modular:** Calculators are organized into modules, making the application extensible.
*   **Services:** The application uses services to encapsulate business logic (e.g., `CalculationService`, `PluginManager`).
*   **Factory:** A `CalculatorFactory` is used to create calculator instances.

## Building and Running

### Prerequisites

*   PHP >= 7.4
*   Composer

### Installation

1.  Clone the repository.
2.  Install the dependencies using Composer:

    ```bash
    composer install
    ```

3.  Create a `.env` file from the `.env.example` and configure the database and other settings.
4.  Set up the database:

    ```bash
    php database/setup_db.php
    ```

### Running the Application

The application can be run using a local development server like Laragon or XAMPP. The document root should be set to the `public` directory.

### Testing

The project includes a `testsprite_tests` directory with Python-based API tests. The `API_TEST_PLAN.md` file provides a comprehensive overview of the testing strategy. To run the tests, you will need to have Python and the necessary libraries installed.

## Development Conventions

*   **PSR-4 Autoloading:** The application uses PSR-4 autoloading for the `App` namespace, with the `app/` directory as the root.
*   **Modular Development:** New calculators should be created as modules in the `modules/` directory.
*   **API-driven:** The application has a strong focus on its API, with a versioned API and a detailed API test plan.
*   **Admin Panel:** A comprehensive admin panel is available for managing the application.

## Key Files

*   `composer.json`: Defines the project dependencies.
*   `index.php`: The main application entry point.
*   `public/index.php`: The front controller.
*   `app/bootstrap.php`: Initializes the application.
*   `app/routes.php`: Defines the application routes.
*   `app/Controllers/`: Contains the application controllers.
*   `app/Calculators/`: Contains the core calculator logic.
*   `modules/`: Contains the calculator modules.
*   `API_TEST_PLAN.md`: Provides a detailed overview of the API and testing strategy.
