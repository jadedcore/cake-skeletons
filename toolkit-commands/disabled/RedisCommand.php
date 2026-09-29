<?php

namespace Toolkit\Commands;

use ToolkitUtilities;

class RedisCommand extends AbstractCommand {
	public function name(): string {
		return 'redis';
	}

	public function description(): string {
		return 'Open Redis CLI';
	}

	public function run(array $args, ToolkitUtilities $utilities): void {
		if (!$utilities->isServiceRunning(service: 'redis')) {
			echo "\033[1;31m\nRedis is not running. Run './toolkit restart redis' to restart the service.\033[0m\n\n";
			return;
		}

		$password = exec("{$utilities->dockerComposeCommand()} exec redis printenv REDIS_PASSWORD", $passwordOutput);
		$utilities->executeInContainer(service: 'redis', command: "redis-cli -a $password");
	}
}
