<?php
if ($argc < 2) {
    echo "Lua compiler through PHP\nUsage : php lua.php \"<lua code>\"\n";
    exit();
}

require_once __DIR__ . DIRECTORY_SEPARATOR . 'vendor/autoload.php';
/**/
$compiler = \Hoa\Compiler\Llk::load(
    new \Hoa\File\Read('Grammar.pp')
);
$visitor  = new \Hoathis\Lua\Visitor\Interpreter();
$dump = new \Hoa\Compiler\Visitor\Dump();

$input    = $argv[1];
$start = microtime(true);
$ast      = $compiler->parse($input);
echo 'OK';
echo $dump->visit($ast);
(new \Hoathis\Lua\Visitor\LL2LR)->visit($ast);
echo $dump->visit($ast);
echo 'Parsed in ', round(1000 * (microtime(true) - $start)) . "ms\n";


$start = microtime(true);
$visitor->visit($ast);
echo 'Executed in ', round(1000 * (microtime(true) - $start)) . "ms\n";
/**/
/*
use Hoa\Compiler\Llk\Lexer as HoaLexer;
use Hoa\Compiler\Llk\Llk;
use Railt\Compiler\Grammar\Reader;
use Railt\Compiler\Lexer as RailtLexer;
use Railt\Io\File;
require __DIR__ . '/vendor/autoload.php';
$grammar = File::fromPathname('Grammar.pp');
$sources = $argv[1];

echo $grammar->getContents();

$compiler = \Railt\Compiler\Parser::fromGrammar($grammar);
exit();
$visitor  = new \Hoathis\Lua\Visitor\Interpreter();
$dump = new \Hoa\Compiler\Visitor\Dump();

$input    = $argv[1];
$start = microtime(true);
$ast      = $compiler->parse($input);
echo $dump->visit($ast);
(new \Hoathis\Lua\Visitor\LL2LR)->visit($ast);
//echo $dump->visit($ast);
echo 'Parsed in ', round(1000 * (microtime(true) - $start)) . "ms\n";


$start = microtime(true);
$visitor->visit($ast);
echo 'Executed in ', round(1000 * (microtime(true) - $start)) . "ms\n";

 */