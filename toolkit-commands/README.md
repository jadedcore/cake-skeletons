# Toolkit commands

The root [`toolkit`](../toolkit) script provides a command-line interface for common development tasks in the Dockerized CakePHP application. It wraps Docker Compose, CakePHP console commands, Composer, and other project utilities so developers can run them from the project directory.

## Usage

Run commands from the repository root:

```sh
./toolkit help
./toolkit compose up -d
./toolkit cake migrations migrate
./toolkit composer install
```

Running `./toolkit` without arguments, or `./toolkit --help`, also lists the enabled commands. You can use `php toolkit help` to invoke the script through PHP directly.

The launcher requires PHP CLI on the host (PHP 8.1 or later for the toolkit's syntax). Container commands also require the PHP POSIX extension, Docker with Docker Compose, and the relevant services running. Configure `docker/.env` using [`docker/.env.example`](../docker/.env.example) before starting the Docker environment. Application commands require the CakePHP application and its dependencies to be installed.

The currently enabled commands are:

| Command | Purpose |
| --- | --- |
| `cake` | Runs `bin/cake` with the supplied arguments inside the `app` service. |
| `compose` | Runs Docker Compose with `docker/docker-compose.yml` and the supplied arguments. |
| `composer` | Runs Composer with the supplied arguments inside the `app` service. |
| `generate-keys` | Writes a 2048-bit RSA private key and its public key to `config/private.key` and `config/public.key` inside the application, replacing existing files, and sets their permissions to `660`. |
| `open` | Opens the application at `http://localhost:<APACHE_UNSECURE_PORT>` using the host's `open` command (macOS). The port is read from `docker/.env`. |
| `phpcs` | Runs `vendor/bin/phpcs` inside the `app` service and exits with its status code. |

Use `./toolkit help` for the command list in your checkout; it reflects the contents of `active/`.

## Directory structure

```text
toolkit                         PHP command-line entry point
toolkit-commands/
├── README.md                   Usage and command development guide
├── active/                     Commands loaded automatically
│   └── *Command.php
├── disabled/                   Optional commands excluded from discovery
│   └── *Command.php
└── core/
    ├── BaseCommand.php         Command interface
    ├── AbstractCommand.php     Default optional metadata
    ├── CommandRegistry.php     Discovery, registration, help, and dispatch
    └── ToolkitUtilities.php    Shared Docker and environment helpers
```

`toolkit` passes the full command-line argument array to `CommandRegistry`. The registry changes the working directory to the repository root, loads the core files, and discovers `active/*Command.php`. Each file must define a matching class in the `Toolkit\Commands` namespace, implement `BaseCommand`, and be constructible without arguments.

The registry uses `name()` as the command's registration key and resolves aliases from `aliases()`. It then calls `run()` with the original argument array and a shared `ToolkitUtilities` instance. In that array, index `0` is the script name, index `1` is the command name or alias, and indexes `2` onward contain the command's arguments.

`ToolkitUtilities` supplies the configured Docker Compose invocation, container execution, service checks, application path conversion, Docker environment lookups, and fatal error reporting. Container execution uses `docker compose exec`, so it requires an already running service.

## Adding a command

Create a file such as `active/HelloCommand.php`:

```php
<?php

namespace Toolkit\Commands;

use Toolkit\Core\ToolkitUtilities;

/**
 * Prints a greeting from the toolkit.
 */
class HelloCommand extends AbstractCommand {
	public function name(): string {
		return 'hello';
	}

	public function description(): string {
		return 'Print a greeting';
	}

	public function run(array $args, ToolkitUtilities $utilities): void {
		$name = $args[2] ?? 'world';
		echo "Hello, $name!\n";
	}
}
```

The filename and class name must match and end in `Command`. Choose a unique command name and unique aliases; reserve `help` for the registry. No manual registration is needed:

```sh
./toolkit help
./toolkit hello CakePHP
```

Extending `AbstractCommand` provides empty defaults for `aliases()` and `namedArguments()`. Override `aliases()` to return alternative command names, such as `['hi']`. Override `namedArguments()` to return a map such as `['format' => 'json|text']`, which appears in help as `--format=json|text`. This map only documents options; each command must parse and validate its own arguments in `run()`.

Commands can implement `BaseCommand` directly instead, but must then implement all five methods: `name()`, `description()`, `aliases()`, `namedArguments()`, and `run()`.

## Enabling or disabling commands

Move a command file from `active/` to `disabled/` to exclude it from discovery. Move it back to enable it.

Review optional commands before enabling them: some depend on project-specific services, scripts, or tools. Some also retain the older `use ToolkitUtilities;` import, which must be updated to `use Toolkit\Core\ToolkitUtilities;` to match the current interface. Check the filename, namespace, class name, and method signatures before moving a command into `active/`, because an incompatible command can prevent the toolkit from loading, including help.
