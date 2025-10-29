.PHONY: help build up down restart logs shell install migrate seed fresh test test-coverage format analyse clean

# Default target
.DEFAULT_GOAL := help

# Colors for output
BLUE := \033[0;34m
GREEN := \033[0;32m
YELLOW := \033[0;33m
RED := \033[0;31m
NC := \033[0m # No Color

## —— Student Result Portal Makefile ————————————————————————————————————
help: ## Show this help message
	@echo "$(BLUE)Student Result Portal - Available Commands$(NC)"
	@echo ""
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "$(GREEN)%-20s$(NC) %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
	@echo ""

## —— Docker ————————————————————————————————————————————————————————————
build: ## Build Docker containers
	@echo "$(BLUE)Building Docker containers...$(NC)"
	docker-compose build

up: ## Start Docker containers
	@echo "$(BLUE)Starting Docker containers...$(NC)"
	docker-compose up -d
	@echo "$(GREEN)✓ Containers started successfully$(NC)"
	@echo "$(YELLOW)Access the app at: http://localhost:8000$(NC)"

down: ## Stop Docker containers
	@echo "$(BLUE)Stopping Docker containers...$(NC)"
	docker-compose down
	@echo "$(GREEN)✓ Containers stopped$(NC)"

restart: down up ## Restart Docker containers

logs: ## Show Docker logs
	docker-compose logs -f

logs-app: ## Show app container logs
	docker-compose logs -f app

logs-mysql: ## Show MySQL container logs
	docker-compose logs -f mysql

shell: ## Access app container shell
	docker-compose exec app sh

mysql: ## Access MySQL CLI
	docker-compose exec mysql mysql -u laravel -psecret student_portal

redis-cli: ## Access Redis CLI
	docker-compose exec redis redis-cli

ps: ## Show running containers
	docker-compose ps

## —— Setup & Installation —————————————————————————————————————————————
setup: build up install key migrate seed ## Complete project setup
	@echo "$(GREEN)✓ Setup complete!$(NC)"
	@echo "$(YELLOW)Visit: http://localhost:8000$(NC)"

install: ## Install Composer dependencies
	@echo "$(BLUE)Installing Composer dependencies...$(NC)"
	docker-compose exec app composer install
	@echo "$(GREEN)✓ Dependencies installed$(NC)"

key: ## Generate application key
	@echo "$(BLUE)Generating application key...$(NC)"
	docker-compose exec app php artisan key:generate
	@echo "$(GREEN)✓ Application key generated$(NC)"

env: ## Copy .env.example to .env
	@echo "$(BLUE)Creating .env file...$(NC)"
	cp .env.example .env
	@echo "$(GREEN)✓ .env file created$(NC)"

## —— Database ——————————————————————————————————————————————————————————
migrate: ## Run database migrations
	@echo "$(BLUE)Running migrations...$(NC)"
	docker-compose exec app php artisan migrate
	@echo "$(GREEN)✓ Migrations completed$(NC)"

migrate-fresh: ## Drop all tables and re-run migrations
	@echo "$(YELLOW)Warning: This will drop all tables!$(NC)"
	docker-compose exec app php artisan migrate:fresh
	@echo "$(GREEN)✓ Fresh migrations completed$(NC)"

seed: ## Seed the database
	@echo "$(BLUE)Seeding database...$(NC)"
	docker-compose exec app php artisan db:seed
	@echo "$(GREEN)✓ Database seeded$(NC)"

fresh: migrate-fresh seed ## Fresh migrations with seed

rollback: ## Rollback last migration
	@echo "$(BLUE)Rolling back last migration...$(NC)"
	docker-compose exec app php artisan migrate:rollback
	@echo "$(GREEN)✓ Rollback completed$(NC)"

## —— Testing ———————————————————————————————————————————————————————————
test: ## Run all tests
	@echo "$(BLUE)Running tests...$(NC)"
	docker-compose exec app php artisan test

test-unit: ## Run unit tests only
	@echo "$(BLUE)Running unit tests...$(NC)"
	docker-compose exec app php artisan test --testsuite=Unit

test-feature: ## Run feature tests only
	@echo "$(BLUE)Running feature tests...$(NC)"
	docker-compose exec app php artisan test --testsuite=Feature

test-coverage: ## Run tests with coverage
	@echo "$(BLUE)Running tests with coverage...$(NC)"
	docker-compose exec app php artisan test --coverage --min=80

test-parallel: ## Run tests in parallel
	@echo "$(BLUE)Running tests in parallel...$(NC)"
	docker-compose exec app php artisan test --parallel

## —— Code Quality ——————————————————————————————————————————————————————
format: ## Format code with Laravel Pint
	@echo "$(BLUE)Formatting code...$(NC)"
	docker-compose exec app ./vendor/bin/pint
	@echo "$(GREEN)✓ Code formatted$(NC)"

format-test: ## Check code formatting
	@echo "$(BLUE)Checking code formatting...$(NC)"
	docker-compose exec app ./vendor/bin/pint --test

analyse: ## Run static analysis with PHPStan
	@echo "$(BLUE)Running static analysis...$(NC)"
	docker-compose exec app ./vendor/bin/phpstan analyse

check: format-test analyse test ## Run all quality checks

## —— Artisan Commands —————————————————————————————————————————————————
cache-clear: ## Clear application cache
	@echo "$(BLUE)Clearing cache...$(NC)"
	docker-compose exec app php artisan cache:clear
	docker-compose exec app php artisan config:clear
	docker-compose exec app php artisan route:clear
	docker-compose exec app php artisan view:clear
	@echo "$(GREEN)✓ Cache cleared$(NC)"

optimize: ## Optimize the application
	@echo "$(BLUE)Optimizing application...$(NC)"
	docker-compose exec app php artisan optimize
	@echo "$(GREEN)✓ Application optimized$(NC)"

queue-work: ## Start queue worker
	docker-compose exec app php artisan queue:work

tinker: ## Open Laravel Tinker
	docker-compose exec app php artisan tinker

routes: ## Show application routes
	docker-compose exec app php artisan route:list

## —— Git ———————————————————————————————————————————————————————————————
status: ## Show git status
	@git status

push: ## Push to current branch
	@git push origin $$(git branch --show-current)

pull: ## Pull from current branch
	@git pull origin $$(git branch --show-current)

## —— Cleanup & Maintenance ————————————————————————————————————————————
clean: ## Remove vendor and node_modules
	@echo "$(BLUE)Cleaning up...$(NC)"
	rm -rf vendor node_modules
	@echo "$(GREEN)✓ Cleanup completed$(NC)"

clean-all: down clean ## Stop containers and clean everything
	@echo "$(BLUE)Removing Docker volumes...$(NC)"
	docker-compose down -v
	@echo "$(GREEN)✓ Complete cleanup done$(NC)"

permissions: ## Fix storage and cache permissions
	@echo "$(BLUE)Fixing permissions...$(NC)"
	docker-compose exec app chmod -R 775 storage bootstrap/cache
	docker-compose exec app chown -R www-data:www-data storage bootstrap/cache
	@echo "$(GREEN)✓ Permissions fixed$(NC)"

## —— Local Development (without Docker) ———————————————————————————————
local-install: ## Install dependencies locally
	composer install
	npm install

local-serve: ## Start local development server
	php artisan serve

local-test: ## Run tests locally
	php artisan test

local-migrate: ## Run migrations locally
	php artisan migrate

local-seed: ## Seed database locally
	php artisan db:seed

## —— Production ————————————————————————————————————————————————————————
prod-optimize: ## Optimize for production
	@echo "$(BLUE)Optimizing for production...$(NC)"
	docker-compose exec app composer install --optimize-autoloader --no-dev
	docker-compose exec app php artisan config:cache
	docker-compose exec app php artisan route:cache
	docker-compose exec app php artisan view:cache
	@echo "$(GREEN)✓ Production optimization complete$(NC)"

prod-build: ## Build production Docker image
	docker-compose -f docker-compose.prod.yml build

## —— Monitoring ————————————————————————————————————————————————————————
health: ## Check application health
	@echo "$(BLUE)Checking application health...$(NC)"
	@curl -s http://localhost:8000 > /dev/null && echo "$(GREEN)✓ Application is running$(NC)" || echo "$(RED)✗ Application is not responding$(NC)"
	@docker-compose ps

stats: ## Show container stats
	docker stats --no-stream
