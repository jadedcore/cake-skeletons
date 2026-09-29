<?php

namespace Toolkit\Commands;

use Toolkit\Core\ToolkitUtilities;

/**
 * Defines the metadata and execution contract for toolkit commands.
 */
interface BaseCommand {
	/**
	 * Returns the canonical name used to register and invoke the command.
	 *
	 * @return string Command name.
	 */
	public function name(): string;

	/**
	 * Returns the command description displayed in help output.
	 *
	 * @return string Command description.
	 */
	public function description(): string;

	/**
	 * Returns alternative names that can invoke the command.
	 *
	 * @return string[] Alternative command names.
	 */
	public function aliases(): array;

	/**
	 * Returns named argument metadata displayed in help output.
	 *
	 * @return array<string, string> Argument names mapped to value hints for help output.
	 */
	public function namedArguments(): array;

	/**
	 * Executes the command using the supplied arguments and shared utilities.
	 *
	 * @param string[] $args Full command-line arguments, including the script and command names.
	 * @param ToolkitUtilities $utilities Shared toolkit utilities.
	 * @return void
	 */
	public function run(array $args, ToolkitUtilities $utilities): void;
}
