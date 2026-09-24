<?php

namespace Toolkit\Commands;

use ToolkitUtilities;

interface BaseCommand {
	public function name(): string;

	public function description(): string;

	public function aliases(): array;

	public function namedArguments(): array;

	public function run(array $args, ToolkitUtilities $utilities): void;
}
