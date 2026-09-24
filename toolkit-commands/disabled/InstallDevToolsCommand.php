<?php

namespace Toolkit\Commands;

use ToolkitUtilities;

class InstallDevToolsCommand extends AbstractCommand {
	public function name(): string {
		return 'install-dev-tools';
	}

	public function description(): string {
		return 'Install tools like Xdebug';
	}

	public function run(array $args, ToolkitUtilities $utilities): void {
		echo "\n1. Xdebug\nChoose tool to install: ";
		$tool = trim(fgets(STDIN));

		if ($tool !== '1') {
			return;
		}

		exec("{$utilities->dockerComposeCommand()} exec app php -m | grep -qi '^xdebug$'", $output, $status);
		if ($status === 0) {
			echo "Xdebug is already installed.\n";
			return;
		}

		$installStatus = $utilities->executeInContainer(
			service: 'app',
			command: "pecl list | grep -qi '^xdebug ' || pecl install xdebug",
			useRoot: true,
		);
		if ($installStatus !== 0) {
			echo "Xdebug installation failed.\n";
			return;
		}

		$configStatus = $utilities->executeInContainer(
			service: 'app',
			command: "grep -q 'zend_extension=.*xdebug.so' /usr/local/etc/php/php.ini || echo 'zend_extension=xdebug.so' >> /usr/local/etc/php/php.ini",
			useRoot: true,
		);
		if ($configStatus === 0) {
			$configStatus = $utilities->executeInContainer(
				service: 'app',
				command: "grep -q '^xdebug.mode=coverage$' /usr/local/etc/php/php.ini || echo 'xdebug.mode=coverage' >> /usr/local/etc/php/php.ini",
				useRoot: true,
			);
		}
		if ($configStatus !== 0) {
			echo "Xdebug configuration failed.\n";
			return;
		}

		$verifyStatus = $utilities->executeInContainer(
			service: 'app',
			command: "php -m | grep -qi '^xdebug$'",
			useRoot: true,
		);
		if ($verifyStatus !== 0) {
			echo "Xdebug was installed but could not be loaded. Check /usr/local/etc/php/php.ini.\n";
			return;
		}

		echo "Xdebug installed and enabled for coverage.\n";
	}
}
