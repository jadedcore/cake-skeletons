<?php

namespace Toolkit\Commands;

use Toolkit\Core\ToolkitUtilities;

/**
 * Runs composer commands inside the container app directory.
 */
class ComposerCommand extends AbstractCommand {
	/**
	 * {@inheritdoc}
	 */
	public function name(): string {
		return 'composer';
	}

	/**
	 * {@inheritdoc}
	 */
	public function description(): string {
		return 'Run composer commands in app directory';
	}

	/**
	 * {@inheritdoc}
	 */
	public function run(array $args, ToolkitUtilities $utilities): void {
		$utilities->executeInContainer(
			service: 'app',
			command: "composer " . implode(' ', array_slice($args, 2))
		);
	}
}
