<?php

define('USE_COMPOSER', is_dir(dirname(__DIR__) . DIRECTORY_SEPARATOR . 'vendor'));

if (USE_COMPOSER) {
	require_once dirname(__DIR__) . '/vendor/autoload.php';
} else {
	require_once dirname(__DIR__) . '/autoload.php';
	require_once __DIR__ . '/Lua.php';
	require_once __DIR__ . '/Luatoum.php';
}
