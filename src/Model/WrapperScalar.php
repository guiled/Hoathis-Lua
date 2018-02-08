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
class WrapperScalar {

    protected $val;

    public function __construct(&$val) {
        $this->val = &$val;
    }

    public function &getValue() {
        return $this->val;
    }

    public function setValue($a) {
        $this->val = $a;
    }

    public function &getPHPValue() {
        return $this->val;
    }

    public function __toString() {
        return strval($this->val);
    }

}
