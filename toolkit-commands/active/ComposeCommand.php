<?php

namespace Toolkit\Commands;

use ToolkitUtilities;

class ComposeCommand extends AbstractCommand {
	public function name(): string {
		return 'compose';
	}

	public function description(): string {
		return 'docker-compose commands';
	}

	public function run(array $args, ToolkitUtilities $utilities): void {
		passthru("{$utilities->dockerComposeCommand()} " . implode(' ', array_slice($args, 2)));
	}
}
