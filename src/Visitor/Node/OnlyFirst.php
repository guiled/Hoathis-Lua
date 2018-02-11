<?php

namespace Hoathis\Lua\Visitor\Node;

/**
 * Description of OnlyFirst
 *
 * @author houra
 */
class OnlyFirst extends \Hoathis\Lua\Visitor\Node
{

    public function getHandledNodes() {
        return ['#onlyfirst'];
    }

    public function visit(\Hoa\Visitor\Element $element, &$handle = null, $eldnah = null)
    {
        /**
         * @link http://www.lua.org/manual/5.3/manual.html#3.4.8
         * @lua As usual, you can use parentheses to change the precedences of an expression.
         */
        $children = $element->getChildren();
        $childValue = $children[0]->accept($this->interpreter, $handle, $eldnah);
        /**
         * @link http://www.lua.org/manual/5.3/manual.html#3.4
         * @lua Any expression enclosed in parentheses always results in only one value. Thus, (f(x,y,z)) is always a single value, even if f returns several values. (The value of (f(x,y,z)) is the first value returned by f or nil if f does not return any values.)
         */
        if ($childValue instanceof \Hoathis\Lua\Model\ValueGroup) {
            // TODO gérer les groupes
            //$values = $childValue->getValue();
            //return $values[0];
        } else {
            return $childValue;
        }
    }
}