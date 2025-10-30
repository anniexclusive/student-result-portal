.PHONY: help build up down restart logs shell install migrate seed test format analyse clean
.DEFAULT_GOAL := help

# Colors
BLUE := \033[0;34m
GREEN := \033[0;32m
YELLOW := \033[0;33m
NC := \033[0m

## —— Student Result Portal Makefile ————————————————————————————————————
help: ## Show this help message
	@echo "$(BLUE)Student Result Portal - Available Commands$(NC)"
	@echo ""
	@grep -E '(^[a-zA-Z_-]+:.*?##.*$$)|(^##)' $(MAKEFILE_LIST) | awk 'BEGIN {FS = ":.*?## "}; {printf "$(GREEN)%-20s$(NC) %s\n", $$1, $$2}' | sed -e 's/\[32m##/[33m/'
	@echo ""

## —— Docker ————————————————————————————————————————————————————————————
build: ## Build Docker containers
	@echo "$(BLUE)Building containers...$(NC)"
	docker-compose build

up: ## Start Docker containers
	@echo "$(BLUE)Starting containers...$(NC)"
	docker-compose up -d
	@echo "$(GREEN)✓ Containers started → http://localhost:8000$(NC)"

down: ## Stop Docker containers
	@echo "$(BLUE)Stopping containers...$(NC)"
	docker-compose down

restart: down up ## Restart Docker containers

logs: ## Show container logs
	docker-compose logs -f

shell: ## Access app container shell
	docker-compose exec app sh

ps: ## Show running containers
	docker-compose ps

## —— Setup ——————————————————————————————————————————————————————————————
setup: build up install key migrate seed ## Complete project setup
	@echo "$(GREEN)✓ Setup complete → http://localhost:8000$(NC)"

install: ## Install dependencies
	@echo "$(BLUE)Installing dependencies...$(NC)"
	docker-compose exec app composer install

key: ## Generate application key
	docker-compose exec app php artisan key:generate

env: ## Copy .env.example to .env
	cp .env.example .env
	@echo "$(GREEN)✓ .env file created$(NC)"

## —— Database ——————————————————————————————————————————————————————————
migrate: ## Run database migrations
	docker-compose exec app php artisan migrate

seed: ## Seed the database
	docker-compose exec app php artisan db:seed

fresh: ## Drop tables, migrate, and seed
	@echo "$(YELLOW)⚠ This will drop all tables!$(NC)"
	docker-compose exec app php artisan migrate:fresh --seed

## —— Testing ———————————————————————————————————————————————————————————
test: ## Run all tests
	docker-compose exec app php artisan test

test-unit: ## Run unit tests only
	docker-compose exec app php artisan test --testsuite=Unit

test-feature: ## Run feature tests only
	docker-compose exec app php artisan test --testsuite=Feature

test-coverage: ## Run tests with coverage (70% min)
	docker-compose exec app php artisan test --coverage --min=70

## —— Code Quality ——————————————————————————————————————————————————————
format: ## Format code with Pint
	docker-compose exec app ./vendor/bin/pint

format-test: ## Check code formatting
	docker-compose exec app ./vendor/bin/pint --test

analyse: ## Run PHPStan analysis
	docker-compose exec app ./vendor/bin/phpstan analyse

check: format-test analyse test ## Run all quality checks

## —— Artisan ———————————————————————————————————————————————————————————
cache-clear: ## Clear all caches
	docker-compose exec app php artisan optimize:clear

routes: ## Show application routes
	docker-compose exec app php artisan route:list

## —— Cleanup ———————————————————————————————————————————————————————————
clean: down ## Stop containers and remove volumes
	docker-compose down -v
	@echo "$(GREEN)✓ Cleanup complete$(NC)"

permissions: ## Fix storage permissions
	docker-compose exec app chmod -R 775 storage bootstrap/cache
	docker-compose exec app chown -R www-data:www-data storage bootstrap/cache

## —— Production —————————————————————————————————————————————————————————
prod-optimize: ## Optimize for production
	docker-compose exec app composer install --optimize-autoloader --no-dev
	docker-compose exec app php artisan optimize
	@echo "$(GREEN)✓ Production optimized$(NC)"
