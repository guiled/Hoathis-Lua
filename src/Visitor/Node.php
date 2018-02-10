<?php

namespace Hoathis\Lua\Visitor;

/**
 * Description of Block
 *
 * @author Guislain Duthieuw
 */
abstract class Node implements \Hoa\Visitor\Visit
{
    /**
     *
     * @var Hoathis\Lua\Visitor\Interpreter
     */
    protected $interpreter;

    /**
     *
     * @return Hoathis\Lua\Visitor\Interpreter
     */
    public function getInterpreter()
    {
        return $this->interpreter;
    }

    public function setInterpreter($interpreter)
    {
        $this->interpreter = $interpreter;
        return $this;
    }

    protected function getValue($identifier)
    {
        if (is_string($identifier)) {
            return $this->interpreter->getEnvironment()->get($identifier);
        } elseif (is_array($identifier)) {
            return array_map([$this,'getValue'], $identifier);
        }
        return $identifier;
    }
}