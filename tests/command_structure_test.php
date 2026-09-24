<?php

$root = dirname(__DIR__);
$core = [
	$root . '/toolkit-commands/core/BaseCommand.php',
	$root . '/toolkit-commands/core/AbstractCommand.php',
];
$active = glob($root . '/toolkit-commands/active/*Command.php');

foreach ($core as $path) {
	if (!is_file($path)) {
		fwrite(STDERR, "Missing required command file: $path\n");
		exit(1);
	}

	require_once $path;
}

if (!$active) {
	fwrite(STDERR, "No active toolkit commands found\n");
	exit(1);
}

foreach ($active as $path) {
	require_once $path;
	$class = 'Toolkit\\Commands\\' . basename($path, '.php');
	if (!class_exists($class) || !is_subclass_of($class, Toolkit\Commands\BaseCommand::class)) {
		fwrite(STDERR, "Invalid active command: $path\n");
		exit(1);
	}
}

$output = [];
$exitCode = 0;
exec(escapeshellarg(PHP_BINARY) . ' ' . escapeshellarg($root . '/toolkit') . ' --help', $output, $exitCode);
$help = preg_replace('/\x1B\[[0-9;]*m/', '', implode("\n", $output));
if ($exitCode !== 0) {
	fwrite(STDERR, "Toolkit help failed with exit code $exitCode\n");
	exit(1);
}

foreach ($active as $path) {
	$class = 'Toolkit\\Commands\\' . basename($path, '.php');
	$name = (new $class())->name();
	if (!preg_match('/^\s+' . preg_quote($name, '/') . '\s/m', $help)) {
		fwrite(STDERR, "Active command missing from help: $name\n");
		exit(1);
	}
}

if (preg_match('/^\s+shell\s/m', $help)) {
	fwrite(STDERR, "Disabled shell command appears in help\n");
	exit(1);
}

fwrite(STDOUT, "command class structure ok\n");
