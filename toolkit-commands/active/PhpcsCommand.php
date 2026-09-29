<?php

namespace Toolkit\Commands;

use Toolkit\Core\ToolkitUtilities;

/**
 * Run PHPCS inside the running application container
 */
class PhpcsCommand extends AbstractCommand {
	/**
	 * {@inheritdoc}
	 */
	public function name(): string {
		return 'phpcs';
	}

	/**
	 * {@inheritdoc}
	 */
	public function description(): string {
		return 'Run PHP CodeSniffer';
	}

	/**
	 * {@inheritdoc}
	 */
	public function run(array $args, ToolkitUtilities $utilities): void {
		$path = $utilities->extractAppFilePath(args: $args);
		$command = 'vendor/bin/phpcs' . ($path !== '' ? " $path" : '');

		$utilities->executeInContainer(
			service: 'app',
			command: $command,
			useExitCode: true
		);
	}
}
