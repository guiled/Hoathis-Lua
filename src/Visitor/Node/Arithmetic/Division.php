<?php

namespace Hoathis\Lua\Visitor\Node\Arithmetic;

/**
 * Description of Division
 *
 * @author Guislain Duthieuw
 */
class Division extends \Hoathis\Lua\Visitor\Node
{

    public function visit(\Hoa\Visitor\Element $element, &$handle = null, $eldnah = null)
    {
        $children = $element->getChildren();

        $child0 = $this->getValue($children[0]->accept($this->interpreter, $handle, $eldnah));
        $child1 = $this->getValue($children[1]->accept($this->interpreter, $handle, $eldnah));

        if (0 == $child1->toPHP()) {
            if (0 == $child0->toPHP()) {
                /**
                 * @link http://www.lua.org/manual/5.3/manual.html#2.1 Lua 5.3 Manual § 2.1 – Values and Types
                 * @lua Not a Number is a special value used to represent undefined or unrepresentable numerical results, such as 0/0.
                 */
                return new \Hoathis\Lua\Model\Value\NaN();
            } else {
                /**
                 * @lua 1/0 gives inf, -1/0 gives -inf (no manual reference)
                 */
                return new \Hoathis\Lua\Model\Value\Inf($child0->toPHP());
            }
        }
        return new \Hoathis\Lua\Model\Value\Number($child0->toPHP() / $child1->toPHP());
    }
}