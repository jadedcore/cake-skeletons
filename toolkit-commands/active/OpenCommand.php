<?php

namespace Toolkit\Commands;

use Toolkit\Core\ToolkitUtilities;

/**
 * Convenience command to open the running application in the browser.
 */
class OpenCommand extends AbstractCommand {
	/**
	 * {@inheritdoc}
	 */
	public function name(): string {
		return 'open';
	}

	/**
	 * {@inheritdoc}
	 */
	public function description(): string {
		return 'Open app in browser.';
	}

	/**
	 * {@inheritdoc}
	 */
	public function run(array $args, ToolkitUtilities $utilities): void {
		$port = $utilities->getEnvValue('APACHE_UNSECURE_PORT');

		if ($port === null) {
			$utilities->fatal('APACHE_UNSECURE_PORT is not defined in the docker/.env.');
		}

		passthru("open http://localhost:$port");
	}
}
