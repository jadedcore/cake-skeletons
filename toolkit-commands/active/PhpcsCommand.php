<?php

namespace Toolkit\Commands;

use ToolkitUtilities;

class PhpcsCommand extends AbstractCommand {
	public function name(): string {
		return 'phpcs';
	}

	public function description(): string {
		return 'Run PHP CodeSniffer';
	}

	public function run(array $args, ToolkitUtilities $utilities): void {
		$utilities->executeInContainer(service: 'app', command: "vendor/bin/phpcs" . $utilities->extractAppFilePath(args: $args), useExitCode: true);
	}
}
