<?php

namespace Toolkit\Tests;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Toolkit\Core\ToolkitUtilities;

final class ToolkitUtilitiesTest extends TestCase {
	public function testComposeCommandSelectsProjectConfiguration(): void {
		$this->assertSame(
			'docker compose --progress plain -f docker/docker-compose.yml',
			(new ToolkitUtilities())->dockerComposeCommand()
		);
	}

	#[DataProvider('applicationPaths')]
	public function testExtractsApplicationRelativePaths(array $paths, string $expected): void {
		$args = array_merge(['toolkit', 'phpcs'], $paths);
		$this->assertSame($expected, (new ToolkitUtilities())->extractAppFilePath($args));
	}

	public static function applicationPaths(): array {
		return [
			'no paths' => [[], ''],
			'app prefix' => [['app/src/Example.php'], 'src/Example.php'],
			'absolute path' => [['/project/app/src/Example.php'], 'src/Example.php'],
			'dot relative path' => [['./app/src/Example.php'], 'src/Example.php'],
			'already relative' => [['src/Example.php'], 'src/Example.php'],
			'multiple paths' => [['app/src/A.php', 'app/tests/B.php'], 'src/A.php tests/B.php'],
			'embedded app suffix' => [['plugins/Foo/tests/test_app/src/Bar.php'], 'plugins/Foo/tests/test_app/src/Bar.php'],
			'app substring' => [['myapp/src/Example.php'], 'myapp/src/Example.php'],
			'app without slash' => [['app'], 'app'],
		];
	}
}
