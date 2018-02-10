<?php

namespace Hoathis\Lua\Visitor\Node;

use \Hoathis\Lua\Model\Value;
/**
 * Description of Block
 *
 * @author Guislain Duthieuw
 */
class Token extends \Hoathis\Lua\Visitor\Node
{
    protected $interpreter;

    public function getInterpreter()
    {
        return $this->interpreter;
    }

    public function setInterpreter($interpreter)
    {
        $this->interpreter = $interpreter;
        return $this;
    }

    /**
     *
     * @param \Hoa\Visitor\Element $element
     * @param type $handle
     * @param type $eldnah
     * @return \Hoathis\Lua\Model\BreakStatement
     * @throws \Hoathis\Lua\Exception\Interpreter
     */
    public function visit(\Hoa\Visitor\Element $element, &$handle = null, $eldnah = null)
    {
        $token = $element->getValueToken();
        $value = $element->getValueValue();

        $environment = $this->interpreter->getEnvironment();

        switch ($token) {

            case 'identifier':
                return $value;

            case 'number':
                return new Value\Number($value);

            case 'string':
                // Trim on left and right the string delimiters
                $val = preg_replace('/([\'"])(.*?)\1/', '$2', $value, 1);
                return new Value\String($val);
            case 'longstring':
                // Trim on left and right the string delimiters
                $val = preg_replace('/\[(=*)\[((?:.|\n)*?)\]\1\]/', '$2', $value, 1);
                return new Value\String($val);

            case 'nil':
                return new Value\Nil();

            case 'false':
                return new Value\Boolean(false);

            case 'true':
                echo 'fldjflskjfsdlkj';
                return new Value\Boolean(true);

//            case 'tpoint':
//                if (false === $environment->isFunctionContext()) {
//                    throw new \Hoathis\Lua\Exception\Interpreter(
//                    'Symbol ... is not available outside of function.', 1, $token);
//                }
//                if (false === isset($environment['...'])) {
//                    throw new \Hoathis\Lua\Exception\Interpreter(
//                    'Symbol ... is unknown in this function.', 1, $token);
//                }
//                return $environment['...']->getValue();

            default:
                throw new \Hoathis\Lua\Exception\Interpreter(
                'Token %s is not yet implemented.', 1, $token);
        }
    }
}