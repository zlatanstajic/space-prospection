################################################################################
# Space Prospection
#
# Project tasks for building, serving and checking the website. Every target is
# safe to run repeatedly. Composer remains responsible for PHP dependencies and
# package-provided quality tools.
################################################################################

COMPOSER ?= composer
PHP      ?= php
HOST     ?= localhost
PORT     ?= 8080

.DEFAULT_GOAL := help

.PHONY: help check setup serve export-static test test-phpcs test-phpunit fix coverage clean

help: ## Show the available targets
	@echo "Space Prospection"
	@echo ""
	@grep -E '^[a-zA-Z_-]+:.*?## .*$$' $(MAKEFILE_LIST) \
		| awk 'BEGIN {FS = ":.*?## "}; {printf "  \033[36m%-14s\033[0m %s\n", $$1, $$2}'
	@echo ""
	@echo "Override the serve address with: make serve HOST=0.0.0.0 PORT=9000"

check: ## Verify that the machine has the required tooling
	@echo "PHP"
	@$(PHP) --version
	@echo ""
	@echo "Composer"
	@$(COMPOSER) --version
	@echo ""
	@echo "PHPUnit"
	@if test -x application/vendor/bin/phpunit; then \
		application/vendor/bin/phpunit --version; \
	else \
		echo "Not installed yet, run 'make setup' first"; \
	fi
	@echo ""
	@echo "SQLite"
	@if command -v sqlite3 >/dev/null 2>&1; then \
		sqlite3 --version; \
	else \
		echo "MISSING: install the sqlite3 command-line tool"; \
	fi
	@echo ""
	@echo "PHP SQLite extensions"
	@$(PHP) -r 'foreach (array("sqlite3", "pdo_sqlite") as $$extension) { echo $$extension, extension_loaded($$extension) ? " enabled\n" : " MISSING: enable it in php.ini\n"; }'

setup: ## Install dependencies and prepare the test suite
	@$(COMPOSER) install
	@$(PHP) application/vendor/kenjis/ci-phpunit-test/install.php
	@$(RM) application/tests/controllers/Welcome_test.php
	@cp .extras/phpunit.xml application/tests/phpunit.xml
	@echo "Project setup complete"

serve: ## Run the website locally (override with HOST and PORT)
	@echo "Space Prospection running on http://$(HOST):$(PORT)"
	@$(PHP) -S $(HOST):$(PORT)

export-static: ## Generate the read-only GitHub Pages site
	@$(PHP) .extras/export-static.php

test: ## Run the complete quality suite (PHP_CodeSniffer + PHPUnit)
	@$(COMPOSER) run test

test-phpcs: ## Run PHP_CodeSniffer only
	@$(COMPOSER) run test:phpcs

test-phpunit: ## Run PHPUnit only
	@$(COMPOSER) run test:phpunit

fix: ## Fix the coding standard violations PHP_CodeSniffer can fix
	@$(COMPOSER) run fix

coverage: test-phpunit ## Run PHPUnit and print the coverage report location
	@echo "Coverage report: application/tests/build/coverage/index.html"

clean: ## Remove generated reports, static output, and logs
	@rm -rf application/tests/build
	@rm -rf application/logs/log-*.php
	@rm -rf .build
	@echo "Removed generated reports, static output, and logs"
