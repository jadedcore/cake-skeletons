<?php

namespace Toolkit\Commands;

use ToolkitUtilities;

class ShellCommand extends AbstractCommand {
	public function name(): string {
		return 'shell';
	}

	public function description(): string {
		return 'Open up a shell in a container. Defaults to app. Use ./toolkit shell <service> to open a different service.';
	}

	public function run(array $args, ToolkitUtilities $utilities): void {
		$utilities->executeInContainer(
			service: $args[2] ?? 'app',
			command: "/bin/bash || /bin/sh",
			useRoot: count($args) > 3 && $args[3] === '--root'
		);
	}
}
