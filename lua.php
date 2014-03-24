<?php
if ($argc < 2) {
    echo "Lua compiler through PHP\nUsage : php lua.php \"<lua code>\"\n";
    exit();
}
require '../../Hoa/Core/Core.php';

from('Hoa')
-> import('File.Read')
-> import('Compiler.Llk.~');

from('Hoathis')
-> import('Lua.Visitor.Interpreter');

$compiler = \Hoa\Compiler\Llk::load(
    new \Hoa\File\Read('Grammar.pp')
);
$visitor  = new \Hoathis\Lua\Visitor\Interpreter();

$input    = $argv[1];
$start = microtime(true);
$ast      = $compiler->parse($input);
echo 'Parsed in ', round(1000 * (microtime(true) - $start)) . "ms\n";


$start = microtime(true);
$visitor->visit($ast);
echo 'Executed in ', round(1000 * (microtime(true) - $start)) . "ms\n";
