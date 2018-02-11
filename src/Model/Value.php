<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Hoathis\Lua\Model;

/**
 * Description of Value
 *
 * @author Guislain Duthieuw
 */
abstract class Value
{
    /**
     * @link http://www.lua.org/manual/5.3/manual.html#2.1 Lua 5.3 Manual § 2.1 – Values and Types
     * @lua Lua is a dynamically typed language. This means that variables do not have types; only values do. There are no type definitions in the language. All values carry their own type. 
     */
    const TYPE = 'nil';

    /**
     *
     * @var Hoathis\Lua\Model
     */
    protected $metatable;
    protected $content;

    public function __construct($value = null)
    {
        $this->content = $value;
    }

    protected function initMetaTable()
    {
        if (!$this->metatable instanceof Value\Table) {
            $this->metatable = new Value\Table;
        }
    }

    public function getmetatable()
    {
        $this->initMetaTable();
        return $this->metatable;
    }

    public function setmetatable(Value\Table $metatable)
    {
        $this->metatable = $metatable;
    }

    public function toPHP()
    {
        return $this->content;
    }

    public function __toString()
    {
        return (string) $this->content;
    }
}