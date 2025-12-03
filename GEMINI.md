# GEMINI.md

## Project Overview

This project is a comprehensive, custom-built PHP web application named "Bishwo Calculator". It follows the Model-View-Controller (MVC) architectural pattern and is built on a custom framework. The application provides a wide range of engineering calculators, user authentication, a detailed user profile and history section, and an extensive admin panel for site management.

**Key Technologies:**

*   **Backend:** PHP 7.4+
*   **Dependency Management:** Composer
*   **Routing:** `nikic/fast-route`
*   **Database:** MySQL
*   **Environment Configuration:** `vlucas/phpdotenv`
*   **Key Libraries:** The project utilizes a large number of PHP libraries for various functionalities, including mailing (`phpmailer/phpmailer`), image manipulation (`intervention/image`), PDF generation (`mpdf/mpdf`), and more.

**Architecture:**

*   **MVC:** The code is structured into `Models`, `Views`, and `Controllers` located in the `app/` directory.
*   **Front Controller:** The application uses a front controller pattern. All web requests are directed to `index.php`, which then boots the application via `public/index.php`. This ensures that the web server's document root can be set to the `/public` directory for enhanced security.
*   **Routing:** All application routes are defined in `app/routes.php`.
*   **Configuration:** Application and database configurations are stored in the `config/` directory.
*   **Core Logic:** The core framework components (like the main Controller, Model, Router, and Database handler) are located in `app/Core`.

## Building and Running

### 1. Prerequisites

*   PHP >= 7.4
*   MySQL Database
*   Composer
*   A web server (e.g., Apache, Nginx)

### 2. Installation and Setup

1.  **Clone the repository:**
    ```bash
    git clone <repository-url>
    cd Bishwo_Calculator
    ```

2.  **Install PHP dependencies:**
    ```bash
    composer install
    ```

3.  **Configure Environment:**
    *   Create a `.env` file by copying the example file:
        ```bash
        cp .env.example .env
        ```
    *   Open the `.env` file and update the database credentials (`DB_HOST`, `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD`) and the `APP_URL`.

4.  **Web Server Configuration:**
    *   Configure your web server (e.g., Apache Virtual Host) to use the `public/` directory as the document root.
    *   Ensure that URL rewriting is enabled to allow `index.php` to handle all incoming requests.

### 3. Running the Application

Once the setup is complete, you should be able to access the application in your web browser by navigating to the `APP_URL` you configured in the `.env` file.

### 4. Testing

**TODO:** The project contains a `/tests` directory, but the specific commands for running tests are not immediately apparent from the project files. A testing framework like PHPUnit is likely intended to be used, but its configuration and usage need to be documented.

## Development Conventions

*   **Code Style:** The codebase appears to follow a PSR-like standard, but this is not formally enforced by a linter configuration file. Developers should strive to maintain a consistent style with the existing code.
*   **Routing:** All new web routes should be added to `app/routes.php`. Routes are grouped by functionality (e.g., Public, Authentication, Admin).
*   **Database:** Database schema is managed through migration files, although the exact migration runner is not specified. New database changes should likely be implemented in new migration scripts.
*   **Modules & Plugins:** The application has a concept of "Modules" and "Plugins", with dedicated management sections in the admin panel. New, distinct features should likely be implemented as modules to maintain separation of concerns.
