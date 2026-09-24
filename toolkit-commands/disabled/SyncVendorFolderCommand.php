<?php

namespace Toolkit\Commands;

use ToolkitUtilities;

class SyncVendorFolderCommand extends AbstractCommand {
	public function name(): string {
		return 'sync-vendor-folder';
	}

	public function description(): string {
		return 'Copy vendor folder from container';
	}

	public function run(array $args, ToolkitUtilities $utilities): void {
		passthru("{$utilities->dockerComposeCommand()} cp app:/var/www/html/vendor app/");
	}
}
