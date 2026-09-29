<?php

namespace Toolkit\Tests;

use PHPUnit\Framework\TestCase;
use Toolkit\Commands\AbstractCommand;
use Toolkit\Commands\BaseCommand;
use Toolkit\Core\ToolkitUtilities;

final class AbstractCommandTest extends TestCase {
	public function testOptionalMetadataDefaultsToEmptyArrays(): void {
		$command = new class extends AbstractCommand {
			public function name(): string { return 'example'; }
			public function description(): string { return 'Example command'; }
			public function run(array $args, ToolkitUtilities $utilities): void {}
		};
		$this->assertInstanceOf(BaseCommand::class, $command);
		$this->assertSame([], $command->aliases());
		$this->assertSame([], $command->namedArguments());
	}
}
