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
        if (intval($value) == $value) {
            // parse $value string as int
            $value = intval($value);
        } else {
            // parse $value string as float
            $value = floatval($value);
        }
        parent::__construct($value);
    }
}