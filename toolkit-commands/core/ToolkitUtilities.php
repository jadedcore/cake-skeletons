<?php

namespace Toolkit\Core;

/**
 * Utilities available to the CommandRegistry.php as well as any
 * commands created to be used by toolkit.
 */
class ToolkitUtilities {
	private string $DOCKER_COMPOSE_COMMAND;

	private const DOCKER_COMPOSE_EXEC = "docker compose";
	private const DOCKER_COMPOSE_LOCATION = "docker/docker-compose.yml";

	public function __construct() {
		$this->DOCKER_COMPOSE_COMMAND =
			self::DOCKER_COMPOSE_EXEC . ' --progress plain -f ' . self::DOCKER_COMPOSE_LOCATION;
	}

	/**
	 * Returns the Docker Compose command used by this registry.
	 *
	 * @return string the configured Docker Compose Command
	 */
	public function dockerComposeCommand(): string {
		return $this->DOCKER_COMPOSE_COMMAND;
	}

	/**
	 * Executes a command inside a Docker container.
	 *
	 * @param string $service The service name.
	 * @param string $command The command to execute.
	 * @param string $workdir The working directory inside the container.
	 * @param bool $forceTTY Whether to force TTY allocation.
	 * @param bool $useExitCode Whether to use the exit code of the command.
	 * @param bool $useRoot Whether to execute the command as root.
	 * @return int The command exit code.
	 */
	public function executeInContainer(
		string $service,
		string $command,
		string $workdir = '',
		bool $forceTTY = false,
		bool $useExitCode = false,
		bool $useRoot = false
	): int {
		$tty = $forceTTY || posix_isatty(STDIN);
		$ttyFlag = $tty ? '' : '-T';
		$rootFlag = $useRoot ? '-u 0' : '';

		$environmentFlag = '';
		if (isset($_ENV['BRANDING'])) {
			$environmentFlag = "-e BRANDING=" . $_ENV['BRANDING'];
		}

		$workdirCmd = $workdir ? "cd $workdir && " : '';
		$cmd = "{$this->DOCKER_COMPOSE_COMMAND} exec $ttyFlag $rootFlag $environmentFlag $service /bin/sh -c \"$workdirCmd$command\"";
		$process = proc_open($cmd, [0 => STDIN, 1 => STDOUT, 2 => STDERR], $pipes);
		$exitCode = proc_close($process);
		if ($useExitCode) {
			exit($exitCode);
		}

		return $exitCode;
	}

	/**
	 * Checks the container to determine if a service is running.
	 *
	 * @param string $service - The name of the service to check
	 * @return bool
	 */
	public function isServiceRunning(string $service): bool {
		exec("{$this->DOCKER_COMPOSE_COMMAND} ps -q $service", $output);
		return !empty($output);
	}

	/**
	 * Extracts the file paths from the given arguments, relative to the app directory.
	 *
	 * Only strips `app/` when it is a whole path segment, so paths like
	 * `plugins/Foo/tests/test_app/src/Bar.php` are left intact.
	 *
	 * @param array $args The command line arguments.
	 * @return string The extracted file paths.
	 */
	public function extractAppFilePath(array $args): string {
		$paths = array_map(
			fn(string $arg) => preg_replace('#^(?:.*?/)?app/#', '', $arg),
			array_slice($args, 2)
		);
		return implode(' ', $paths);
	}

	/**
	 * Gets a value from the Docker environment file.
	 *
	 * @param string $key The environment variable key.
	 * @return string|null The value of the environment variable, or null if not found.
	 */
	public function getEnvValue(string $key): ?string {
		$envFile = __DIR__ . '/../../docker/.env';

		if (!is_file($envFile)) {
			return null;
		}

		$lines = file($envFile, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

		foreach ($lines as $line) {
			$line = trim($line);

			if ($line === '' || str_starts_with($line, '#')) {
				continue;
			}

			[$name, $value] = array_pad(explode('=', $line, 2), 2, null);

			if (trim($name) === $key) {
				return trim($value, " \t\n\r\0\x0B\"'");
			}
		}

		return null;
	}

	/**
	 * Handles fatal errors by displaying an error message and terminating the script.
	 *
	 * @param string $message The error message to display.
	 */
	public function fatal(string $message): never {
		fwrite(STDERR, "\033[1;31mToolkit error: $message\033[0m\n");
		exit(1);
	}
}
