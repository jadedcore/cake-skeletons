<?php

namespace Toolkit\Commands;

use Toolkit\Core\ToolkitUtilities;

/**
 * This command is used to generate public/private key pair inside the
 * container.
 */
class GenerateKeysCommand extends AbstractCommand {
	/**
	 * {@inheritdoc}
	 */
	public function name(): string {
		return 'generate-keys';
	}

	/**
	 * {@inheritdoc}
	 */
	public function description(): string {
		return 'Generate public/private keys';
	}

	/**
	 * {@inheritdoc}
	 */
	public function run(array $args, ToolkitUtilities $utilities): void {
		$utilities->executeInContainer(
			service: 'app', command: "openssl genrsa -out config/private.key 2048"
		);
		$utilities->executeInContainer(
			service: 'app', command: "openssl rsa -in config/private.key -pubout -out config/public.key"
		);
		$utilities->executeInContainer(
			service: 'app', command: "chmod 660 config/private.key config/public.key"
		);
	}
}
