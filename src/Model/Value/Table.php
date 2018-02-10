<?php

namespace Hoathis\Lua\Model\Value;

/**
 * Description of Table
 *
 * @author Guislain Duthieuw
 */
class Table extends \Hoathis\Lua\Model\Value
{
    const TYPE = 'table';

    public function __construct($value = null)
    {
        parent::__construct([]);
    }

    public function get($name)
    {
        if (!array_key_exists($name, $this->content)) {
            return new Nil();
        }
        return $this->content[$name];
    }

    public function set($name, \Hoathis\Lua\Model\Value $value) {
        $this->content[$name] = $value;
    }

    public function exists($name)
    {
        return array_key_exists($name, $this->content);
    }
}