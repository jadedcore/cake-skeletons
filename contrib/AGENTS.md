# AGENTS.md

## Project Overview

This repository is a CakePHP application created from the JadedCore CakePHP development skeleton.

This file defines the default development conventions for this application. Application-specific architecture, domain rules, and additional documentation should be added as the project evolves.

Before making changes, inspect the existing implementation and relevant documentation rather than assuming how a subsystem works.

## Technology

The standard development environment uses:

* PHP 8.4+
* CakePHP 5.x
* Composer
* MariaDB
* Docker / Docker Compose
* PHPUnit
* PHP_CodeSniffer

Prefer framework-native CakePHP functionality and existing project dependencies before introducing additional libraries or custom infrastructure.

Do not add a Composer dependency without a clear technical reason.

## CakePHP Architecture

Follow CakePHP conventions unless this repository explicitly documents an alternative.

### Controllers

Controllers should coordinate HTTP requests and application workflows.

Controllers should:

* accept and validate request-level input;
* invoke the appropriate application/model functionality;
* handle redirects and responses;
* prepare data for templates.

Avoid placing substantial persistence or reusable business logic directly in controllers.

### Table Classes

Table classes are the primary location for:

* persistence logic;
* queries and finders;
* associations;
* validation;
* rules;
* database-oriented application logic.

Prefer reusable Table methods and finders over duplicating queries throughout controllers.

### Entities

Entities represent individual records and record-level behavior.

Use entities for:

* field transformations;
* virtual properties;
* serialization behavior;
* record-level behavior.

Do not place database queries in entities.

### Policies

Authorization decisions belong in policies or the application's established authorization layer.

Do not rely solely on hiding UI elements to enforce authorization.

Server-side authorization must protect restricted actions and resources.

### Middleware

Use middleware for request-level cross-cutting concerns.

Do not put application-specific business logic in middleware.

### Services

Introduce service classes when functionality does not naturally belong in a controller, Table, Entity, Policy, or middleware component.

Do not create services merely to add another architectural layer.

Prefer the simplest structure that cleanly expresses the application behavior.

## Coding Standards

All PHP code must comply with the repository's `phpcs.xml`.

Important conventions include:

* Use tabs for indentation. Do not use spaces for indentation.
* Opening braces belong on the same line as class, function, method, conditional, loop, and other block declarations.
* Follow existing naming conventions.
* Use explicit types where appropriate.
* Prefer readable code over clever or unnecessarily abstract code.
* Do not reformat unrelated code.
* Do not modify unrelated files while completing a task.

Preferred formatting:

```php
public function example(string $value): bool {
	if ($value === '') {
		return false;
	}

	return true;
}
```

Before considering PHP changes complete, run the project's configured code-quality checks.

## Database

Use CakePHP ORM for normal application database access.

Prefer:

* Table associations;
* finders;
* query builder;
* validation;
* application rules;

over raw SQL.

Raw SQL should only be introduced when there is a clear reason that the ORM/query builder is unsuitable.

Database schema changes must be implemented using CakePHP migrations.

Do not manually change the expected schema without creating or modifying an appropriate migration.

## Security

Treat authentication, authorization, user input, credentials, and other security-sensitive functionality conservatively.

Never:

* commit passwords, API keys, tokens, private keys, or other secrets;
* commit local environment configuration containing secrets;
* hard-code production credentials;
* log passwords or authentication credentials;
* bypass authorization to simplify an implementation;
* implement custom cryptography when established framework or library functionality exists.

Validate and authorize requests server-side regardless of client-side validation or UI restrictions.

## Configuration

Environment-specific configuration must not be hard-coded into application source code.

Use the project's established CakePHP configuration and environment-variable mechanisms.

Do not commit local secrets or environment-specific credentials.

When adding a new configuration value:

1. determine whether it belongs in shared application configuration or environment-specific configuration;
2. document it appropriately;
3. provide a safe development/example value when appropriate;
4. never commit a production secret.

## Tests

New functionality and bug fixes should include appropriate automated tests.

Prioritize tests for:

* authentication;
* authorization;
* persistence behavior;
* validation;
* important application workflows;
* security boundaries;
* bug regressions.

Tests should verify behavior rather than implementation details whenever practical.

Do not report tests as passing unless they were actually executed successfully.

If tests cannot run because of an environment or dependency issue, clearly report the reason.

## Working With Existing Code

Before implementing a change:

1. Inspect the relevant existing code.
2. Identify similar functionality already present in the application.
3. Read documentation relevant to the subsystem.
4. Follow established patterns unless there is a clear reason to change them.
5. Make the smallest coherent change necessary.
6. Add or update appropriate tests.
7. Run relevant tests and code-quality checks.
8. Review the resulting diff for unrelated changes.

Do not perform broad refactoring unless explicitly requested or necessary to implement the requested change safely.

If you discover an unrelated problem, identify it rather than silently expanding the scope of the task.

## Dependencies

Before adding a new dependency:

1. determine whether CakePHP, PHP, or an existing dependency already provides the required functionality;
2. consider the maintenance and security implications;
3. explain why the dependency is necessary.

Do not replace established application functionality with a new package without an explicit reason.

## Documentation

`AGENTS.md` contains instructions for agents working on this repository.

Human-facing technical documentation should live in `README.md` or under `docs/`.

When changing:

* installation requirements;
* configuration;
* public interfaces;
* important architecture;
* development commands;
* application behavior that developers need to understand;

update the relevant documentation.

## Application-Specific Instructions

Add application-specific architectural rules below this section as the project develops.

Examples include:

* domain model boundaries;
* authentication architecture;
* authorization rules;
* API conventions;
* external integrations;
* background processing;
* application-specific testing requirements;
* deployment constraints.

Do not assume application-specific conventions that have not yet been established or documented.
