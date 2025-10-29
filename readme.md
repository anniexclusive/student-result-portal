# Student Result Portal

A modern Laravel 11 application for managing and accessing student examination results using PIN-based authentication. Students can securely check their results using a unique PIN and serial number combination.

## Features

- 🔐 **Secure PIN-based result access** - Results protected by unique PIN and serial number
- ♻️ **Reusable PINs** - PINs can be used up to 5 times for the same result
- 🏗️ **Modern Architecture** - Service layer, form requests, and proper separation of concerns
- ✅ **Comprehensive Testing** - Full test coverage with Pest PHP
- 🐳 **Docker Support** - Fully containerized development environment
- 🚀 **CI/CD Ready** - GitHub Actions workflow for automated testing and quality checks
- 📊 **Type Safety** - PHPStan/Larastan static analysis

## Tech Stack

- **Framework**: Laravel 11.x
- **PHP**: 8.3
- **Database**: MySQL 8.0
- **Cache/Queue**: Redis 7
- **Web Server**: Nginx (Alpine)
- **Testing**: Pest PHP
- **Code Quality**: Laravel Pint, PHPStan/Larastan

## Prerequisites

- Docker & Docker Compose
- Git

**OR** for local development:

- PHP 8.3+
- Composer
- MySQL 8.0+
- Redis 7+
- Node.js & NPM (for frontend assets)

## Quick Start with Docker

1. **Clone the repository**
   ```bash
   git clone <repository-url>
   cd student-result-portal
   ```

2. **Copy environment file**
   ```bash
   cp .env.example .env
   ```

3. **Start Docker containers**
   ```bash
   docker-compose up -d
   ```

4. **Install dependencies**
   ```bash
   docker-compose exec app composer install
   ```

5. **Generate application key**
   ```bash
   docker-compose exec app php artisan key:generate
   ```

6. **Run migrations and seed database**
   ```bash
   docker-compose exec app php artisan migrate --seed
   ```

7. **Access the application**
   - Open your browser and visit: `http://localhost:8000`

## Local Development Setup

1. **Install dependencies**
   ```bash
   composer install
   npm install
   ```

2. **Configure environment**
   ```bash
   cp .env.example .env
   php artisan key:generate
   ```

   Update `.env` with your local database credentials:
   ```env
   DB_CONNECTION=mysql
   DB_HOST=127.0.0.1
   DB_PORT=3306
   DB_DATABASE=student_portal
   DB_USERNAME=your_username
   DB_PASSWORD=your_password
   ```

3. **Run migrations**
   ```bash
   php artisan migrate --seed
   ```

4. **Start development server**
   ```bash
   php artisan serve
   ```

5. **Access the application**
   - Visit: `http://localhost:8000`

## Testing

The project uses Pest PHP for testing with comprehensive coverage.

### Run all tests
```bash
# With Docker
docker-compose exec app php artisan test

# Local
php artisan test
```

### Run with coverage
```bash
php artisan test --coverage
```

### Run specific test suites
```bash
# Feature tests only
php artisan test --testsuite=Feature

# Unit tests only
php artisan test --testsuite=Unit
```

## Code Quality

### Format code with Laravel Pint
```bash
# Check formatting
./vendor/bin/pint --test

# Fix formatting
./vendor/bin/pint
```

### Run static analysis
```bash
./vendor/bin/phpstan analyse
```

## Project Structure

```
app/
├── Http/
│   ├── Controllers/     # Application controllers
│   └── Requests/        # Form request validation classes
├── Models/              # Eloquent models
└── Services/            # Business logic layer
database/
├── factories/           # Model factories for testing
├── migrations/          # Database migrations
└── seeders/            # Database seeders
tests/
├── Feature/            # Feature/integration tests
└── Unit/               # Unit tests
```

## Key Components

### Models

- **Result**: Stores student examination results
- **Pin**: Manages PIN codes for result access
- **User**: Application users (for admin access)

### Services

- **ResultService**: Handles result retrieval and management
- **PinService**: Manages PIN validation and usage tracking

### Business Rules

- PINs can be used maximum 5 times
- Once used, a PIN is locked to a specific result
- The same PIN can be reused by the same student
- Different students cannot share PINs

## API Documentation

### Check Result

**Endpoint**: `POST /check`

**Parameters**:
- `pin` (string, required): PIN code
- `serial_number` (string, required): Serial number
- `reg_number` (string, required): Examination number

**Response**: Displays result page or returns error

## Environment Variables

Key environment variables (see `.env.example` for full list):

```env
APP_NAME="Student Result Portal"
APP_URL=http://localhost:8000
APP_PORT=8000

DB_CONNECTION=mysql
DB_HOST=mysql              # Use 'mysql' for Docker, '127.0.0.1' for local
DB_DATABASE=student_portal
DB_USERNAME=laravel
DB_PASSWORD=secret

CACHE_STORE=redis
SESSION_DRIVER=redis
QUEUE_CONNECTION=redis

REDIS_HOST=redis           # Use 'redis' for Docker, '127.0.0.1' for local
```

## Docker Commands

```bash
# Start containers
docker-compose up -d

# Stop containers
docker-compose down

# View logs
docker-compose logs -f

# Access app container shell
docker-compose exec app sh

# Run artisan commands
docker-compose exec app php artisan <command>

# Run composer commands
docker-compose exec app composer <command>
```

## CI/CD

The project includes GitHub Actions workflows for:

- ✅ Automated testing on push/PR
- 🔍 Code quality checks (Pint, PHPStan)
- 🔒 Security vulnerability scanning
- 📊 Code coverage reporting

## Contributing

1. Fork the repository
2. Create a feature branch (`git checkout -b feature/amazing-feature`)
3. Commit your changes (`git commit -m 'feat: Add amazing feature'`)
4. Push to the branch (`git push origin feature/amazing-feature`)
5. Open a Pull Request

### Commit Convention

This project follows [Conventional Commits](https://www.conventionalcommits.org/):

- `feat:` New features
- `fix:` Bug fixes
- `refactor:` Code refactoring
- `test:` Adding or updating tests
- `docs:` Documentation changes
- `chore:` Maintenance tasks

## Troubleshooting

### Docker containers won't start
```bash
# Check if ports are already in use
docker-compose down
docker-compose up -d
```

### Database connection issues
- Ensure MySQL container is running: `docker-compose ps`
- Check database credentials in `.env`
- Wait a few seconds for MySQL to fully start

### Permission issues
```bash
# Fix storage permissions
docker-compose exec app chmod -R 775 storage bootstrap/cache
docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
```

## License

This project is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).

## Support

For issues, questions, or contributions, please open an issue on GitHub.

---

**Note**: This is a modernized version of the application, upgraded from Laravel 5.4 to Laravel 11 with best practices and modern development tools.
