# Space Prospection

[![License: MIT](https://img.shields.io/badge/License-MIT-green.svg)](LICENSE.md)
[![PHP 8.1](https://img.shields.io/badge/PHP-8.1-blue.svg)](https://www.php.net/)
[![CodeIgniter 3.1.13](https://img.shields.io/badge/CodeIgniter-3.1.13-red.svg)](https://codeigniter.com/)
[![SQLite 3](https://img.shields.io/badge/SQLite-3-lightblue.svg)](https://www.sqlite.org/)

> Explore space from your browser.

A small [CodeIgniter](https://codeigniter.com/) website about space exploration and the search for extraterrestrial life. The content and purpose are made up; the project exists to demonstrate a complete MVC application and to help other web developers learn the framework.

After GitHub Pages is enabled, the included workflow publishes a read-only visual demo at [zlatanstajic.github.io/space-prospection](https://zlatanstajic.github.io/space-prospection/). The screenshot below is also kept for quick comparison with your own installation.

<img src=".extras/screenshots/homepage.jpg?clear_cache=1" alt="Homepage of the Space Prospection website" width="100%">

## Table of Contents

- [Features](#features)
- [Tech Stack](#tech-stack)
- [Install](#install)
  - [Requirements](#requirements)
  - [Local Setup](#local-setup)
  - [GitHub Pages Demo](#github-pages-demo)
  - [Server Installation](#server-installation)
- [Make Commands](#make-commands)
- [Project Structure](#project-structure)
- [Testing](#testing)
- [Contributing](#contributing)
- [License](#license)

---

## Features

- **Database-driven navigation:** Build the main menu and social links from the `navigation` and `social_links` tables, ordered and toggled per row.
- **Featured projects:** Show the five most recent active rows from the `projects` table on the projects page.
- **Contact form:** Validate a visitor message server-side and email it to the configured administrator address.
- **Static content pages:** Serve the home, about, projects, and contact pages through a single controller.
- **Clean URLs:** Route every request through a catch-all rule so pages resolve without `index.php`.
- **Responsive interface:** Load a separate mobile stylesheet and mobile image set alongside the desktop layout.
- **Zero-configuration database:** Ship the SQLite file inside the repository, so a fresh clone runs without a database server.

[⬆ back to top](#table-of-contents)

---

## Tech Stack

- **Backend:** PHP 8.1 and CodeIgniter 3.1.13
- **Frontend:** CodeIgniter views, hand-written CSS, and jQuery from a CDN
- **Database:** SQLite 3, stored at [`application/database/space-prospection.db`](application/database/space-prospection.db)
- **Testing:** PHPUnit 9 through [ci-phpunit-test](https://github.com/kenjis/ci-phpunit-test), with an HTML coverage report
- **Quality:** PHP_CodeSniffer against the ruleset in [`.extras/phpcs.xml`](.extras/phpcs.xml)

[⬆ back to top](#table-of-contents)

---

## Install

### Requirements

- PHP 8.1 with the `mbstring`, `sqlite3`, and `pdo_sqlite` extensions enabled in `php.ini`. PHP 8.1 is the newest version CodeIgniter 3.1.13 supports; see [Testing](#testing) for what changes on PHP 8.2 and newer.
- Composer 2
- The `sqlite3` command-line tool
- GNU Make, for the shortcuts described in [Make Commands](#make-commands)

[TablePlus](https://tableplus.com/) is recommended for browsing and editing the database file.

### Local Setup

Clone the repository, verify the machine, and install the dependencies:

```bash
git clone https://github.com/zlatanstajic/space-prospection.git
cd space-prospection
make check
make setup
```

`make check` prints the PHP, Composer, PHPUnit, and SQLite versions and warns when a required PHP extension is missing. `make setup` installs the Composer dependencies into `application/vendor`, installs the ci-phpunit-test scaffolding into [`application/tests`](application/tests), and copies the project PHPUnit configuration into place.

Start the website with:

```bash
make serve
```

The website is available at `http://localhost:8080`. Override the address when the port is taken:

```bash
make serve HOST=0.0.0.0 PORT=9000
```

### GitHub Pages Demo

The repository includes a GitHub Actions workflow that renders the public CodeIgniter routes and publishes the generated HTML and assets to GitHub Pages. The demo is intended to be available at `https://zlatanstajic.github.io/space-prospection/` after GitHub Actions is selected once under **Settings → Pages → Build and deployment → Source**.

Generate the same read-only site locally with:

```bash
make export-static
```

The output is written to `.build/pages` and uses `/space-prospection/` as its default URL prefix. Override that prefix when checking another deployment path:

```bash
STATIC_BASE_PATH=/another-path make export-static
```

The home, about, projects, and contact pages retain their design and navigation. Project rows are read from SQLite while the snapshot is generated. Because GitHub Pages only serves static files, the exported contact page displays a read-only notice instead of the server-side email form.

### Server Installation

GitHub Pages cannot run the complete application because it only hosts static HTML, CSS, and JavaScript, while Space Prospection requires PHP and SQLite at request time. Hosting the interactive version requires a PHP 8.1-compatible service.

Change the following before deploying:

1. Set the `APP_BASE_URL` environment variable to your public URL, or change its local fallback in [`application/config/config.php`](application/config/config.php).
2. Add your email credentials in [`application/config/email.php`](application/config/email.php).
3. Set `EMAIL_ADMIN` in [`application/config/constants.php`](application/config/constants.php) to the address that should receive contact-form messages.
4. Change `ENVIRONMENT` in [`index.php`](index.php) from `development` to `production`, or set the `CI_ENV` server variable instead.

Make sure the web server can write to the SQLite file and to the directory that contains it, and that [`.htaccess`](.htaccess) is honoured so the clean URLs keep working.

[⬆ back to top](#table-of-contents)

---

## Make Commands

```bash
make              # List every target with a short description
make check        # Verify that the machine has the required tooling
make setup        # Install dependencies and prepare the test suite
make serve        # Run the website (override with HOST and PORT)
make export-static # Generate the read-only GitHub Pages site
make test         # Run the complete quality suite
make test-phpcs   # Run PHP_CodeSniffer only
make test-phpunit # Run PHPUnit only
make fix          # Fix the coding standard violations PHP_CodeSniffer can fix
make coverage     # Run PHPUnit and print the coverage report location
make clean        # Remove generated test reports and logs
```

Make handles project setup and environment checks directly. Package-backed commands still use the matching script in [`composer.json`](composer.json), so commands such as `composer run test` and `composer run export:static` remain available where Make is not installed.

[⬆ back to top](#table-of-contents)

---

## Project Structure

| Path | Contents |
|---|---|
| [`index.php`](index.php) | Front controller and `ENVIRONMENT` switch |
| [`system/`](system/) | Unmodified CodeIgniter 3.1.13 framework; never edit |
| [`application/controllers/`](application/controllers/) | `Website_Controller`, which serves every page |
| [`application/models/`](application/models/) | `Website_Model`, which holds every database query |
| [`application/views/`](application/views/) | `templates/` chrome, `pages/` bodies, and `errors/` |
| [`application/config/`](application/config/) | Routes, autoloading, database, email, and constants |
| [`application/database/`](application/database/) | The SQLite database file |
| [`application/tests/`](application/tests/) | PHPUnit tests and the ci-phpunit-test scaffolding |
| [`assets/`](assets/) | CSS, JavaScript, fonts, and images |
| [`.github/workflows/pages.yml`](.github/workflows/pages.yml) | Static export and GitHub Pages deployment |
| [`.extras/`](.extras/) | PHP_CodeSniffer and PHPUnit configuration, the static export utility, seed SQL, and screenshots |

[⬆ back to top](#table-of-contents)

---

## Testing

Run the complete quality suite:

```bash
make test
```

This runs PHP_CodeSniffer over the controllers, models, and their tests, then runs PHPUnit. PHPUnit and PHP_CodeSniffer are installed by `make setup`, so no global installation is needed.

**Run the suite on PHP 8.1.** CodeIgniter 3.1.13 is the final 3.x release and supports PHP up to 8.1. On PHP 8.2 or newer the framework itself raises deprecation notices, and ci-phpunit-test turns those into test errors, so the suite fails for reasons unrelated to this project's code. When the machine's default PHP is newer, run the suite in a container:

```bash
docker run --rm -v "$PWD":/app -w /app php:8.1-cli \
	php application/vendor/bin/phpunit -c application/tests/
```

Run a single test file or a single test method during development:

```bash
application/vendor/bin/phpunit -c application/tests/ application/tests/models/Website_Model_Test.php

application/vendor/bin/phpunit -c application/tests/ --filter=test_navigation_method
```

The coverage report is written to `application/tests/build/coverage/index.html` and requires Xdebug or PCOV to contain real numbers. `make clean` removes it along with the generated logs.

[⬆ back to top](#table-of-contents)

---

## Contributing

Contributions are welcome. See [CONTRIBUTING.md](CONTRIBUTING.md) for the project structure, coding conventions, and test workflow.

[⬆ back to top](#table-of-contents)

---

## License

This project is licensed under the MIT License. See the [LICENSE.md](LICENSE.md) file for details.

[⬆ back to top](#table-of-contents)
