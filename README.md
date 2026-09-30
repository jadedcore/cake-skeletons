# CakePHP 5 Dockerized Skeleton

An opinionated development environment and project bootstrap for quickly starting new CakePHP 5 applications.

The goal of this repository is simple: eliminate the repetitive setup required every time a new CakePHP project is created.

The installer creates a clean CakePHP application, applies standard project configuration and coding conventions, and provides a Docker-based local development environment.

## Requirements

The following must already be installed on the host system:

* Docker Desktop
* Git

## What's Included

The generated development environment provides:

* CakePHP 5
* PHP 8.4+
* Apache
* MariaDB
* Composer
* PHPUnit
* CakePHP CodeSniffer
* Custom PHP_Codesniffer configuration
* Docker Compose configuration
* Development toolkit
* AI agent development instructions

The skeleton also applies custom project defaults from the `contrib/` directory after CakePHP creates the application.

These defaults include project coding standards and AI agent development instructions.

## Quick Start

Clone the repository:

```bash
git clone https://github.com/jadedcore/cake-skeletons.git
cd cake-skeletons
```

Run the installer:

```bash
./install.sh
```

The installer asks for a project name and creates a sibling directory with that name. It copies in the development tooling, creates the CakePHP application in the project's `app/` directory, and initializes Git for the entire project. The skeleton checkout is left unchanged.

## How It Works

The bootstrap process is intentionally built on top of the official CakePHP application skeleton rather than maintaining a fork of CakePHP's skeleton.

At a high level:

```text
CakePHP Application Skeleton
            │
            ▼
    composer create-project
            │
            ▼
 Apply Your Configuration
            │
            ▼
 Configure Docker Environment
            │
            ▼
   Ready for Development
```

This allows new projects to start from the current CakePHP 5 application skeleton while still applying a consistent development environment and project conventions.

## Project Defaults

Files under `contrib/` contain configuration that is applied to newly created CakePHP applications.

These files override or supplement CakePHP's generated defaults.

Examples include:

### `phpcs.xml`

Defines the PHP coding standards used by generated projects.

Among other project conventions:

* tabs are used for indentation;
* opening braces are placed on the same line as declarations;
* generated projects use a consistent PHPCS configuration.

### `AGENTS.md`

Provides repository-level development instructions for AI coding agents such as Codex.

It establishes baseline CakePHP architecture, coding standards, security practices, testing expectations, and rules for modifying generated applications.

Application-specific instructions should be added to the generated project's `AGENTS.md` as the application evolves.

## Docker Environment

The Docker environment provides the services required for local CakePHP development.

The stack includes:

* Apache/PHP application container
* MariaDB

Environment-specific configuration is maintained separately from application source code.

Copy the example environment configuration when configuring the environment manually:

```bash
cp docker/.env.example docker/.env
```

The installer normally handles the required development setup.

## Development Toolkit

`toolkit` provides convenience commands for interacting with the Dockerized CakePHP environment.

The toolkit is intended to provide a consistent interface for common development operations such as:

* starting and stopping the environment;
* running CakePHP CLI commands;
* running migrations;
* accessing containers;
* running tests;
* running code-quality tools.

Run:

```bash
./toolkit
```

to view the currently supported commands.

## Skeleton Development

The skeleton's PHPUnit tests use a separate root Composer project. These development dependencies are for contributors working on this repository and are not part of generated CakePHP applications.

Install the test dependencies and run the test suite with:

```bash
composer install
composer test
```

## Repository Structure

```text
cake-skeletons/
├── contrib/            Files applied to generated applications
├── docker/             Docker configuration and environment
├── tests/              Unit Tests
├── toolkit-commands    Toolkit classes and individual toolkit commands
├── install.sh          Project bootstrap/install script
├── toolkit             Development command wrapper
└── README.md
```

## Design Philosophy

This repository is intended to handle **development environment and project bootstrap**, not reusable application functionality.

Reusable CakePHP functionality such as authentication, account management, auditing, or other common application infrastructure should live in reusable CakePHP plugins rather than being copied into every generated project.

This keeps the responsibilities separate:

```text
cake-skeletons
    Development environment
    Project bootstrap
    Coding standards
    Development tooling

CakePHP Plugins
    Reusable application functionality

Application
    Product-specific business logic
```

## Customizing the Skeleton

To add another standard file to newly generated applications:

1. Add the file to `contrib/`.
2. Update `install.sh` to copy it to the appropriate location after CakePHP's `composer create-project` step.
3. Document the purpose of the file here if developers need to know about it.

Prefer explicit installation of configuration files rather than maintaining a modified copy of the entire CakePHP application skeleton.

## Roadmap

The skeleton is still evolving.

Current areas for improvement include:

* Provide a standard `app_local.php` configuration that consumes the Docker environment variables.
* Continue generalizing `toolkit` so that it contains only functionality appropriate for any generated CakePHP project.
* Standardize project verification commands for PHPUnit and PHPCS.
* Make installation of reusable standard plugins configurable.

## Relationship to Foundation

A reusable CakePHP Foundation plugin is being developed separately to provide common application-level functionality such as:

* user identity;
* authentication;
* account registration and activation;
* password reset;
* authorization infrastructure;
* auditing;
* other common application concerns.

The long-term goal is for `cake-skeletons` to be capable of creating a new CakePHP development environment and optionally installing the standard Foundation plugin, leaving the new application ready for product-specific development.

## License

This project is licensed under the BSD 3-Clause License.

Copyright © 2026, Chris Valliere

See the [License](License) file for deatails.

## Generated Projects

This repository uses the official CakePHP application skeleton to create new CakePHP applications. CakePHP and its application skeleton are separately licensed under the MIT License.

The BSD 3-Clause License applies to the original code and configuration provided by this repository. It does not replace or modify the licenses of CakePHP or other third-party dependencies.
