<?php

namespace Toolkit\Commands;

use ToolkitUtilities;

class RunCommand extends AbstractCommand {
	public function name(): string {
		return 'run';
	}

	public function description(): string {
		return 'Run a command in a container. Use ./toolkit run <service> <command> to run a command';
	}

	public function run(array $args, ToolkitUtilities $utilities): void {
		$utilities->executeInContainer(
			service: $args[2],
			command: implode(' ', array_slice($args, 3)),
			useExitCode: true
		);
	}
}
