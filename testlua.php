<?php
require '../../Hoa/Core/Core.php';

from('Hoa')
-> import('File.Read')
-> import('Compiler.Llk.~');

from('Hoathis')
-> import('Lua.Visitor.Interpreter');
$compiler = \Hoa\Compiler\Llk::load(
    new \Hoa\File\Read('Grammar.pp')
);
$input    = 'b=2;print(b+1);';
$ast      = $compiler->parse($input);
$visitor  = new \Hoathis\Lua\Visitor\Interpreter();

class A {
    public $a;
}
$a = new A();
$a->a = 1;
$a = ['a' => 1];
$b = 2;
$env = $visitor->getRoot();
$env->wrap('a', $a);
$env->wrap('b', $b);
//var_dump($a,$b);
$visitor->visit($ast);
//var_dump($a,$b);