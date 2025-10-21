# VUE Inertia Book Management

This is a Laravel-based application for managing books. It provides features for creating, updating, and managing book records alias CRUD

## Features

- Add, edit, and delete books.
- Search and filter books.
- RESTful API for book management.

## Requirements

- PHP >= 8.0
- Composer
- Laravel >= 10.x
- MySQL or any supported database
- Vue 3 Composition API
- InertiaJS

# Features:

User Management:
Users can sign up (as Librarian or Customer), log in, and log out.

Featured Books:
Display random books with Title, Author, Description, Cover Image, and Average Rating.
Filter and sort by Title, Author, and Availability.

Book Details:
View full book information including Publisher, Publication Date, Category, ISBN, Page Count, and Customer Reviews.

Book Search:
Search books by partial title matches.

Book Management (Librarian only):
Add, edit, or delete books.

Book Checkout:
Customers can check out one copy of a book for 5 days.
Only librarians can mark books as returned.

Customer Reviews:
Customers can leave a short review and a 1–5 star rating.

And more.

## Installation

1. Clone the repository:
    ```bash
    git clone git@github.com:frederickgzmn/vueinertia-bookmanagement.git
    cd vueinertia-bookmanagement
    ```
    
2. Install dependencies:
    ```bash
    composer install or composer update
    ```

3. Copy the `.env.example` file to `.env` and configure your environment variables:
    ```bash
    cp .env.example .env
    ```

4. Generate the application key:
    ```bash
    php artisan key:generate
    ```

5. Generate the jwt key:
    ```bash
    php artisan jwt:secret
    ```

5. Run migrations and seed the database:
    ```bash
    php artisan migrate --seed
    ```

6. Run npm for the dependencies:
    ```bash
    npm install
    ```
7. Run start vue and inertia:
    ```bash
    npm run dev
    ```

8. Start the development server:
    ```bash
    php artisan serve
    ```

9. Enjoy!!..

## Database Structure
Tool use: https://dbdiagram.io/
DB diagram file: /db diagram.txt

![Database Structure](https://github.com/frederickgzmn/vueinertia-bookmanagement/blob/03031b13bd9d0b2b4d47806201566a2463bfefb0/db%20structure.png)

## Usage

- Access the application at `http://localhost:8000`.
- Use the provided API endpoints for programmatic access.

## License

This project is licensed under the [MIT License](LICENSE).
