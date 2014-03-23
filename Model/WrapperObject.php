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
-> import('Lua.Exception.Model')
/**
 * \Hoathis\Lua\Model.Wrapper
 */
-> import('Lua.Model.Wrapper');

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
class WrapperObject implements Wrapper {

    protected $obj;

    protected $hasMagicFieldRead;
    protected $hasMagicFieldWrite;

    protected $hasMagicFunction;

    protected $fields;

    protected $functions;

    public function __construct($object) {
        $this->obj = $object;

        if (true === is_object($object)) {

            $this->hasMagicFieldRead = method_exists($object, '__get');
            $this->hasMagicFieldWrite = method_exists($object, '__set');

            $this->hasMagicFunction = method_exists($object, '__call');

            $this->fields = array_keys(get_object_vars($object));

            $this->functions = get_class_methods($object);
        } else {
            $this->hasMagicFieldRead = false;
            $this->hasMagicFieldWrite = false;

            $this->hasMagicFunction = false;

            $this->fields = array_keys($object);

            $this->functions = array();
        }
    }


    public function offsetExists($offset) {
        return in_array($offset, $this->fields) || in_array($offset, $this->functions) || $this->hasMagicFieldRead || $this->hasMagicFunction;
    }

    public function offsetGet($offset) {
        if (true === in_array($offset, $this->fields)) {
            $returnType = 0;
        } elseif (true === in_array($offset, $this->functions)) {
            $returnType = 1;
        } elseif ($this->hasMagicFieldRead) {
            $returnType = 0;
        } elseif ($this->hasMagicFunction) {
            $returnType = 1;
        } else {
            throw new \Hoathis\Lua\Exception\Interpreter(
                'Unknown field %s in object type %s', 902, array($offset, get_class($this->obj)));
        }

        if ($returnType === 0) {
            if (true === is_object($this->obj->$offset)) {
                $val = new Value(new WrapperObject($this->obj->$offset), Value::REFERENCE);
                return $val;
            } elseif (true === is_array($this->obj->$offset)) {
                $wrap = new WrapperArray($this->obj->$offset);
                $val = new Value($wrap, Value::REFERENCE);
                return $val;
            } else {
                $val = new Value($this->obj->$offset);
                $wrapper = $this;
                $val->setSetValueFunction(function ($val) use ($wrapper, $offset) {
                    $wrapper[$offset] = $val;
                });
                return $val;
            }
        } else {
            $cb = array($this->obj,$offset);
            $returnFunction = function () use ($cb) {
                $returnVal = call_user_func_array($cb, func_get_args());
                if (true === is_object($returnVal)) {
                    $wrap = new self($returnVal);
                    return new Value($wrap, Value::REFERENCE);
                } else {
                    return new Value($returnVal);
                }
            };
            return $returnFunction;
        }
    }

    public function offsetSet($offset, $value) {
        if (true === in_array($offset, $this->fields) || $this->hasMagicFieldWrite) {
            if ($value instanceof Value) {
                $this->obj->$offset = $value->getValue();
            } else {
                $this->obj->$offset = $value;
            }
        } else {
            throw new \Hoathis\Lua\Exception\Interpreter(
                'Unknown field %s in object type %s', 901, array($offset, get_class($this->obj)));
        }
    }

    public function offsetUnset($offset) {

    }

    public function __destruct() {
    }

    public function getValue() {
        return $this;
    }

    public function setValue($a) {
    }

    public function getPHPValue() {
        return $this->obj;
    }

}
}