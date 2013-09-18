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
class WrapperArray implements Wrapper {

    protected $array;

    protected $fields;

    public function __construct(&$array) {
        $this->array = &$array;

        $this->fields = array_keys($array);
    }


    public function offsetExists($offset) {
        return in_array($offset, $this->fields);
    }

    public function offsetGet($offset) {
        if (true === in_array($offset, $this->fields)) {
            if (true === is_object($this->array[$offset])) {
                $val = new Value(new WrapperObject($this->array[$offset]), Value::REFERENCE);
                return $val;
            } elseif (true === is_array($this->array[$offset])) {
                return new Value(new WrapperArray($this->array[$offset]), Value::REFERENCE);
            } elseif (true === is_callable($this->array[$offset])) {
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
            } else {
                $wrapper = $this;
                $val = new Value($this->array[$offset]);
                $val->setSetValueFunction(function ($val) use ($wrapper, $offset) {
                    $wrapper[$offset] = $val;
                });
                return $val;
            }
        } else {
            throw new \Hoathis\Lua\Exception\Interpreter(
                'Unknown field %s in array', 902, $offset);
        }
    }

    public function offsetSet($offset, $value) {
        if (true === in_array($offset, $this->fields)) {
            if ($value instanceof Value) {
                $this->array[$offset] = $value->getValue();
            } else {
                $this->array[$offset] = $value;
            }
        } else {
            throw new \Hoathis\Lua\Exception\Interpreter(
                'Unknown field %s in array', 901, $offset);
        }
    }

    public function offsetUnset($offset) {

    }
//
//    public function __destruct() {
//        var_dump($this->obj);
//    }

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