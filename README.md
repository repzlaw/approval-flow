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

6. Add $approvable property to your models to determine which fields would not require approval:
    ```sh
    protected $approvable = [
        'email',
    ];
    ```

7. Use the trait on your filament resources pages e.g UserResource/CreateUser.php:
    ```sh
    use ApprovalFlow;
    ```

8. Give approve permission to roles or users than can approve requests.

9. Add $approvable_related_column property to your models to determine which field alongside the ID would be shown as related record:
    ```sh
    protected $approvable_related_column = 'name';
    ```

## Usage
    Edit or Create a user and then approve or reject the request.
