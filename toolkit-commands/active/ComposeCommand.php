<?php

namespace Toolkit\Commands;

use Toolkit\Core\ToolkitUtilities;

/**
 * This toolkit command is a wrapper for docker compose commands. It uses the preconfigured
 * container paths to execute the command.
 */
class ComposeCommand extends AbstractCommand {
	public function name(): string {
		return 'compose';
	}

	/**
	 * {@inheritdoc}
	 */
	public function description(): string {
		return 'docker-compose commands';
	}

	/**
	 * {@inheritdoc}
	 */
	public function run(array $args, ToolkitUtilities $utilities): void {
		echo implode(' ', $args) . "\n";
		passthru("{$utilities->dockerComposeCommand()} " . implode(' ', array_slice($args, 2)));
	}
}
