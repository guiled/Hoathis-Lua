<?php
/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace Hoathis\Lua\Model\Value;

/**
 * Description of Nil
 *
 * @author Guislain Duthieuw
 */
class NaN extends \Hoathis\Lua\Model\Value\Number
{

    public function __toString()
    {
        return 'nan';
    }
}