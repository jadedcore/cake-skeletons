<?php

namespace Toolkit\Tests\Support {
	/** Captures shell calls without invoking Docker or opening a browser. */
	final class CommandFunctions {
		public static ?array $calls = null;
	}
}

namespace Toolkit\Commands {
	function passthru(string $command, ?int &$resultCode = null): void {
		if (\Toolkit\Tests\Support\CommandFunctions::$calls === null) {
			throw new \LogicException('Unexpected shell execution in a unit test.');
		}
		\Toolkit\Tests\Support\CommandFunctions::$calls[] = $command;
		$resultCode = 0;
	}
}
