<?php

require '../../Hoa/Core/Core.php';

from('Hoa')
    ->import('File.Read')
    ->import('Compiler.Llk.~');

from('Hoathis')
    ->import('Lua.Visitor.Interpreter');
$compiler = \Hoa\Compiler\Llk::load(
        new \Hoa\File\Read('Grammar.pp')
);
$input = implode(' ', array_slice($argv, 1));
$ast = null;
try {
    $ast = $compiler->parse($input);
} catch (\Hoa\Compiler\Exception $e) {
    echo 'Impossible de parser : ', $e->getMessage();
}
if ($ast) {
    $visitor = new \Hoathis\Lua\Visitor\Interpreter();
    try {
        $visitor->visit($ast);
    } catch (\Hoathis\Lua\Exception\Interpreter $e) {
        echo 'Erreur à l\'exécution : ', $e->getMessage();
    }
}
