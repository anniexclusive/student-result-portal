# Student Result Portal

A secure Laravel 11 application for managing and accessing student examination results. Students can check their results using PIN-based authentication after logging in.

## Features

- 🔐 **User Authentication** - Register/login required before accessing results
- 📌 **PIN-Based Access** - Results protected by unique PIN and serial number
- ♻️ **Reusable PINs** - Each PIN can be used up to 5 times for the same result
- 🛡️ **Enterprise Security** - SQL injection prevention, XSS protection, rate limiting, CSRF protection
- ⚡ **Redis Caching** - 30-minute cache TTL for optimized performance
- ✅ **94.4% Test Coverage** - 128 tests covering security, boundaries, and error handling
- 🐳 **Docker Ready** - Fully containerized development environment
- 🚀 **CI/CD Pipeline** - Automated testing, code quality, and security checks

## Tech Stack

- **Laravel** 11.x | **PHP** 8.3 | **MySQL** 8.0 | **Redis** 7
- **Testing**: Pest PHP | **Quality**: Laravel Pint, PHPStan
- **Server**: Nginx Alpine | **Container**: Docker Compose

## Quick Start

```bash
# 1. Clone and setup environment
git clone <repository-url>
cd student-result-portal
make env  # Creates .env file

# 2. Complete setup (builds containers, installs deps, migrates, seeds)
make setup

# Application now running at http://localhost:8000
```

**Default Users:**
- `student@example.com` / `password`
- `test@example.com` / `password123`

## Common Commands

```bash
# Docker Management
make up              # Start containers
make down            # Stop containers
make restart         # Restart containers
make logs            # View logs
make shell           # Access container shell
make ps              # Show running containers

# Database
make migrate         # Run migrations
make seed            # Seed database
make fresh           # Drop tables, migrate, and seed (⚠️  destructive)

# Testing
make test            # Run all tests
make test-unit       # Run unit tests
make test-feature    # Run feature tests
make test-coverage   # Run with coverage report (70% min)

# Code Quality
make format          # Format code with Pint
make format-test     # Check formatting
make analyse         # Run PHPStan static analysis
make check           # Run all quality checks (format + analyse + test)

# Utilities
make routes          # List all routes
make cache-clear     # Clear all caches
make permissions     # Fix storage permissions
make clean           # Stop containers and remove volumes

# Production
make prod-optimize   # Optimize for production

# Help
make help            # Show all available commands
```

## Manual Docker Commands

If you prefer Docker Compose directly:

```bash
# Start/Stop
docker-compose up -d
docker-compose down

# Run commands
docker-compose exec app php artisan test
docker-compose exec app composer install
docker-compose exec app php artisan migrate
```

## Testing

```bash
# Run all 128 tests
make test

# Run specific test suites
make test-unit       # Unit tests only
make test-feature    # Integration tests only

# Generate coverage report
make test-coverage   # Requires 70% minimum
```

**Test Coverage Breakdown:**
- **Total**: 94.4% (128 tests, 233 assertions)
- **Security**: 100% (SQL injection, XSS, CSRF, mass assignment)
- **Business Logic**: 100% (PIN validation, caching, error handling)
- **Boundary Conditions**: 90%+ (count limits, scores, dates, strings)

## Code Quality

```bash
# Auto-format code
make format

# Check formatting without changes
make format-test

# Static analysis
make analyse

# Run all checks (CI pipeline equivalent)
make check
```

## Architecture

### Core Models
- **User** - Application users with authentication
- **Result** - Student examination results
- **Pin** - PIN codes with usage tracking and expiration

### Services
- **ResultService** - Result retrieval with Redis caching (30min TTL)
- **PinService** - PIN validation, usage limits, and result association

### Security Features
- SQL injection prevention (parameterized queries)
- XSS protection (output escaping)
- Rate limiting (5 requests/minute on result checks)
- CSRF token validation
- Password hashing (bcrypt)
- Session regeneration on login

### Business Rules
- Users must be authenticated to check results
- PINs have maximum 5 uses
- Once used, PIN is locked to specific result
- Same student can reuse their PIN
- PIN sharing between students blocked

## API Endpoints

### Authentication
- `GET /register` - Registration form
- `POST /register` - Create new user
- `GET /login` - Login form
- `POST /login` - Authenticate user
- `POST /logout` - End session

### Result Checking
- `GET /` - Check result form (requires auth)
- `POST /check` - Validate and display result (rate limited)

**Parameters:**
- `pin` - PIN code (required)
- `serial_number` - Serial number (required)
- `reg_number` - Examination number (required)

## Environment Configuration

```env
# Application
APP_NAME="Student Result Portal"
APP_URL=http://localhost:8000
APP_PORT=8000

# Database (Docker)
DB_HOST=mysql
DB_DATABASE=student_portal
DB_USERNAME=laravel
DB_PASSWORD=secret

# Cache & Sessions
CACHE_STORE=redis
SESSION_DRIVER=redis
REDIS_HOST=redis
```

## CI/CD

GitHub Actions workflows include:
- ✅ Automated testing (all 128 tests)
- 🔍 Code quality (Pint, PHPStan)
- 🔒 Security scanning
- 📊 Coverage validation (70% minimum)
- 📈 Job summaries with metrics

## Performance Optimizations

- **Caching**: Redis cache for result queries (30-minute TTL)
- **Database Indexes**: Composite indexes on frequently queried columns
- **OPcache**: Configured for production (256MB, 20K files)
- **Docker Volumes**: Delegated mode for 30-50% faster I/O
- **Query Optimization**: Eager loading to prevent N+1 queries

## Troubleshooting

```bash
# Containers won't start
make down
make up

# Permission issues
make permissions

# Clear all caches
make cache-clear

# View logs
make logs

# Fresh start (⚠️  drops all data)
make clean
make setup
```

## Project History

Originally built with Laravel 5.4, fully modernized to Laravel 11 with:
- Modern authentication system (Laravel Breeze-style)
- Service layer architecture
- Comprehensive security hardening
- Enterprise-grade test coverage
- Production-ready infrastructure

## Author
Anne Iheanacho

## License

MIT License - Open source software

---

**Need Help?** Open an issue on GitHub
