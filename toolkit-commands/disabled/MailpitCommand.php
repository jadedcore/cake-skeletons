<?php

namespace Toolkit\Commands;

use ToolkitUtilities;

class MailpitCommand extends AbstractCommand {
	public function name(): string {
		return 'mailpit';
	}

	public function description(): string {
		return 'Open Mailpit in browser';
	}

	public function run(array $args, ToolkitUtilities $utilities): void {
		passthru("open https://mailpit.localhost");
	}
}
