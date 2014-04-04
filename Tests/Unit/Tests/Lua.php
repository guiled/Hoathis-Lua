<?php

namespace Hoathis\Lua\Tests\Unit\Tests;

use atoum;
use Hoathis\Lua\Tests\Lua as testedClass;

class Lua extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubClassOf('mageekguy\atoum\asserter');
	}

	public function testGetVisitor()
	{
		$this
			->if($lua = new testedClass())
			->then
				->object($visitor = $lua->getVisitor())->isInstanceOf('Hoathis\Lua\Visitor\Interpreter')
				->object($lua->getVisitor())->isIdenticalTo($visitor)
		;
	}

	public function testGetCompiler()
	{
		$this
			->if($lua = new testedClass())
			->then
				->object($compiler = $lua->getCompiler())->isInstanceOf('Hoa\Compiler\Llk\Parser')
				->object($lua->getCompiler())->isIdenticalTo($compiler)
		;
	}
}
