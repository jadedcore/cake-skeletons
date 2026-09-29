<?php

namespace Toolkit\Tests;

use PHPUnit\Framework\TestCase;
use Toolkit\Commands\CakeCommand;
use Toolkit\Commands\ComposeCommand;
use Toolkit\Commands\ComposerCommand;
use Toolkit\Commands\GenerateKeysCommand;
use Toolkit\Commands\OpenCommand;
use Toolkit\Commands\PhpcsCommand;
use Toolkit\Core\ToolkitUtilities;
use Toolkit\Tests\Support\CommandFunctions;

final class ActiveCommandsTest extends TestCase {
	protected function setUp(): void {
		CommandFunctions::$calls = [];
	}

	protected function tearDown(): void {
		CommandFunctions::$calls = null;
	}

	public function testCakeForwardsArgumentsToAppContainer(): void {
		$utilities = $this->createMock(ToolkitUtilities::class);
		$utilities->expects($this->once())->method('executeInContainer')
			->with('app', 'bin/cake migrations migrate')->willReturn(0);
		(new CakeCommand())->run(['toolkit', 'cake', 'migrations', 'migrate'], $utilities);
	}

	public function testComposerForwardsArgumentsToAppContainer(): void {
		$utilities = $this->createMock(ToolkitUtilities::class);
		$utilities->expects($this->once())->method('executeInContainer')
			->with('app', 'composer install --no-dev')->willReturn(0);
		(new ComposerCommand())->run(['toolkit', 'composer', 'install', '--no-dev'], $utilities);
	}

	public function testComposeUsesConfiguredInvocationAndPrintsArguments(): void {
		$utilities = $this->createMock(ToolkitUtilities::class);
		$utilities->expects($this->once())->method('dockerComposeCommand')
			->willReturn('docker compose -f fixture.yml');
		$this->expectOutputString("toolkit compose up -d\n");
		(new ComposeCommand())->run(['toolkit', 'compose', 'up', '-d'], $utilities);
		$this->assertSame(['docker compose -f fixture.yml up -d'], CommandFunctions::$calls);
	}

	public function testGenerateKeysCreatesPrivateThenPublicKeyAndSetsPermissions(): void {
		$calls = [];
		$utilities = $this->createMock(ToolkitUtilities::class);
		$utilities->expects($this->exactly(3))->method('executeInContainer')
			->willReturnCallback(function (string $service, string $command) use (&$calls): int {
				$calls[] = [$service, $command];
				return 0;
			});
		(new GenerateKeysCommand())->run(['toolkit', 'generate-keys'], $utilities);
		$this->assertSame([
			['app', 'openssl genrsa -out config/private.key 2048'],
			['app', 'openssl rsa -in config/private.key -pubout -out config/public.key'],
			['app', 'chmod 660 config/private.key config/public.key'],
		], $calls);
	}

	public function testOpenUsesConfiguredPort(): void {
		$utilities = $this->createMock(ToolkitUtilities::class);
		$utilities->expects($this->once())->method('getEnvValue')
			->with('APACHE_UNSECURE_PORT')->willReturn('8080');
		$utilities->expects($this->never())->method('fatal');
		(new OpenCommand())->run(['toolkit', 'open'], $utilities);
		$this->assertSame(['open http://localhost:8080'], CommandFunctions::$calls);
	}

	public function testOpenReportsMissingPortWithoutLaunchingBrowser(): void {
		$utilities = $this->createMock(ToolkitUtilities::class);
		$utilities->expects($this->once())->method('getEnvValue')
			->with('APACHE_UNSECURE_PORT')->willReturn(null);
		$utilities->expects($this->once())->method('fatal')
			->with('APACHE_UNSECURE_PORT is not defined in the docker/.env.')
			->willThrowException(new \RuntimeException('missing port'));
		try {
			(new OpenCommand())->run(['toolkit', 'open'], $utilities);
			$this->fail('Expected fatal error for the missing port.');
		} catch (\RuntimeException $exception) {
			$this->assertSame('missing port', $exception->getMessage());
			$this->assertSame([], CommandFunctions::$calls);
		}
	}

	public function testPhpcsWithoutPathsPropagatesExitCode(): void {
		$utilities = $this->getMockBuilder(ToolkitUtilities::class)
			->onlyMethods(['executeInContainer'])->getMock();
		$utilities->expects($this->once())->method('executeInContainer')
			->with('app', 'vendor/bin/phpcs', '', false, true)->willReturn(0);
		(new PhpcsCommand())->run(['toolkit', 'phpcs'], $utilities);
	}

	public function testPhpcsSeparatesExecutableFromRequestedPath(): void {
		$utilities = $this->getMockBuilder(ToolkitUtilities::class)
			->onlyMethods(['executeInContainer'])->getMock();
		$utilities->expects($this->once())->method('executeInContainer')
			->with('app', 'vendor/bin/phpcs src/Example.php', '', false, true)->willReturn(0);
		(new PhpcsCommand())->run(['toolkit', 'phpcs', 'app/src/Example.php'], $utilities);
	}
}
