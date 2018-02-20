<?php

namespace Hoathis\Lua\Visitor;

/**
 * Description of LL2LR
 *
 * @author houra
 */
class LL2LR implements \Hoa\Visitor\Visit
{

    protected $precedences = [
        '#division' => 1,
        '#floordivision' => 1,
        '#multiplication' => 1,
        '#modulo' => 1,
        '#substraction' => 2,
        '#addition' => 2
    ];

    public function visit(\Hoa\Visitor\Element $element, &$handle = null, $eldnah = null)
    {
        /* @var $element \Hoa\Compiler\Llk\TreeNode */
        $root = $element;
        $children = $root->getChildren();
        //echo 'Visit : ', $root->getId(), (isset($this->precedences[$root->getId()]) ? ' Precedence : ' . $this->precedences[$root->getId()] : ''), PHP_EOL;
        /**
         * @link http://www.lua.org/manual/5.3/manual.html#3.4.8 Lua 5.3 Manual § 3.4.8 – Precedence
         * @lua The concatenation ('..') and exponentiation ('^') operators are right associative. All other binary operators are left associative.
         */
        while ($this->isNodePrecedenceError($root)) {
            //echo 'switch ', $root->getId(), '(', $children[0]->getValueValue(), ') with ', $children[1]->getId(), PHP_EOL;
            $this->switchInTree($root, $children[1]);
            //echo (new \Hoa\Compiler\Visitor\Dump)->visit($root->getParent());
            $root = $children[1];
            $children = $root->getChildren();
        }

        foreach ($children as $child) {
            $child->accept($this, $handle, $eldnah);
        }

    }

    protected function isNodePrecedenceError(\Hoa\Compiler\Llk\TreeNode $element)
    {
        if (!array_key_exists($element->getId(), $this->precedences)) {
            return false;
        }
        return array_key_exists($element->getChild(1)->getId(), $this->precedences)
            && $this->precedences[$element->getId()] === $this->precedences[$element->getChild(1)->getId()];
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