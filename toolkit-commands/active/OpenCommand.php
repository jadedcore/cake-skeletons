<?php

namespace Toolkit\Commands;

use ToolkitUtilities;

class OpenCommand extends AbstractCommand {
	public function name(): string {
		return 'open';
	}

	public function description(): string {
		return 'Open app in browser.';
	}

	public function run(array $args, ToolkitUtilities $utilities): void {
		passthru("open https://localhost");
	}
}
