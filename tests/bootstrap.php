<?php

require '../../Hoa/Core/Core.php';
require 'Luatoum.php';

from('Hoa')
-> import('File.Read')
-> import('Compiler.Llk.~');

from('Hoathis')
-> import('Lua.Visitor.Interpreter');

$compiler = \Hoa\Compiler\Llk::load(
    new \Hoa\File\Read('Grammar.pp')
);