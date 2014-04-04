<?php

if (defined('USE_COMPOSER') === false) {
	define('USE_COMPOSER', is_dir(__DIR__ . DIRECTORY_SEPARATOR . 'vendor'));
}

if (USE_COMPOSER) {
	require_once __DIR__ . '/vendor/autoload.php';
} else {
	require_once __DIR__ . '/autoload.php';
}

$runner->addTestsFromDirectory(__DIR__ . DIRECTORY_SEPARATOR . 'Tests/Unit');
$script->noCodeCoverageForNamespaces('mageekguy');
