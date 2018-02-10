<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Hoathis\Lua\Model\Value;

/**
 * Description of LuaFunction
 *
 * @author Guislain Duthieuw
 */
class Closure extends \Hoathis\Lua\Model\Value
{
    const TYPE = 'function';

    public function call($arguments)
    {
        return call_user_func_array($this->content, $arguments);
    }
}