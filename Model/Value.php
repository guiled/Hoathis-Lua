<?php

/**
 * Hoa
 *
 *
 * @license
 *
 * New BSD License
 *
 * Copyright © 2007-2013, Ivan Enderlin. All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without
 * modification, are permitted provided that the following conditions are met:
 *     * Redistributions of source code must retain the above copyright
 *       notice, this list of conditions and the following disclaimer.
 *     * Redistributions in binary form must reproduce the above copyright
 *       notice, this list of conditions and the following disclaimer in the
 *       documentation and/or other materials provided with the distribution.
 *     * Neither the name of the Hoa nor the names of its contributors may be
 *       used to endorse or promote products derived from this software without
 *       specific prior written permission.
 *
 * THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS"
 * AND ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE
 * IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE
 * ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDERS AND CONTRIBUTORS BE
 * LIABLE FOR ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR
 * CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED TO, PROCUREMENT OF
 * SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS
 * INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN
 * CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
 * ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
 * POSSIBILITY OF SUCH DAMAGE.
 */

namespace {

from('Hoathis')

/**
 * \Hoathis\Lua\Exception\Model
 */
-> import('Lua.Exception.Model');

}

namespace Hoathis\Lua\Model {

/**
 * Class \Hoathis\Lua\Model\Variable.
 *
 * Variable.
 *
 * @author     Guislain Duthieuw <guislain.duthieuw@gmail.com>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */

class Value {

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
        return $this->getValue();
    }

    public function setContainer($container) {
        $this->_container = $container;
    }

    public function getContainer() {
        return $this->_container;
    }
}

}
