<?php

namespace Toolkit\Commands;

use ToolkitUtilities;

class ComposerCommand extends AbstractCommand {
	public function name(): string {
		return 'composer';
	}

	public function description(): string {
		return 'Run composer commands in app directory';
	}

	public function run(array $args, ToolkitUtilities $utilities): void {
		$utilities->executeInContainer(service: 'app', command: "composer " . implode(' ', array_slice($args, 2)));
	}
}
