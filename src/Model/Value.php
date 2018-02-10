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
     *
     * @var Hoathis\Lua\Model
     */
    protected $metatable;

    protected $content;

    public function __construct($value = null)
    {
        $this->content = $value;
    }

    protected function initMetaTable() {
        if(!$this->metatable instanceof Value\Table) {
            $this->metatable = new Value\Table;
        }
    }

    public function getmetatable() {
        $this->initMetaTable();
        return $this->metatable;
    }

    public function setmetatable(Value\Table $metatable) {
        $this->metatable = $metatable;
    }

    public function toPHP()
    {
        return $this->content;
    }
}