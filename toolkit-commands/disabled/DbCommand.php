<?php

namespace Toolkit\Commands;

use ToolkitUtilities;

class DbCommand extends AbstractCommand {
	public function name(): string {
		return 'db';
	}

	public function description(): string {
		return 'Open MySQL or GUI URL. Use ./toolkit db sql to open MySQL CLI or ./toolkit db to open MySQL Application (SequelAce, etc.).';
	}

	public function run(array $args, ToolkitUtilities $utilities): void {
		if (!$utilities->isServiceRunning(service: 'mariadb')) {
			echo "\033[1;31m\nMariadb is not running. Run './toolkit restart mariadb' to restart the service.\033[0m\n\n";
			return;
		}

		exec("{$utilities->dockerComposeCommand()} exec mariadb printenv MARIADB_ROOT_PASSWORD", $passwordOutput);
		$password = $passwordOutput[0];
		if (($args[2] ?? '') === 'sql') {
			$utilities->executeInContainer(service: 'mariadb', command: "mariadb -u root --password=$password");
			return;
		}

		exec("{$utilities->dockerComposeCommand()} port mariadb 3306", $portOut);
		[$host, $hostPort] = explode(':', trim($portOut[0]), 2);
		exec("open mysql://root:$password@{$host}:{$hostPort}");
	}
}
