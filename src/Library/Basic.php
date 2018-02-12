<?php

namespace Hoathis\Lua\Library;

/**
 * Description of Basic
 *
 * @author Guislain Duthieuw
 */
class Basic
{

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
}