<?php

namespace Toolkit\Commands;

use ToolkitUtilities;

/**
 * This command requires another command so I think this needs to be reworked
 *
 * @todo rework this with validateLocalCerts()
 */

class InstallLocalCertsCommand extends AbstractCommand {
	public function name(): string {
		return 'install-local-certs';
	}

	public function description(): string {
		return 'Install local SSL certificates';
	}

	public function run(array $args, ToolkitUtilities $utilities): void {
		if (!$utilities->validateLocalCerts()) {
			return;
		}

		passthru('mkcert -key-file docker/traefik/certs/key.pem -cert-file docker/traefik/certs/cert.pem "*.localhost"');
		$utilities->execute(['', 'restart', 'traefik']);
		echo "\033[1;32m\nLocal certificates installed and traefik restarted.\033[0m\n\n";
	}
}
