<?php

namespace Hoathis\Lua\Model\Environment;

use Hoathis\Lua\Model\Value;

/**
 * Description of Basic
 *
 * @author Guislain Duthieuw
 */
class Basic extends \Hoathis\Lua\Model\Environment
{

    public function __construct($parent = null)
    {
        parent::__construct($parent);

        $this->set('_VERSION', (new Value\LuaString('Lua 5.3')));
        $this->set('assert', new Value\Closure($this->unimplemented('assert')));
        $this->set('collectgarbage', new Value\Closure($this->unimplemented('collectgarbage')));
        $this->set('dofile', new Value\Closure($this->unimplemented('dofile')));
        $this->set('error', new Value\Closure($this->unimplemented('error')));
        $this->set('getmetatable', new Value\Closure($this->unimplemented('getmetatable')));
        $this->set('ipairs', new Value\Closure($this->unimplemented('ipairs')));
        $this->set('load', new Value\Closure($this->unimplemented('load')));
        $this->set('loadfile', new Value\Closure($this->unimplemented('loadfile')));
        $this->set('next', new Value\Closure($this->unimplemented('next')));
        $this->set('pairs', new Value\Closure($this->unimplemented('pairs')));
        $this->set('pcall', new Value\Closure($this->unimplemented('pcall')));
        $this->set('print', new Value\Closure([$this, 'stdPrint']));
        $this->set('rawequal', new Value\Closure($this->unimplemented('rawequal')));
        $this->set('rawget', new Value\Closure($this->unimplemented('rawget')));
        $this->set('rawlen', new Value\Closure($this->unimplemented('rawlen')));
        $this->set('rawset', new Value\Closure($this->unimplemented('rawset')));
        $this->set('select', new Value\Closure($this->unimplemented('select')));
        $this->set('setmetatable', new Value\Closure($this->unimplemented('setmetatable')));
        $this->set('tonumber', new Value\Closure($this->unimplemented('tonumber')));
        $this->set('tostring', new Value\Closure($this->unimplemented('tostring')));
        $this->set('type', new Value\Closure([$this, 'type']));
        $this->set('xpcall', new Value\Closure($this->unimplemented('xpcall')));
        ;
    }

    public function stdPrint()
    {
        /**
         * @link http://www.lua.org/manual/5.3/manual.html#pdf-print Lua 5.3 Manual § 6.1 – Basic Functions - print (···)
         * @lua Receives any number of arguments and prints their values to stdout
         */
        $args = func_get_args();
        $sep  = '';
        foreach ($args as $arg) {
            echo $sep;
            echo $arg;
            $sep = "\t";
        }
        echo "\n";
    }

    public function type(\Hoathis\Lua\Model\Value $value)
    {
        /**
         * This lib does NOT support thread and userdata
         * @link http://www.lua.org/manual/5.3/manual.html#pdf-type Lua 5.3 Manual § 6.1 – Basic Functions - type (v)
         * @lua Returns the type of its only argument, coded as a string. The possible results of this function are "nil" (a string, not the value nil), "number", "string", "boolean", "table", "function", "thread", and "userdata". 
         */
        return new \Hoathis\Lua\Model\Value\LuaString($value::TYPE);
    }

    public function unimplemented($name)
    {
        return function () use ($name) {
            throw new \Hoathis\Lua\Exception\Interpreter(
            'Function %s is not yet implemented.', 2, $name);
        };
    }
}