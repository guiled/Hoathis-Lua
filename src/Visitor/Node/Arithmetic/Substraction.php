<?php

namespace Hoathis\Lua\Visitor\Node\Arithmetic;

/**
 * Description of Addition
 *
 * @author Guislain Duthieuw
 */
class Substraction extends \Hoathis\Lua\Visitor\Node
{

    public function visit(\Hoa\Visitor\Element $element, &$handle = null, $eldnah = null)
    {
        $children = $element->getChildren();
        $parent   = $element->getParent();

        $child0 = $this->getValue($children[0]->accept($this->interpreter, $handle, $eldnah));
        $child1 = $this->getValue($children[1]->accept($this->interpreter, $handle, $eldnah));

        return new \Hoathis\Lua\Model\Value\Number($child0->toPHP() - $child1->toPHP());
    }
}