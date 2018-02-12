<?php

namespace Hoathis\Lua\Visitor\Node;

/**
 * Description of Arithmetics
 *
 * @author houra
 */
class Arithmetics extends \Hoathis\Lua\Visitor\Node
{

    public function getHandledNodes()
    {
        return [
            '#addition',
            '#substraction',
            '#multiplication',
            '#division',
            '#floordivision',
            '#power',
            '#negative'
        ];
    }

    /**
     * 
     * @param \Hoa\Visitor\Element $element
     * @param mixed $handle
     * @param mixed $eldnah
     * @return \Hoathis\Lua\Model\Value\Number|\Hoathis\Lua\Model\Value\NaN|\Hoathis\Lua\Model\Value\Inf
     */
    public function visit(\Hoa\Visitor\Element $element, &$handle = null, $eldnah = null)
    {
        /**
         * @link http://www.lua.org/manual/5.3/manual.html#3.4.1 Lua 5.3 Manual § 3.4.1 – Arithmetic Operators
         * @lua  Lua supports the following arithmetic operators: +: addition -: subtraction *: multiplication /: float division //: floor division %: modulo ^: exponentiation -: unary minus
         */
        $children = $element->getChildren();
        $values   = [];

        foreach ($children as $child) {
            $values[] = $this->getValue($child->accept($this->interpreter, $handle, $eldnah))->toPHP();
        }
        switch ($element->getId()) {
            case '#addition':
                $value = $values[0] + $values[1];
                break;
            case '#substraction':
                $value = $values[0] - $values[1];
                break;
            case '#multiplication':
                $value = $values[0] * $values[1];
                break;
            case '#division':
                if (0 == $values[1]) {
                    if (0 == $values[0]) {
                        /**
                         * @link http://www.lua.org/manual/5.3/manual.html#2.1 Lua 5.3 Manual § 2.1 – Values and Types
                         * @lua Not a Number is a special value used to represent undefined or unrepresentable numerical results, such as 0/0.
                         */
                        return new \Hoathis\Lua\Model\Value\NaN();
                    } else {
                        /**
                         * @lua 1/0 gives inf, -1/0 gives -inf (no manual reference)
                         */
                        return new \Hoathis\Lua\Model\Value\Inf($values[0]);
                    }
                }
                /**
                 * Cast to double because it is required by lua manual
                 * @link http://www.lua.org/manual/5.3/manual.html#3.4.1 Lua 5.3 Manual § 3.4.1 – Arithmetic Operators
                 * @lua Exponentiation and float division (/) always convert their operands to floats and the result is always a float.
                 */
                $value = (double) ((double) $values[0] / (double) $values[1]);
                break;
            case '#floordivision':
                if (0 === $values[1]) {
                    /**
                     * @lua 1//0 raise a runtime error (maybe a bug ?)
                     */
                    throw new \Hoathis\Lua\Exception\Interpreter('attempt to divide by zero');
                } elseif (0.0 === $values[1]) {
                    /**
                     * @lua 1//0.0 gives inf, -1/0.0 gives -inf (no manual reference)
                     */
                    return new \Hoathis\Lua\Model\Value\Inf($values[0]);
                }
                /**
                 * Use of floor and not intdiv (which round to the integer toward 0)
                 * @link http://www.lua.org/manual/5.3/manual.html#3.4.1 Lua 5.3 Manual § 3.4.1 – Arithmetic Operators
                 * @lua Floor division (//) is a division that rounds the quotient towards minus infinity, that is, the floor of the division of its operands.
                 */
                $value = floor($values[0] / $values[1]);
                break;
            case '#power':
                $value = $values[0] ** $values[1];
                break;
            case '#negative':
                $value = -1 * $values[0];
                break;
        }
        return new \Hoathis\Lua\Model\Value\Number($value);
    }
}