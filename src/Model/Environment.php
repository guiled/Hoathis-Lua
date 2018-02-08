<?php

namespace Hoathis\Lua\Model;

/**
 * Class \Hoathis\Lua\Model\Environment.
 *
 * Environment.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */
class Environment implements \ArrayAccess
{
    protected $_name         = null;
    protected $_environments = null;
    protected $_parent       = null;
    protected $_symbols      = array();

    public function __construct($name, self $parent = null)
    {

        $this->_name         = $name;
        $this->_environments = new \SplStack();
        $this->_parent       = $parent;

        return;
    }

    public function offsetExists($symbol)
    {
        $foundInLocal = array_key_exists($symbol, $this->_symbols);
        if (false === $foundInLocal && $this->_parent instanceof Environment) {
            return $this->_parent->offsetExists($symbol);
        }
        return $foundInLocal;
    }

    public function offsetGet($symbol)
    {
        if (true === array_key_exists($symbol, $this->_symbols)) {
            return $this->_symbols[$symbol];
        } elseif ($this->_parent instanceof Environment) {
            return $this->_parent->offsetGet($symbol);
        } else {
            $var = new Variable($symbol, $this, new Value(null));
            $var->setValue(new Value(null));
            return $var;
        }
    }

    public function localExists($symbol)
    {
        return array_key_exists($symbol, $this->_symbols);
    }

    public function localSet($symbol, $value)
    {
        $this->_symbols[$symbol] = $value;
    }

    public function offsetSet($symbol, $value)
    {
        if (false === array_key_exists($symbol, $this->_symbols) && $this->_parent instanceof Environment) {     // variables are global by default
            $this->_parent->offsetSet($symbol, $value);
        } else {
            $this->_symbols[$symbol] = $value;      // we are in global environment or the local symbol is declared
        }
        return $this;
    }

    public function offsetUnset($symbol)
    {
        if (array_key_exists($symbol, $this->_symbols)) {
            unset($this->_symbols[$symbol]);
        } elseif ($this->_parent instanceof Environment) {
            $this->_parent->offsetUnset($symbol, $value);
        }

        return;
    }

    public function getName()
    {

        return $this->_name;
    }

    public function getParent()
    {

        return $this->_parent;
    }

    public function getSymbols()
    {
        return $this->_symbols;
    }

    public function getDeclaredSymbols()
    {
        return array_keys($this->_symbols);
    }

    public function isFunctionContext()
    {
        if (false === is_null($this->_parent)) {
            return $this->_parent->isFunctionContext();
        } else {
            return false;
        }
    }

    public function wrap($name, &$obj)
    {
        if (true === is_object($obj)) {
            $wrapper = new WrapperObject($obj);
            $type    = Value::REFERENCE;
        } elseif (true === is_array($obj)) {
            $wrapper = new WrapperArray($obj);
            $type    = Value::REFERENCE;
        } else {
            $wrapper = new WrapperScalar($obj);
            $type    = Value::REFERENCE;
        }
        $this->_symbols[$name] = new Value($wrapper, $type);
    }

    public function setFunction($function_name, $callback)
    {
        $this->_symbols[$function_name] = new \Hoathis\Lua\Model\Variable($function_name, $this);
        $this->_symbols[$function_name]->setValue(new \Hoathis\Lua\Model\Value(new \Hoathis\Lua\Model\Closure($function_name,
            $this, array(), $callback)));
    }
}