<?php

namespace Hoathis\Lua\Model\Value;

/**
 * Description of Nil
 *
 * @author Guislain Duthieuw
 */
class Number extends \Hoathis\Lua\Model\Value
{
    const TYPE = 'number';

    public function __construct($value = null)
    {
        /**
         * @link http://www.lua.org/manual/5.3/manual.html#2.1 Lua 5.3 Manual § 2.1 – Values and Types
         * @lua The type number uses two internal representations, or two subtypes, one called integer and the other called float. Lua has explicit rules about when each representation is used
         */
        if (is_int($value) || is_double($value)) {
            $subTypedValue = $value;
        } elseif ($value === strval(intval($value))) {
            // parse $value string as int
            $subTypedValue = intval($value);
        } else {
            // parse $value string as double
            /**
             * @link http://www.lua.org/manual/5.3/manual.html#2.1 Lua 5.3 Manual § 2.1 – Values and Types
             * @lua Standard Lua uses 64-bit integers and double-precision (64-bit) floats
             */
            $subTypedValue = doubleval($value);
        }
        parent::__construct($subTypedValue);
    }

    public function __toString()
    {
        /**
         * @lua float numbers like 1.0 are displayed 1.0
         */
        if (is_double($this->content) && ceil($this->content) == $this->content) {
            return number_format($this->content, 1);
        }
        /**
         * @lua integer numbers are displayed without decimals
         */
        return parent::__toString();
    }
}