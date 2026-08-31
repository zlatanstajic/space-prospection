# AGENTS.md

This file provides guidance to coding agents working in this repository.

## Overview

Space Prospection is a small CodeIgniter 3.1.13 / PHP 8.1 website about space exploration. It is server-rendered with plain CodeIgniter views, hand-written CSS, and jQuery from a CDN. The database is a SQLite file committed to the repository, so a fresh clone runs with no database server. There is no user authentication, no API, and no build step for the frontend.

## Commands

```bash
make check        # PHP, Composer, PHPUnit, SQLite versions plus a PHP extension check
make setup        # composer install, ci-phpunit-test scaffolding, project phpunit.xml
make serve        # PHP built-in server on localhost:8080 (override HOST and PORT)
make test         # Full gate: PHP_CodeSniffer, then PHPUnit
make test-phpcs   # PHP_CodeSniffer only
make test-phpunit # PHPUnit only
make fix          # phpcbf, for the violations PHP_CodeSniffer can fix
make coverage     # PHPUnit plus the path to the HTML coverage report
make clean        # Remove application/tests/build and generated logs
```

Make handles project setup and environment checks directly. Package-backed targets delegate to the matching script in `composer.json`; use `composer run <script>` for those where Make is unavailable. PHPUnit and PHP_CodeSniffer are vendored in `application/vendor/bin`, never global.

Run the smallest relevant test first:

```bash
application/vendor/bin/phpunit -c application/tests/ application/tests/models/Website_Model_Test.php
application/vendor/bin/phpunit -c application/tests/ --filter=test_navigation_method
```

## Architecture

Single controller, single model, no libraries or helpers of our own.

- `index.php` — CodeIgniter front controller. Holds the `ENVIRONMENT` switch, which also decides `error_reporting`.
- `application/config/routes.php` — `Website_Controller` is the default controller and `$route['(:any)']` sends every other segment to a method of the same name. Adding a page means adding a controller method and a view, not a route.
- `application/config/autoload.php` — the `database`, `email`, and `form_validation` libraries, the `url`, `form`, and `security` helpers, and `Website_Model` (as `$this->website_model`) are all autoloaded. Do not load them again inside a controller.
- `application/controllers/Website_Controller.php` — every page. The constructor fills `$this->data` with the navigation and social links that the header and footer need, so each page method loads `templates/header_view`, its own `pages/*_view`, and `templates/footer_view`.
- `application/models/Website_Model.php` — every database query, written as raw SQL through `$this->db->query(...)->result_array()`.
- `application/views/` — `templates/` for shared chrome, `pages/` for page bodies, `errors/` for framework error pages.
- `application/database/space-prospection.db` — the SQLite database, committed. Seed SQL for the `navigation`, `social_links`, and `projects` tables lives in `.extras/sql/`.
- `assets/` — CSS, JavaScript, fonts, and images, served directly.

## Conventions

- **Never edit `system/`.** It is an unmodified CodeIgniter 3.1.13 tree. Upgrading the framework means replacing the whole directory and merging the new `application/config/config.php` keys by hand.
- **PHP 8.1 is the target, not a floor.** `composer.json` pins the Composer platform to PHP 8.1, so the lock file stays installable on the target runtime even when the development machine runs something newer. CodeIgniter 3.1.13 is the last release of the 3.x line and supports PHP up to 8.1. On PHP 8.2 or newer the site still serves pages, but the framework raises deprecation notices (dynamic property creation, `E_STRICT`, implicit nullable parameters), and ci-phpunit-test escalates those into test errors, so `make test` fails there. Do not "fix" that by editing `system/`, by calling `disableStrictErrorCheck()`, or by loosening the assertions; run the suite on PHP 8.1 instead:

  ```bash
  docker run --rm -v "$PWD":/app -w /app php:8.1-cli \
  	php application/vendor/bin/phpunit -c application/tests/
  ```
- **Match the existing style,** which the PHP_CodeSniffer ruleset in `.extras/phpcs.xml` enforces: four-space indentation and no tabs, braces on their own line, uppercase `TRUE`, `FALSE`, and `NULL`, long `array()` syntax, `snake_case` methods, and a docblock on every class property and method. Run `make test-phpcs` before finishing.
- **Queries belong in `Website_Model`.** Controllers pass data to views; they do not talk to `$this->db`.
- **Views receive data, not logic.** Pass everything a view needs through the array given to `$this->load->view()`.
- **Escape output** and keep using `$this->input->post($key, TRUE)` for form input. `form_validation` rules are declared in the controller method that handles the submission.
- **Tests are PHPUnit classes** named `<Class>_Test.php` under `application/tests/controllers/` or `application/tests/models/`, to match the suffix configured in `application/tests/phpunit.xml`. Extend `UnitTestCase` when the test needs `newController()` or `newModel()`, and `TestCase` otherwise; both provide `request()`. `setUp()` needs the `: void` return type, and the PHPUnit 9 assertion names apply (`assertIsArray`, not `assertInternalType`).
- **Exercise controller methods through `request()`,** not by calling them directly on the instance. A direct call leaves the request superglobals unset, which makes the framework's input handling take null paths that raise deprecation notices unrelated to the code under test.
- **`application/tests/_ci_phpunit_test/` is generated** by `make setup` from the vendored package. Do not edit it; do not commit changes to it.
- **Never commit secrets.** `application/config/email.php` credentials and the `EMAIL_ADMIN` constant in `application/config/constants.php` are environment-specific, and `application/config/production/` is gitignored for per-server overrides.
- **The database file is a real artifact.** Changing schema or seed data means updating both `application/database/space-prospection.db` and the matching file in `.extras/sql/`.
