<?php

namespace Toolkit\Commands;

use ToolkitUtilities;

class RestartCommand extends AbstractCommand {
	public function name(): string {
		return 'restart';
	}

	public function description(): string {
		return 'Restart services even if they are down already';
	}

	public function run(array $args, ToolkitUtilities $utilities): void {
		$service = $args[2] ?? '';
		if ($service) {
			passthru("{$utilities->dockerComposeCommand()} down $service && {$utilities->dockerComposeCommand()} up -d $service");
			return;
		}

		passthru("{$utilities->dockerComposeCommand()} down && {$utilities->dockerComposeCommand()} up -d");
	}
}
