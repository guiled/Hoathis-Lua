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

	public function testSetWith()
	{
		$this
			->if($lua = new testedClass())
			->then
				->object($lua->setWith('return nil;'))->isIdenticalTo($lua)
		;
	}

	public function testCode()
	{
		$this
			->if($lua = new testedClass())
			->then
				->object($lua->code('return nil;'))->isIdenticalTo($lua)
		;
	}

	public function testIsParsed()
	{
		$this
			->if($lua = new testedClass())
			->then
				->exception(function() use ($lua, & $code) {
					$lua->code($code = uniqid())->isParsed();
				})
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf('Lua Code "%s" can not be parsed', $code))
				->object($lua->code('')->isParsed())->isIdenticalTo($lua)
		;
	}

	public function testIsNotParsed()
	{
		$this
			->if($lua = new testedClass())
			->then
				->exception(function() use ($lua, & $code) {
					$lua->code($code = 'return nil;')->isNotParsed();
				})
					->isInstanceOf('mageekguy\atoum\asserter\exception')
					->hasMessage(sprintf('Lua Code "%s" can be parsed', $code))
				->object($lua->code(uniqid())->isNotParsed())->isIdenticalTo($lua)
		;
	}

	public function testExecutionEnv()
	{
		$this
			->if($lua = new testedClass())
			->then
				->object($env = $lua->getVisitor()->getRoot())->isInstanceOf('Hoathis\Lua\Model\Environment')
				->object($lua->code('return nil;'))->isIdenticalTo($lua)
				->object($lua->getVisitor()->getRoot())->isIdenticalTo($env)
				->object($lua->setWith('return nil;'))->isIdenticalTo($lua)
				->object($lua->getVisitor()->getRoot())
					->isNotIdenticalTo($lua)
					->isInstanceOf('Hoathis\Lua\Model\Environment')
		;
	}
}
