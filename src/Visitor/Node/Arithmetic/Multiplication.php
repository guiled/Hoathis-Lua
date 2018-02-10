<?php

namespace Hoathis\Lua\Visitor\Node\Arithmetic;

/**
 * Description of Multiplication
 *
 * @author Guislain Duthieuw
 */
class Multiplication extends \Hoathis\Lua\Visitor\Node
{

    public function visit(\Hoa\Visitor\Element $element, &$handle = null, $eldnah = null)
    {
        $children = $element->getChildren();

        $child0 = $this->getValue($children[0]->accept($this->interpreter, $handle, $eldnah));
        $child1 = $this->getValue($children[1]->accept($this->interpreter, $handle, $eldnah));

        return new \Hoathis\Lua\Model\Value\Number($child0->toPHP() * $child1->toPHP());
    }
}