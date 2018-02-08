<?php

namespace Hoathis\Lua\Model;

/**
 * Class \Hoathis\Lua\Model\Closure.
 *
 * Closure.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */

class ValueGroup extends Value implements \ArrayAccess {


    public function __construct($value, $referenceType = self::SCALAR) {
        parent::__construct($value, $referenceType);
        $this->_referenceType = self::SCALAR;
        $this->_value = array();
    }

    public function addValue($value) {
        if ($value instanceof ValueGroup) {
            $this->_value = array_merge($this->_value, $value->getValue());
        } else {
            $this->_value[] = $value;
        }
    }

    public function offsetExists($offset) {
        return array_key_exists($offset, $this->_value);
    }

    public function offsetGet($offset) {
        return $this->_value[$offset];
    }

    public function offsetSet($offset, $value) {
        $this->_value[$offset] = $value;
    }

    public function offsetUnset($offset) {
        unset($this->_value[$offset]);
    }

    public function getPHPValue() {
        $value = $this->getValue();
        $result = array();
        foreach ($value as $key => $val) {
            $result[$key] = $val->getPHPValue();
        }
        return $result;
    }

}
