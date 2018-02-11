<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Hoathis\Lua\Model\Value;

/**
 * Description of Inf
 *
 * @author Guislain Duthieuw
 */
class Inf extends \Hoathis\Lua\Model\Value\Number
{

    public function __toString()
    {
        return ($this->content < 0 ? '-' : '') . 'inf';
    }
}