<?php

namespace Hoathis\Lua\Visitor\Node;

/**
 * Description of Comparison
 *
 * @author houra
 */
class Comparison extends \Hoathis\Lua\Visitor\Node
{

    public function getHandledNodes() {
        return ['#comparison'];
    }

    public function visit(\Hoa\Visitor\Element $element, &$handle = null, $eldnah = null)
    {
        $children = $element->getChildren();
        $val1       = $this->getValue($children[0]->accept($this->interpreter, $handle, $eldnah));
        $comparison = $children[1]->getValueToken();
        $val2       = $this->getValue($children[2]->accept($this->interpreter, $handle, $eldnah));
        switch ($comparison) {
            case 'dequal':
                return new \Hoathis\Lua\Model\Value\Boolean($val1->toPHP() === $val2->toPHP());
            /*case 'nequal':
                return new \Hoathis\Lua\Model\Value($val1->getValue() !== $val2->getValue());
            case 'lt':
                if (is_numeric($val1->getValue()) && is_numeric($val2->getValue()) || is_string($val1->getValue()) && is_string($val2->getValue())) {
                    return new \Hoathis\Lua\Model\Value($val1->getValue() < $val2->getValue());
                } // TODO must manage when comparing two tables
                break;
            case 'gt':
                if (is_numeric($val1->getValue()) && is_numeric($val2->getValue()) || is_string($val1->getValue()) && is_string($val2->getValue())) {
                    return new \Hoathis\Lua\Model\Value($val1->getValue() > $val2->getValue());
                } // TODO must manage when comparing two tables
                break;
            case 'lte':
                if (is_numeric($val1->getValue()) && is_numeric($val2->getValue()) || is_string($val1->getValue()) && is_string($val2->getValue())) {
                    return new \Hoathis\Lua\Model\Value($val1->getValue() <= $val2->getValue());
                }
                // TODO must manage when comparing two tables

                break;
            case 'gte':
                if (is_numeric($val1->getValue()) && is_numeric($val2->getValue()) || is_string($val1->getValue()) && is_string($val2->getValue())) {
                    return new \Hoathis\Lua\Model\Value($val1->getValue() >= $val2->getValue());
                } // TODO must manage when comparing two tables

                break;*/
            default:
                throw new \Hoathis\Lua\Exception\Interpreter(
                'Comparison operator %s is not yet implemented.', 1, $comparison);
        }
    }
}