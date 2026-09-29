<?php

namespace Toolkit\Commands;

use Toolkit\Core\ToolkitUtilities;

/**
 * This toolkit command will run the specified CakePHP console command
 * inside the running container.
 */
class CakeCommand extends AbstractCommand {
	/**
	 * {@inheritdoc}
	 */
	public function name(): string {
		return 'cake';
	}

	/**
	 * {@inheritdoc}
	 */
	public function description(): string {
		return 'Run CakePHP console commands';
	}

	/**
	 * {@inheritDoc}
	 */
	public function run(array $args, ToolkitUtilities $utilities): void {
		$utilities->executeInContainer(
			service: 'app',
			command: "bin/cake " . implode(' ', array_slice($args, 2))
		);
	}
}
