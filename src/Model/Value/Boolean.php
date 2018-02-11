<?php
namespace Hoathis\Lua\Model\Value;

/**
 * Description of Nil
 *
 * @author Guislain Duthieuw
 */
class Boolean extends \Hoathis\Lua\Model\Value
{
    const TYPE='boolean';

    /**
     * @link http://www.lua.org/manual/5.3/manual.html#2.1 Lua 5.3 Manual § 2.1 – Values and Types
     * @lua The type boolean has two values, false and true
     */
    public function isTrue() {
        return ($this->content === true);
    }

    public function isFalse() {
        return ($this->content === false);
    }

    public function __toString()
    {
        if ($this->content === true) {
            return 'true';
        } else {
            return 'false';
        }
    }

}