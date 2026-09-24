<?php

namespace Toolkit\Commands;

use ToolkitUtilities;

class GenerateKeysCommand extends AbstractCommand {
	public function name(): string {
		return 'generate-keys';
	}

	public function description(): string {
		return 'Generate public/private keys';
	}

	public function run(array $args, ToolkitUtilities $utilities): void {
		$utilities->executeInContainer(service: 'app', command: "openssl genrsa -out config/private.key 2048");
		$utilities->executeInContainer(service: 'app', command: "openssl rsa -in config/private.key -pubout -out config/public.key");
		$utilities->executeInContainer(service: 'app', command: "chmod 660 config/private.key config/public.key");
	}
}
