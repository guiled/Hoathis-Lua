<?php
if ($argc < 2) {
    echo "Lua compiler through PHP\nUsage : php lua.php \"<lua code>\"\n";
    exit();
}

require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor/autoload.php';

$compiler = \Hoa\Compiler\Llk::load(
    new \Hoa\File\Read('Grammar.pp')
);
$visitor  = new \Hoathis\Lua\Visitor\Interpreter();

$input    = $argv[1];
$start = microtime(true);
$ast      = $compiler->parse($input);
(new \Hoathis\Lua\Visitor\LL2LR)->visit($ast);
echo 'Parsed in ', round(1000 * (microtime(true) - $start)) . "ms\n";


$start = microtime(true);
$visitor->visit($ast);
echo 'Executed in ', round(1000 * (microtime(true) - $start)) . "ms\n";
