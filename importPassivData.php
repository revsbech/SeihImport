<?php


require_once('PassivImporter.php');

if ($argc != 2) {
	print 'Usage: php ' . $argv[0] . ' <DIRECTORY>' . PHP_EOL;
	exit();
}
$directory = $argv[1];

$importer = new PassivImporter();
$importer->importFromDirectory($directory);
