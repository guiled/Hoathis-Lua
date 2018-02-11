<?php

namespace Hoathis\Lua\Visitor\Node;

/**
 * Description of Block
 *
 * @author Guislain Duthieuw
 */
class Arguments extends \Hoathis\Lua\Visitor\Node
{
    protected $interpreter;

    public function getInterpreter()
    {
        return $this->interpreter;
    }

    public function setInterpreter($interpreter)
    {
        $this->interpreter = $interpreter;
        return $this;
    }

    /**
     *
     * @param \Hoa\Visitor\Element $element
     * @param type $handle
     * @param type $eldnah
     * @return \Hoathis\Lua\Model\BreakStatement
     * @throws \Hoathis\Lua\Exception\Interpreter
     */
    public function visit(\Hoa\Visitor\Element $element, &$handle = null, $eldnah = null)
    {
        $children = $element->getChildren();
        $result = [];

        if (false === empty($children)) {
            $result[] = $children[0]->accept($this->interpreter, $handle, \Hoathis\Lua\Visitor\Interpreter::AS_VALUE);
        }

        return $result;
    }
}