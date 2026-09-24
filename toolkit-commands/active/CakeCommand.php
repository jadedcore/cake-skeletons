<?php

namespace Toolkit\Commands;

use ToolkitUtilities;

class CakeCommand extends AbstractCommand {
	public function name(): string {
		return 'cake';
	}

	public function description(): string {
		return 'Run CakePHP console commands';
	}

	public function run(array $args, ToolkitUtilities $utilities): void {
		$utilities->executeInContainer(service: 'app', command: "bin/cake " . implode(' ', array_slice($args, 2)));
	}
}
