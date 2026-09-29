<?php

namespace Toolkit\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Toolkit\Commands\AbstractCommand;
use Toolkit\Core\CommandRegistry;
use Toolkit\Core\ToolkitUtilities;

final class CommandRegistryTest extends TestCase {
	#[DataProvider('helpArguments')]
	public function testHelpListsActiveCommandsAndExcludesDisabledCommands(array $args): void {
		$registry = new CommandRegistry();
		$output = $this->captureOutput(fn() => $registry->execute($args));
		$output = preg_replace('/\x1b\[[0-9;]*m/', '', $output);
		$this->assertStringContainsString('Toolkit', $output);
		foreach (['cake', 'compose', 'composer', 'generate-keys', 'open', 'phpcs'] as $name) {
			$this->assertMatchesRegularExpression('/\b' . preg_quote($name, '/') . '\s+/', $output);
		}
		$this->assertStringNotContainsString('unit-test', $output);
	}

	public static function helpArguments(): array {
		return [
			'no command' => [['toolkit']],
			'help command' => [['toolkit', 'help']],
			'help option' => [['toolkit', '--help']],
		];
	}

	public function testUnknownCommandPrintsGuidance(): void {
		$registry = new CommandRegistry();
		$output = $this->captureOutput(fn() => $registry->execute(['toolkit', 'does-not-exist']));
		$this->assertStringContainsString('Command not found. Use ./toolkit help', $output);
	}

	#[DataProvider('commandNames')]
	public function testDispatchPreservesArgumentsAndPassesUtilities(string $name): void {
		$registry = new CommandRegistry();
		$command = $this->registerFixture($registry);
		$args = ['toolkit', $name, '--format=json', 'some value'];
		$registry->execute($args);
		$this->assertSame($args, $command->receivedArguments);
		$this->assertInstanceOf(ToolkitUtilities::class, $command->receivedUtilities);
	}

	public static function commandNames(): array {
		return [['fixture'], ['fixture-alias'], ['--fixture']];
	}

	public function testHelpIncludesAliasesAndNamedArgumentHints(): void {
		$registry = new CommandRegistry();
		$this->registerFixture($registry);
		$output = $this->captureOutput(fn() => $registry->execute(['toolkit', 'help']));
		$this->assertStringContainsString('Fixture command (--format=json|text) (aliases: fixture-alias)', $output);
	}

	private function registerFixture(CommandRegistry $registry): AbstractCommand {
		$command = new class extends AbstractCommand {
			public array $receivedArguments = [];
			public ?ToolkitUtilities $receivedUtilities = null;

			public function name(): string { return 'fixture'; }
			public function description(): string { return 'Fixture command'; }
			public function aliases(): array { return ['fixture-alias']; }
			public function namedArguments(): array { return ['format' => 'json|text']; }

			public function run(array $args, ToolkitUtilities $utilities): void {
				$this->receivedArguments = $args;
				$this->receivedUtilities = $utilities;
			}
		};
		// Register a harmless fixture without adding files to active/.
		$register = new \ReflectionMethod(CommandRegistry::class, 'registerCommand');
		$register->invoke($registry, $command);
		return $command;
	}

	private function captureOutput(callable $callback): string {
		ob_start();
		try {
			$callback();
			return ob_get_contents();
		} finally {
			ob_end_clean();
		}
	}
}
