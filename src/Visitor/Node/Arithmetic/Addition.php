<?php

namespace Hoathis\Lua\Visitor\Node\Arithmetic;

/**
 * Description of Addition
 *
 * @author Guislain Duthieuw
 */
class Addition extends \Hoathis\Lua\Visitor\Node
{

    public function visit(\Hoa\Visitor\Element $element, &$handle = null, $eldnah = null)
    {
        $children = $element->getChildren();
        $parent   = $element->getParent();

        $child0 = $this->getValue($children[0]->accept($this->interpreter, $handle, $eldnah));
        $child1 = $this->getValue($children[1]->accept($this->interpreter, $handle, $eldnah));

        /**
         * @link http://www.lua.org/manual/5.3/manual.html#3.4.8
         * @lua The concatenation ('..') and exponentiation ('^') operators are right associative. All other binary operators are left associative.
         */
        if (null !== $parent && '#substraction' === $parent->getId()) {
            return new \Hoathis\Lua\Model\Value\Number($child0->toPHP() - $child1->toPHP());
        }

        return new \Hoathis\Lua\Model\Value\Number($child0->toPHP() + $child1->toPHP());
    }
}