<?php

namespace Toolkit\Commands;

use ToolkitUtilities;

class RectorCommand extends AbstractCommand {
	public function name(): string {
		return 'rector';
	}

	public function description(): string {
		return 'Run Rector';
	}

	public function run(array $args, ToolkitUtilities $utilities): void {
		$extra = count($args) > 2 ? implode(' ', array_slice($args, 2)) : "src/ plugins/ templates/ tests/ webroot/";
		$utilities->executeInContainer(service: 'app', command: "/var/www/html/vendor/bin/rector process $extra", useExitCode: true);
	}
}
