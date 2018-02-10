<?php

namespace Hoathis\Lua\Visitor\Node;

/**
 * Description of Block
 *
 * @author Guislain Duthieuw
 */
class FunctionCall extends \Hoathis\Lua\Visitor\Node
{
    /**
     *
     * @var \Hoathis\Lua\Visitor\Interpreter
     */
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
        $children = $element->getChildren();

        //$selfFunction = ($children[0]->getId() === '#table_access_self');
        $closure       = $this->getValue($children[0]->accept($this->interpreter, $handle, $eldnah));
        if (true === isset($children[1])) {
            $arguments = $this->getValue($children[1]->accept($this->interpreter, $handle, $eldnah));
        } else {
            $arguments = array();
        }

        //$closure = $this->interpreter->getEnvironment()->get($symbol);

        /*if ($symbol instanceof \Hoathis\Lua\Model\Value) {
            // @todo $closure = $symbol->getValue();
        } else {
            if (true === is_callable($symbol)) {
                $argValues = array();
                foreach ($arguments as $arg) {
                    if ($arg instanceof ValueGroup) {
                        $argValues = array_merge($argValues, $arg->getValue());
                    } else {
                        $argValues[] = $arg->getPHPValue();
                    }
                    //$argValues[] = $arg->getPHPValue();
                }
                return call_user_func_array($symbol, $argValues);
            }

            $environment = $this->interpreter->getRoot();

            if (!$environment->exists($symbol)) {
                throw new \Hoathis\Lua\Exception\Interpreter(
                'Unknown symbol %s()', 42, $symbol);
            }
            $closure = $environment->get($symbol);
            if (!($closure instanceof \Hoathis\Lua\Model\Closure)) {
                throw new \Hoathis\Lua\Exception\Interpreter(
                'Symbol %s() is not a function.', 42, $symbol);
            }
        }

        $oldEnvironment = $environment;
        if (true === $selfFunction) {           // a function called with colon in a table receives a hidden parameter called self (the function container)
            array_unshift($arguments, $symbol->getContainer());
        }*/
        $out = $closure->call($arguments, $this->interpreter);

        return $out;
    }
}