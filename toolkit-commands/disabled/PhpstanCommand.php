<?php

namespace Toolkit\Commands;

use ToolkitUtilities;

class PhpstanCommand extends AbstractCommand {
	public function name(): string {
		return 'phpstan';
	}

	public function description(): string {
		return 'Run PHPStan';
	}

	public function run(array $args, ToolkitUtilities $utilities): void {
		$extra = count($args) > 2 ? implode(' ', array_slice($args, 2)) : '';
		$utilities->executeInContainer(service: 'app', command: "vendor/bin/phpstan --memory-limit=8G analyze -c phpstan.neon $extra", useExitCode: true);
	}
}
