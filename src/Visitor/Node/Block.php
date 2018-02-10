<?php

namespace Hoathis\Lua\Visitor\Node;

/**
 * Description of Block
 *
 * @author Guislain Duthieuw
 */
class Block extends \Hoathis\Lua\Visitor\Node
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

        $nbchildren = count($children);
        for ($i = 0; $i < $nbchildren; $i++) {
            $val = $children[$i]->accept($this->interpreter, $handle, $eldnah);
            if ($val instanceof \Hoathis\Lua\Model\ReturnedValue) {
                $parent = $element->getParent();
                if (true === is_null($parent)) {
                    $returnedValue = $val->getValue()->getPHPValue();
                } else {
                    $returnedValue = $val;
                }
                break;
            } elseif ($val instanceof \Hoathis\Lua\Model\BreakStatement) {
                $parent = $element->getParent();
                if (true === is_null($parent)) {
                    throw new \Hoathis\Lua\Exception\Interpreter(
                    'Break found outside of loop.', 1);
                } else {
                    $returnedValue = $val;
                }
                break;
            }
        }
        if (true === isset($oldEnvironment)) {
            $this->_environment = $oldEnvironment;
        }
        if (true === isset($returnedValue)) {
            return $returnedValue;
        }
    }
}