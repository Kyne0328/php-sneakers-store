# Sneakers Store Web Application

This is a responsive PHP-based web application for an online sneakers store. It features user authentication, product browsing, a shopping cart, checkout, order management, and an admin dashboard for managing products, orders, and users.

## Features

-   **User Authentication:**
    -   Registration with email validation and secure password hashing.
    -   Login with session management and CSRF protection.
    -   Logout functionality.
    -   Password strength validation.

-   **Product Browsing:**
    -   Homepage with featured products.
    -   Product listing page with search functionality.
    -   Product details page with available sizes and stock information.
    -   Related products suggestions.

-   **Shopping Cart:**
    -   Add products to the cart (requires login).
    -   Update product quantities in the cart.
    -   Remove products from the cart.
    -   Cart persistence using sessions and database synchronization for logged-in users.
    -   Cart quantity limit.

-   **Checkout:**
    -   Multi-step checkout process.
    -   Address management (saving multiple addresses).
    -   Shipping fee calculation (free shipping over a certain amount).
    -   Order processing with stock management.
    -   Order confirmation.

-   **User Profile:**
    -   Profile information update (name, email, password).
    -   Order history with detailed order information (via AJAX loading).

-   **Admin Dashboard:**
    -   Admin login with role-based access control.
    -   Dashboard with key statistics (total orders, revenue, products, users, low stock items).
    -   Product management (add, edit, delete).
    -   Order management (view, update status, search).
    -   User management (view, search, toggle admin status).

-   **Error Handling:**
    -   Detailed error logging.
    -   User-friendly error messages.
    -   Robust validation and sanitization of user inputs.
    -   Exception handling with database transaction management.

-   **Security:**
    -   Password hashing (bcrypt).
    -   CSRF protection.
    -   Input sanitization (using `htmlspecialchars` and filters).
    -   Session management (including session regeneration).
    -   Role-based access control for admin features.
    -   Prepared statements to prevent SQL injection.

- **Other Features:**
    - use of XML for order and sidebar configuration
    - use of a .env file

## Technologies Used

-   **PHP:**  Server-side scripting language.
-   **MySQL:**  Database for storing product, user, and order information.
-   **HTML/CSS/JavaScript:** Front-end development.
-   **Bootstrap:** CSS framework for responsive design.
-   **Composer:** Dependency management for PHP.
-   **Bramus/Router:**  A lightweight PHP router.
-   **vlucas/phpdotenv:** For managing environment variables.
-   **PDO:**  For database interactions.

## Setup Instructions

1.  **Clone the repository:**

    ```bash
    git clone <repository_url>
    cd <repository_directory>
    ```

2.  **Install dependencies:**

    ```bash
    composer install
    ```

3.  **Configure environment variables:**

    -   Create a `.env` file in the root directory by copying the `.env.example` file:
        ```bash
        cp .env.example .env
        ```
    -   Edit the `.env` file and set your database credentials and application URL:
        ```
        DB_HOST=your_database_host
        DB_NAME=your_database_name
        DB_USER=your_database_username
        DB_PASS=your_database_password
        APP_URL=your_application_url
        APP_ENV=development  # Change to 'production' in a production environment
        ```

4.  **Import the database:**

    -   Create a database named `sneakers_store` (or the name you specified in `.env`).
    -   Import the `database.sql` file into your MySQL database:
        ```bash
        mysql -u your_database_username -p your_database_name < database.sql
        ```

5.  **Configure your web server:**

    -   Point your web server's document root to the `public` directory.  The `.htaccess` file is provided for Apache, and contains the necessary rewrite rules to direct all requests to `public/index.php`.  If you're using a different web server (e.g., Nginx), you'll need to configure the appropriate rewrite rules.

6.  **Access the application:**

    -   Open your web browser and navigate to the `APP_URL` you set in your `.env` file (e.g., `http://localhost/php-sneakers-store/public`).

## Important Notes

-   **Error Logging:** The application logs database errors and other important events.  Check your web server's error logs for debugging information.
-   **Security:**  For production environments, ensure that:
    -   The `.env` file is not publicly accessible.
    -   `APP_ENV` is set to `production` in your `.env` file.
    -   You use HTTPS.
-   **Image Placeholders:** The initial database includes placeholder image URLs (`https://placehold.co/300x200`).  Replace these with your actual product images.

## Project Structure
This follows a typical MVC structure.

-   **.env.example:** Example environment variable configuration.
-   **.gitignore:** Specifies intentionally untracked files that Git should ignore.
-   **app/:** Contains the application's core code.
    -   **Config/:** Configuration files, including database connection settings.
    -   **Controllers/:** Controllers handle user requests and interact with models.
        - **Admin/:** Controllers for admin-specific functionality.
    -   **Models/:** Models represent data and interact with the database.
    -   **Views/:** View templates for rendering HTML.
-   **composer.json:**  Defines project dependencies.
-   **composer.lock:**  Locks dependency versions.
-   **database.sql:** SQL file for creating the database schema and initial data.
-   **database/:** directory containing database related file
    -   **schema.sql:** contains the database schema
-   **images/:** Contains images used in the application (e.g., logo).
-   **public/:** The web server's document root.
    -   **css/:**  CSS files.
    -   **index.php:** The main entry point for the application.
    -   **.htaccess:** Apache configuration file for URL rewriting.
