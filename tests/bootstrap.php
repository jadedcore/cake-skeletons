<?php

require_once __DIR__ . '/Support/CommandFunctions.php';
require_once __DIR__ . '/../toolkit-commands/core/ToolkitUtilities.php';
require_once __DIR__ . '/../toolkit-commands/core/BaseCommand.php';
require_once __DIR__ . '/../toolkit-commands/core/AbstractCommand.php';

$originalDirectory = getcwd();
require_once __DIR__ . '/../toolkit-commands/core/CommandRegistry.php';
chdir($originalDirectory);

foreach (glob(__DIR__ . '/../toolkit-commands/active/*Command.php') as $commandFile) {
	require_once $commandFile;
}
