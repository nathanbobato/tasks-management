# Task Manager

A task management system built with Laravel 10 and Vue.js 3.

## Features

- **Task Management**
  - Create, read, update, and delete tasks
  - Real-time updates across all clients
  - Task filtering by status (pending, in progress, completed)
  - Pagination with 10 items per page
  - Responsive design with modern UI

- **User Authentication**
  - Secure login and registration
  - Password reset functionality
  - Remember me feature
  - Profile management
  - Password update

- **Testing**
  - Comprehensive test suite using Pest
  - Unit tests for Task model
  - Feature tests for TaskController
  - Authentication tests

## Requirements

- PHP 8.2
- PostgreSQL 15
- Node.js 20+
- Composer
- Docker (Recommended)

## Installation

### Using Docker (Recommended)

1. Clone the repository:
```bash
git clone https://github.com/nathanbobato/tasks-management.git
```

Go to the folder with the code:
```
cd tasks-management
```

2. Copy the environment file:
```bash
cp .env.example .env
```

3. Run docker build:
```bash
docker compose build --no-cache
```

4. Start the containers:
```bash
docker-compose up -d
```

5. Install dependencies:
```bash
docker-compose exec app composer install
docker-compose exec app npm install
```

6. Update the database configuration in .env:
```
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=task_manager
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

7. Generate application key:
```bash
docker-compose exec app php artisan key:generate
```

8. Run migrations and seeders:
```bash
docker-compose exec app php artisan migrate --seed
```

9. If you are using Linux run:
```bash
chmod -R 777 .
```

The application will be available at http://localhost:8000

### Manual Installation

1. Clone the repository:
```bash
git clone https://github.com/yourusername/task-manager.git
cd task-manager
```

2. Install PHP dependencies:
```bash
composer install
```

3. Install Node.js dependencies:
```bash
npm install
```

4. Copy the environment file:
```bash
cp .env.example .env
```

5. Update the database configuration in .env:
```env
DB_CONNECTION=pgsql
DB_HOST=127.0.0.1
DB_PORT=5432
DB_DATABASE=task_manager
DB_USERNAME=postgres
DB_PASSWORD=postgres
```

6. Generate application key:
```bash
php artisan key:generate
```

7. Run migrations and seeders:
```bash
php artisan migrate --seed
```

8. Build assets:
```bash
npm run build
```

9. Start the development server:
```bash
php artisan serve
```

The application will be available at http://localhost:8000

## Testing

The application includes a comprehensive test suite using Pest. Run the tests using:

```bash
php artisan test
```

The test suite includes:
- Unit tests for the Task model
- Feature tests for the TaskController
- Authentication tests
- Form validation tests

## License

This project is licensed under the MIT License - see the [LICENSE](LICENSE) file for details.