<?php
namespace Hoathis\Lua\Model\Value;

/**
 * Description of Nil
 *
 * @author Guislain Duthieuw
 */
class Boolean extends \Hoathis\Lua\Model\Value
{
    const TYPE='number';

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