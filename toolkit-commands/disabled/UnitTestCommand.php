<?php

namespace Toolkit\Commands;

use ToolkitUtilities;

class UnitTestCommand extends AbstractCommand {
	public function name(): string {
		return 'unit-test';
	}

	public function description(): string {
		return 'Run unit tests';
	}

	public function run(array $args, ToolkitUtilities $utilities): void {
		$utilities->executeInContainer(service: 'app', command: "rm -f /tmp/cake_cache/*");
		$utilities->executeInContainer(service: 'app', command: "./bin/rebuildUnitTestTables.script");

		$arg = $args[2] ?? '';
		if ($arg === 'all') {
			$utilities->executeInContainer(service: 'app', command: "php -d apc.enable_cli=1 vendor/bin/phpunit --testsuite FullTestSuite");
		} else {
			$utilities->executeInContainer(service: 'app', command: "php -d apc.enable_cli=1 vendor/bin/phpunit " . $utilities->extractAppFilePath(args: $args));
		}

		$utilities->executeInContainer(service: 'app', command: "chmod 777 -R /tmp/cake_cache");
	}
}
