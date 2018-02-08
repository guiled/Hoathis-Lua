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
        return $this->array;
    }

}
