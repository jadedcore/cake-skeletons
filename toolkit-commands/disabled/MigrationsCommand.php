<?php

namespace Toolkit\Commands;

use ToolkitUtilities;

class MigrationsCommand extends AbstractCommand {
	public function name(): string {
		return 'migrations';
	}

	public function description(): string {
		return 'Run phinx migration commands';
	}

	public function run(array $args, ToolkitUtilities $utilities): void {
		$utilities->executeInContainer(service: 'app', command: "bin/phinxRunMigration.php");
	}
}
