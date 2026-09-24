<?php

namespace Toolkit\Commands;

abstract class AbstractCommand implements BaseCommand {
	public function aliases(): array {
		return [];
	}

	public function namedArguments(): array {
		return [];
	}
}
