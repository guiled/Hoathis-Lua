<?php

namespace Hoathis\Lua\Model\Library;

use Hoathis\Lua\Model\Value;

/**
 * Description of Math
 *
 * @author houra
 */
class Math extends Value\Table
{

    public function __construct($value = null)
    {
        parent::__construct($value);
        $this->set('mininteger', new Value\Number(PHP_INT_MIN));
        $this->set('maxinteger', new Value\Number(PHP_INT_MAX));
        $this->set('pi', new Value\Number(pi()));
        $this->set('huge', new Value\Inf());
    }

    public function abs(Value $x)
    {
        throw new \Hoathis\Lua\Exception\Interpreter(
        'Function %s.%s is not yet implemented.', 2, [strtolower(substr(strrchr(__CLASS__, "\\"), 1)), __FUNCTION__]);
    }

    public function acos(\Hoathis\Lua\Model\Value $x)
    {
        throw new \Hoathis\Lua\Exception\Interpreter(
        'Function %s.%s is not yet implemented.', 2, [strtolower(substr(strrchr(__CLASS__, "\\"), 1)), __FUNCTION__]);
    }

    public function asin(\Hoathis\Lua\Model\Value $x)
    {
        throw new \Hoathis\Lua\Exception\Interpreter(
        'Function %s.%s is not yet implemented.', 2, [strtolower(substr(strrchr(__CLASS__, "\\"), 1)), __FUNCTION__]);
    }

    public function atan(\Hoathis\Lua\Model\Value $y, \Hoathis\Lua\Model\Value $x = null)
    {
        throw new \Hoathis\Lua\Exception\Interpreter(
        'Function %s.%s is not yet implemented.', 2, [strtolower(substr(strrchr(__CLASS__, "\\"), 1)), __FUNCTION__]);
    }

    public function atan2(\Hoathis\Lua\Model\Value $y, s\Hoathis\Lua\Model\Value $x = null)
    {
        throw new \Hoathis\Lua\Exception\Interpreter(
        'Function %s.%s is not yet implemented.', 2, [strtolower(substr(strrchr(__CLASS__, "\\"), 1)), __FUNCTION__]);
    }

    public function ceil(\Hoathis\Lua\Model\Value $x)
    {
        throw new \Hoathis\Lua\Exception\Interpreter(
        'Function %s.%s is not yet implemented.', 2, [strtolower(substr(strrchr(__CLASS__, "\\"), 1)), __FUNCTION__]);
    }

    public function cos(\Hoathis\Lua\Model\Value $x)
    {
        throw new \Hoathis\Lua\Exception\Interpreter(
        'Function %s.%s is not yet implemented.', 2, [strtolower(substr(strrchr(__CLASS__, "\\"), 1)), __FUNCTION__]);
    }

    public function cosh(\Hoathis\Lua\Model\Value $x)
    {
        throw new \Hoathis\Lua\Exception\Interpreter(
        'Function %s.%s is not yet implemented.', 2, [strtolower(substr(strrchr(__CLASS__, "\\"), 1)), __FUNCTION__]);
    }

    public function deg(\Hoathis\Lua\Model\Value $x)
    {
        throw new \Hoathis\Lua\Exception\Interpreter(
        'Function %s.%s is not yet implemented.', 2, [strtolower(substr(strrchr(__CLASS__, "\\"), 1)), __FUNCTION__]);
    }

    public function exp(\Hoathis\Lua\Model\Value $x)
    {
        throw new \Hoathis\Lua\Exception\Interpreter(
        'Function %s.%s is not yet implemented.', 2, [strtolower(substr(strrchr(__CLASS__, "\\"), 1)), __FUNCTION__]);
    }

    public function floor(\Hoathis\Lua\Model\Value $x)
    {
        throw new \Hoathis\Lua\Exception\Interpreter(
        'Function %s.%s is not yet implemented.', 2, [strtolower(substr(strrchr(__CLASS__, "\\"), 1)), __FUNCTION__]);
    }

    public function fmod(\Hoathis\Lua\Model\Value $x, \Hoathis\Lua\Model\Value $y)
    {
        throw new \Hoathis\Lua\Exception\Interpreter(
        'Function %s.%s is not yet implemented.', 2, [strtolower(substr(strrchr(__CLASS__, "\\"), 1)), __FUNCTION__]);
    }

    public function ldexp(\Hoathis\Lua\Model\Value $x)
    {
        throw new \Hoathis\Lua\Exception\Interpreter(
        'Function %s.%s is not yet implemented.', 2, [strtolower(substr(strrchr(__CLASS__, "\\"), 1)), __FUNCTION__]);
    }

    public function log(\Hoathis\Lua\Model\Value $x, \Hoathis\Lua\Model\Value $base)
    {
        throw new \Hoathis\Lua\Exception\Interpreter(
        'Function %s.%s is not yet implemented.', 2, [strtolower(substr(strrchr(__CLASS__, "\\"), 1)), __FUNCTION__]);
    }

    public function log10(\Hoathis\Lua\Model\Value $value)
    {
        return $this->log($value, 10);
    }

    public function max(\Hoathis\Lua\Model\Value $x, ...$values)
    {
        throw new \Hoathis\Lua\Exception\Interpreter(
        'Function %s.%s is not yet implemented.', 2, [strtolower(substr(strrchr(__CLASS__, "\\"), 1)), __FUNCTION__]);
    }

    public function min(\Hoathis\Lua\Model\Value $x, ...$values)
    {
        throw new \Hoathis\Lua\Exception\Interpreter(
        'Function %s.%s is not yet implemented.', 2, [strtolower(substr(strrchr(__CLASS__, "\\"), 1)), __FUNCTION__]);
    }

    public function modf(\Hoathis\Lua\Model\Value $x)
    {
        throw new \Hoathis\Lua\Exception\Interpreter(
        'Function %s.%s is not yet implemented.', 2, [strtolower(substr(strrchr(__CLASS__, "\\"), 1)), __FUNCTION__]);
    }

    public function pow(\Hoathis\Lua\Model\Value $x, \Hoathis\Lua\Model\Value $y)
    {
        throw new \Hoathis\Lua\Exception\Interpreter(
        'Function %s.%s is not yet implemented.', 2, [strtolower(substr(strrchr(__CLASS__, "\\"), 1)), __FUNCTION__]);
    }

    public function rad(\Hoathis\Lua\Model\Value $x)
    {
        throw new \Hoathis\Lua\Exception\Interpreter(
        'Function %s.%s is not yet implemented.', 2, [strtolower(substr(strrchr(__CLASS__, "\\"), 1)), __FUNCTION__]);
    }

    public function random(\Hoathis\Lua\Model\Value $m, \Hoathis\Lua\Model\Value $n)
    {
        throw new \Hoathis\Lua\Exception\Interpreter(
        'Function %s.%s is not yet implemented.', 2, [strtolower(substr(strrchr(__CLASS__, "\\"), 1)), __FUNCTION__]);
    }

    public function randomseed(\Hoathis\Lua\Model\Value $x)
    {
        throw new \Hoathis\Lua\Exception\Interpreter(
        'Function %s.%s is not yet implemented.', 2, [strtolower(substr(strrchr(__CLASS__, "\\"), 1)), __FUNCTION__]);
    }

    public function sin(\Hoathis\Lua\Model\Value $x)
    {
        throw new \Hoathis\Lua\Exception\Interpreter(
        'Function %s.%s is not yet implemented.', 2, [strtolower(substr(strrchr(__CLASS__, "\\"), 1)), __FUNCTION__]);
    }

    public function sinh(\Hoathis\Lua\Model\Value $x)
    {
        throw new \Hoathis\Lua\Exception\Interpreter(
        'Function %s.%s is not yet implemented.', 2, [strtolower(substr(strrchr(__CLASS__, "\\"), 1)), __FUNCTION__]);
    }

    public function sqrt(\Hoathis\Lua\Model\Value $x)
    {
        throw new \Hoathis\Lua\Exception\Interpreter(
        'Function %s.%s is not yet implemented.', 2, [strtolower(substr(strrchr(__CLASS__, "\\"), 1)), __FUNCTION__]);
    }

    public function tan(\Hoathis\Lua\Model\Value $x)
    {
        throw new \Hoathis\Lua\Exception\Interpreter(
        'Function %s.%s is not yet implemented.', 2, [strtolower(substr(strrchr(__CLASS__, "\\"), 1)), __FUNCTION__]);
    }

    public function tanh(\Hoathis\Lua\Model\Value $x)
    {
        throw new \Hoathis\Lua\Exception\Interpreter(
        'Function %s.%s is not yet implemented.', 2, [strtolower(substr(strrchr(__CLASS__, "\\"), 1)), __FUNCTION__]);
    }

    public function tointeger(\Hoathis\Lua\Model\Value $x)
    {
        throw new \Hoathis\Lua\Exception\Interpreter(
        'Function %s.%s is not yet implemented.', 2, [strtolower(substr(strrchr(__CLASS__, "\\"), 1)), __FUNCTION__]);
    }

    public function type(\Hoathis\Lua\Model\Value $x)
    {
        throw new \Hoathis\Lua\Exception\Interpreter(
        'Function %s.%s is not yet implemented.', 2, [strtolower(substr(strrchr(__CLASS__, "\\"), 1)), __FUNCTION__]);
    }

    public function ult(\Hoathis\Lua\Model\Value $m, \Hoathis\Lua\Model\Value $n)
    {
        throw new \Hoathis\Lua\Exception\Interpreter(
        'Function %s.%s is not yet implemented.', 2, [strtolower(substr(strrchr(__CLASS__, "\\"), 1)), __FUNCTION__]);
    }
}