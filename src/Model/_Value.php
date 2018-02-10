<?php

namespace Hoathis\Lua\Model;

/**
 * Class \Hoathis\Lua\Model\Variable.
 *
 * Variable.
 *
 * @author     Guislain Duthieuw <guislain.duthieuw@gmail.com>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */

class _Value {

    protected $_value      = null;
    protected $_referenceType = false;
    protected $_container;
    protected $setValueFunction;

    const SCALAR = 0;
    const REFERENCE = 1;


    public function __construct ( $value, $referenceType = self::SCALAR) {

        $this->_referenceType = $referenceType;
        if ($this->_referenceType === self::REFERENCE) {
            $this->_value       = new self($value);
        } else {
            $this->_value = $value;
        }
        $this->setValueFunction = array($this, 'setValueFunction');
    }

    public function setSetValueFunction($setValueFunction) {
        $this->setValueFunction = $setValueFunction;
    }

    public function setValue($value) {
        return call_user_func($this->setValueFunction, $value);
    }

    protected function setValueFunction ( $value ) {
        if ($value instanceof self) {
            $val = $value->getValue();
        } else {
            $val = $value;
        }
        if ($this->_referenceType === self::REFERENCE) {
            $old          = $this->_value->getValue();
            $this->_value->setValue($val);
        } else {
            $old          = $this->_value;
            $this->_value = $val;
        }
        return $old;
    }

    public function getValue ( ) {
        if ($this->_referenceType === self::REFERENCE) {
            return $this->_value->getValue();
        } else {
            return $this->_value;
        }
    }

    public function getReference() {
        if ($this->_referenceType === self::REFERENCE) {
            return $this->_value;
        } else {
            return $this;
        }
    }

    public function setReference($newReference) {
        if ($this->_referenceType === self::REFERENCE) {
            $old = $this->_value->getValue();
        } else {
            $old = $this->_value;
        }
        $this->_value = $newReference;
        $this->_referenceType = self::REFERENCE;
        return $old;
    }

    public function isReference() {
        return $this->_referenceType === self::REFERENCE;
    }

    public function getPHPValue() {
        $val = $this->getValue();
        if (true === is_array($val)) {
            $result = array();
            foreach ($val as $k => $v) {
                if ($v instanceof self) {
                    $result[$k] = $v->getPHPValue();
                } else {
                    $result[$k] = $v;
                }
            }
            return $result;
        }
        return $this->getValue();
    }

    public function setContainer($container) {
        $this->_container = $container;
    }

    public function getContainer() {
        return $this->_container;
    }
}
