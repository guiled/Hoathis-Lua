<?php

require_once '../../Hoa/Core/Core.php';

from('Hoa')
	-> import('File.Read')
	-> import('Compiler.Llk.~');

from('Hoathis')
	-> import('Lua.Visitor.Interpreter');
