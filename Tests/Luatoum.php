<?php

namespace Hoathis\Lua\Tests;

use atoum;

class Luatoum extends atoum\test
{

    public function __construct()
    {
        parent::__construct();

        $generator = $this->getAsserterGenerator();
        $factory   = function() use ($generator) {
            static $lua;
            if (null === $lua) {
                $lua = new Lua($generator);
            }

            return $lua;
        };

        $this->getAssertionManager()
            ->setHandler('lua', function($code = null) use ($factory) {
                $lua = $factory();
                $lua->setWithTest($this);
                if (!empty($code)) {
                    $lua->code($code);
                }
                return $lua;
            })
            /*->setHandler('code', function($code) use ($factory) {
				return $factory()->code($code);
            })*/
        ;
    }
}