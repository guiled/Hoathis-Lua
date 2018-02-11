<?php

namespace Hoathis\Lua\Visitor;

/**
 * Description of LL2LR
 *
 * @author houra
 */
class LL2LR implements \Hoa\Visitor\Visit
{

    public function visit(\Hoa\Visitor\Element $element, &$handle = null, $eldnah = null)
    {
        /* @var $element \Hoa\Compiler\Llk\TreeNode */
        $children = $element->getChildren();
        foreach ($children as $child) {
            $child->accept($this, $handle, $eldnah);
        }

        /**
         * @link http://www.lua.org/manual/5.3/manual.html#3.4.8 Lua 5.3 Manual § 3.4.8 – Precedence
         * @lua The concatenation ('..') and exponentiation ('^') operators are right associative. All other binary operators are left associative.
         */
        switch ($element->getId()) {
            // Division is not commutative, so order is important
            case '#division':
                if ($children[1]->getId() === '#multiplication') {
                    $this->switchInTree($element, $children[1]);
                }
                break;

            // Substraction is not commutative, so order is important
            case '#substraction':
                if ($children[1]->getId() === '#addition' || $children[1]->getId() === '#substraction') {
                    $this->switchInTree($element, $children[1]);
                }
                break;
        }
    }

    protected function switchInTree(\Hoa\Compiler\Llk\TreeNode $element, \Hoa\Compiler\Llk\TreeNode $child)
    {
        $parent   = $element->getParent();
        $siblings = $parent->getChildren();
        $idx      = array_search($element, $siblings, true);
        if ($idx === false) {
            throw new Lua\Exception('Element is not a child of its own parent ');
        }
        $this->setChild($parent, $child, $idx);
        $this->setChild($element, $child->getChild(0), 1);
        $this->setChild($child, $element, 0);
    }

    protected function setChild(\Hoa\Compiler\Llk\TreeNode $element, \Hoa\Compiler\Llk\TreeNode $child, $index)
    {
        $children         = $element->getChildren();
        $children[$index] = $child;
        $element->setChildren($children);
        $child->setParent($element);
    }
}