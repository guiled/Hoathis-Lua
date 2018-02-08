<?php

namespace Hoathis\Lua\Model;

/**
 * Class \Hoathis\Lua\Model\Closure.
 *
 * Closure.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */

class Closure extends Environment {

    protected $_body       = null;
    protected $_parameters = array();



    public function __construct ( $name, parent $parent,
                                  Array $parameters,
                                  $body ) {

        parent::__construct($name, $parent);

        foreach($parameters as $parameter) {
            if ($parameter instanceof ValueGroup) {
                $param2 = $parameter->getValue();
                foreach ($param2 as $param) {
                    $this->localSet($param, new Variable($param, $this));
                    $this->_parameters[$param] = &$this[$param];
                }
            } else {
                $this->localSet($parameter, new Variable($parameter, $this));
                $this->_parameters[$parameter] = &$this[$parameter];
            }
        }

        $this->_body = $body;

        return;
    }

    public function call ( Array $arguments, \Hoathis\Lua\Visitor\Interpreter $interpreter ) {
        $oldEnvironment = $interpreter->getRoot();
        $interpreter->setRoot($this);
    	if ($this->getBody() instanceof \Hoa\Compiler\Llk\TreeNode) {

			foreach($this->_parameters as $paramname => $parameter) {
                if ($paramname === '...') {
                    $value = new ValueGroup(null);
                    while (list($k,$argument) = each($arguments)) {
                        $value->addValue($argument);
                    }
                    $parameter->setValue($value);
                } else {
                    $argument_parts = each($arguments);

                    if (false === $argument_parts) {
                        $parameter->setValue(new Value(null));
                    } else {
                        $parameter->setValue($argument_parts[1]);
                    }
                }
			}

			$out = $this->getBody()->accept($interpreter);
			foreach($this->_parameters as $parameter) {
				$parameter->setValue(new Value(null));
            }
            $interpreter->setRoot($oldEnvironment);
			return $out;
		} elseif (true === is_callable($this->_body)) {
            $argValues = array();
            foreach ($arguments as $arg) {
                if ($arg instanceof ValueGroup) {
                    $argValues = array_merge($argValues, $arg->getPHPValue());
                } elseif ($arg instanceof Value) {
                    $argValues[] = $arg->getPHPValue();
                } else {
                    $argValues[] = $arg;
                }
            }
            $interpreter->setRoot($oldEnvironment);
			return call_user_func_array($this->_body, $argValues);
		} else {
            $interpreter->setRoot($oldEnvironment);
			throw new \Hoathis\Lua\Exception\Interpreter('Invalid function body', 43, $this->_name);
		}
    }

    public function getBody ( ) {

        return $this->_body;
    }

    public function isFunctionContext() {
        return true;
    }
}

