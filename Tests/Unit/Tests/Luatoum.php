<?php

namespace Hoathis\Lua\Tests\Unit\Tests;

use atoum;
use Hoathis\Lua\Tests\Luatoum as testedClass;

class Luatoum extends atoum\test
{
	public function testClass()
	{
		$this->testedClass->isSubClassOf('mageekguy\atoum\test');
	}

	public function testLua()
	{
		$this
			->if($luatoum = new testedClass())
			->then
				->object($lua = $luatoum->lua('return nil;'))->isInstanceOf('Hoathis\Lua\Tests\Lua')
				->object($luatoum->lua('return nil;'))->isIdenticalTo($lua)
		;
	}

	public function testCode()
	{
		$this
			->if($luatoum = new testedClass())
			->then
				->object($lua = $luatoum->lua('return nil;'))->isInstanceOf('Hoathis\Lua\Tests\Lua')
				->object($luatoum->code('return nil;'))->isIdenticalTo($lua)
		;
	}
}
