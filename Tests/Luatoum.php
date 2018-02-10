<?php

namespace Hoathis\Lua\Tests;

use atoum;

class Luatoum extends atoum\test
{
    protected $lua;

    public function __construct()
    {
        parent::__construct();

        $generator = $this->getAsserterGenerator();
        $factory   = function() use ($generator) {
            if (null === $this->lua) {
                $this->lua = new Lua($generator);
            }

            return $this->lua;
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

    public function startCase($case)
    {
        if (null !== $this->lua) {
            $this->lua->reset();
        }
        return parent::startCase($case);
    }
}