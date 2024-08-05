# Approval Flow

This is a Laravel-based project that implements an approval workflow system using Filament v3. The project allows for dynamic model handling and custom actions during CRUD operations.

## Features

- **Dynamic Model Handling**: Interact with different models dynamically based on the `approvable_type` field.
- **Custom CRUD Hooks**: Implement custom logic during create, update, and delete operations.
- **Pretty JSON Display**: Render JSON fields in a human-readable format on the frontend.

## Installation

1. Clone the repository:
    ```sh
    git clone https://github.com/repzlaw/approval-flow.git
    cd approval-flow
    ```

2. Install dependencies:
    ```sh
    composer install
    npm install
    ```

3. Set up environment variables:
    ```sh
    cp .env.example .env
    php artisan key:generate
    ```

4. Run migrations:
    ```sh
    php artisan migrate
    ```

5. Serve the application:
    ```sh
    php artisan serve
    ```

## Usage
    Edit or Create a user and then approve or reject the request.
