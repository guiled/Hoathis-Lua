<?php

namespace Hoathis\Lua\Tests;

use atoum;

class Luatoum extends atoum\test
{
    public function __construct()
	{
        parent::__construct();

		$lua = null;
		$generator = $this->getAsserterGenerator();
		$factory = function() use (& $lua, $generator) {
			if (null === $lua) {
				$lua = new Lua($generator);
			}

			return $lua;
		};

		$this->getAssertionManager()
			->setHandler('lua', function($code) use ($factory) {
				return $factory()->setWith($code);
			})
			->setHandler('code', function($code) use ($factory) {
				return $factory()->code($code);
			});
    }

}
