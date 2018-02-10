<?php
namespace Hoathis\Lua\Library;

/**
 * Description of Basic
 *
 * @author Guislain Duthieuw
 */
class Basic
{
    public function stdPrint()
    {
        $args = func_get_args();
        $sep  = '';
        foreach ($args as $arg) {
            echo $sep;
            if ($arg instanceof \Hoathis\Lua\Model\Value\Boolean) {
                if ($arg->isTrue()) {
                    echo 'true';
                } else {
                    echo 'false';
                }
            } else {
                echo $arg->toPHP();
            }

            /*if (true === is_null($arg)) {
                echo 'nil';
            } elseif (false === $arg) {
                echo 'false';
            } elseif (true === is_array($arg)) {
                echo 'array';
            } elseif (true === is_callable($arg) || $arg instanceof \Hoathis\Lua\Model\Closure) {
                echo 'function';
            } else {
                echo $arg;
            }*/
            $sep = "\t";
        }
        echo "\n";
    }
}