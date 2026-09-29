<?php

namespace Toolkit\Core;

use Toolkit\Core\ToolkitUtilities;

chdir(dirname(__DIR__, 2));

/**
 * Class CommandRegistry
 *
 * This class is responsible for managing and executing various commands
 * related to Docker services and other utilities.
 */
class CommandRegistry {
	private ToolkitUtilities $utilities;
	private array $commands = [];

	/**
	 * Constructs the CommandRegistry instance.
	 */
	public function __construct() {
		require_once __DIR__ . '/ToolkitUtilities.php';
		$this->utilities = new ToolkitUtilities();
		$this->registerAllCommands();
	}

	/**
	 * Executes the specified command based on the provided arguments.
	 */
	public function execute(array $args): void {
		$command = ltrim($args[1] ?? '--help', '-');

		// Map alias or command name to canonical command key
		$cmdKey = null;
		foreach ($this->commands as $name => $cmd) {
			if ($command === $name || (!empty($cmd['aliases']) && in_array($command, $cmd['aliases'], true))) {
				$cmdKey = $name;
				break;
			}
		}

		if ($cmdKey === null && $command !== 'help' && $command !== 'h' && $command !== null) {
			echo "\033[1;31m\nCommand not found. Use ./toolkit help to see all commands.\033[0m\n\n";
			return;
		}
		$command = $cmdKey ?? $command;

		if ($command === 'help') {
			$this->showHelp();
			return;
		}

		$this->commands[$command]['callback']($args);
	}

	/**
	 * Registers all available commands from ./toolkit-commands/active directory
	 * as well as the required core command files.
	 */
	private function registerAllCommands(): void {
		$this->loadCoreCommands();

		$commandFiles = glob(__DIR__ . '/../active/*Command.php') ?: [];

		foreach ($commandFiles as $file) {
			require_once $file;

			$className = 'Toolkit\\Commands\\' . basename($file, '.php');

			if (!class_exists($className)) {
				$this->utilities->fatal(
					"Command file '" . basename($file) .
					"' does not define the expected class '$className'."
				);
			}

			if (!is_subclass_of($className, \Toolkit\Commands\BaseCommand::class)) {
				$this->utilities->fatal(
					"Command class '$className' must implement BaseCommand."
				);
			}

			$this->registerCommand(new $className());
		}
	}

	/**
	 * Loads the required core command files.
	 */
	private function loadCoreCommands(): void {
		$coreFiles = [
			'BaseCommand.php',
			'AbstractCommand.php',
		];

		foreach ($coreFiles as $file) {
			$path = __DIR__ . '/' . $file;
			if (!is_file($path) || !is_readable($path)) {
				$this->utilities->fatal("Required core command file '$file' is missing or unreadable.");
			}

			require_once $path;
		}

		if (!interface_exists(\Toolkit\Commands\BaseCommand::class)) {
			$this->utilities->fatal('BaseCommand interface failed to load.');
		}

		if (!class_exists(\Toolkit\Commands\AbstractCommand::class)) {
			$this->utilities->fatal('AbstractCommand class failed to load.');
		}
	}

	/**
	 * Registers a command with the command registry.
	 *
	 * @param \Toolkit\Commands\BaseCommand $command The command instance to register.
	 */
	private function registerCommand(\Toolkit\Commands\BaseCommand $command): void {
		$this->commands[$command->name()] = [
			'description' => $command->description(),
			'callback' => fn(array $args) => $command->run($args, $this->utilities),
			'namedArguments' => $command->namedArguments(),
			'aliases' => $command->aliases(),
		];
	}

	/**
	 * Displays the help information for all registered commands.
	 */
	private function showHelp(): void {
		echo "\n\033[1;36mToolkit\033[0m\n\n";
		foreach ($this->commands as $name => $cmd) {
			$description = $cmd['description'];
			if (!empty($cmd['namedArguments'])) {
				$description .= ' (' . implode(', ', array_map(fn($arg) => "--$arg=" . $cmd['namedArguments'][$arg], array_keys($cmd['namedArguments']))) . ')';
			}
			if (!empty($cmd['aliases'])) {
				$description .= ' (aliases: ' . implode(', ', $cmd['aliases']) . ')';
			}
			printf("  \033[1;32m%-20s\033[0m %s\n", $name, $description);
		}
		echo "\n";
	}
}
