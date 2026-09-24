<?php

namespace Toolkit\Commands;

use ToolkitUtilities;

class PhpcbfCommand extends AbstractCommand {
	public function name(): string {
		return 'phpcbf';
	}

	public function description(): string {
		return 'Run PHP Code Beautifier';
	}

	public function run(array $args, ToolkitUtilities $utilities): void {
		$utilities->executeInContainer(service: 'app', command: "vendor/bin/phpcbf --standard=ruleset.xml " . $utilities->extractAppFilePath(args: $args), useExitCode: true);
	}
}
