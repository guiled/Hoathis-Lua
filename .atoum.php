<?php

if (defined('USE_COMPOSER') === false) {
	define('USE_COMPOSER', is_dir(__DIR__ . DIRECTORY_SEPARATOR . 'vendor'));
}

if (USE_COMPOSER) {
	require_once __DIR__ . '/vendor/autoload.php';
} else {
	require_once __DIR__ . '/Tests/Autoload.php';
}

$runner
	->setBootstrapFile(__DIR__ . DIRECTORY_SEPARATOR . '.bootstrap.atoum.php')
	->addTestsFromDirectory(__DIR__ . DIRECTORY_SEPARATOR . 'Tests/Unit')
;
$script->noCodeCoverageForNamespaces('mageekguy');
