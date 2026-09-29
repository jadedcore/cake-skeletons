<?php

namespace Toolkit\Commands;

/**
 * Provides default metadata for commands without aliases or named arguments.
 */
abstract class AbstractCommand implements BaseCommand {
	/**
	 * Returns an empty alias list unless overridden by a command.
	 *
	 * @return string[] Alternative command names.
	 */
	public function aliases(): array {
		return [];
	}

	/**
	 * Returns an empty named argument map unless overridden by a command.
	 *
	 * @return array<string, string> Argument names mapped to value hints for help output.
	 */
	public function namedArguments(): array {
		return [];
	}
}
