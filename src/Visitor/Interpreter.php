<?php

namespace Hoathis\Lua\Visitor;

/**
 * Class \Hoathis\Lua\Visitor\Interpreter.
 *
 * Interpreter.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */
class Interpreter implements \Hoa\Visitor\Visit
{
    const AS_SYMBOL = 0;
    const AS_VALUE  = 1;

    protected $_environment = null;

    public function __construct()
    {

        $this->_environment = new \Hoathis\Lua\Model\Environment('_G');
        $this->_environment->setFunction('print', array($this, 'stdPrint'));
        $this->_environment->setFunction('ipairs', array($this, 'stdIpairs'));
        $this->_environment->setFunction('next', array($this, 'stdNext'));
        $this->_environment->setFunction('pairs', array($this, 'stdPairs'));

        return;
    }

    /**
     * Visit an element.
     *
     * @access  public
     * @param   \Hoa\Visitor\Element  $element    Element to visit.
     * @param   mixed                 &$handle    Handle (reference).
     * @param   mixed                 $eldnah     Handle (not reference).
     * @return  float
     */
    public function visit(\Hoa\Visitor\Element $element, &$handle = null, $eldnah = null)
    {

        $type     = $element->getId();
        $children = $element->getChildren();

        switch ($type) {

            case '#do_block':
                $oldEnvironment = $this->_environment;
                $data           = $element->getData();
                if (false === isset($data['env'])) {
                    $data['env'] = new \Hoathis\Lua\Model\Environment('block', $this->_environment);
                }
                $this->_environment = $data['env'];
            case '#block':
                $nbchildren         = count($children);
                for ($i = 0; $i < $nbchildren; $i++) {
                    $val = $children[$i]->accept($this, $handle, $eldnah);
                    if ($val instanceof \Hoathis\Lua\Model\ReturnedValue) {
                        $parent = $element->getParent();
                        if (true === is_null($parent)) {
                            $returnedValue = $val->getValue()->getPHPValue();
                        } else {
                            $returnedValue = $val;
                        }
                        break;
                    } elseif ($val instanceof \Hoathis\Lua\Model\BreakStatement) {
                        $parent = $element->getParent();
                        if (true === is_null($parent)) {
                            throw new \Hoathis\Lua\Exception\Interpreter(
                            'Break found outside of loop.', 1);
                        } else {
                            $returnedValue = $val;
                        }
                        break;
                    }
                }
                if (true === isset($oldEnvironment)) {
                    $this->_environment = $oldEnvironment;
                }
                if (true === isset($returnedValue)) {
                    return $returnedValue;
                }
                break;

            case '#chunk':
            case '#function_body':
                foreach ($children as $child) {
                    $execValue = $child->accept($this, $handle, $eldnah);
                    if ($execValue instanceof \Hoathis\Lua\Model\ReturnedValue) {
                        if ($type === '#chunk') {
                            return $execValue->getValue()->getPHPValue();
                        } else {
                            return $execValue->getValue();
                        }
                    }
                }
                break;

            case '#assignation_local':
                $assignation_local = true;
            case '#assignation':
                if (false === isset($assignation_local)) {
                    $assignation_local = false;
                }
                $leftVar  = $children[0]->accept($this, $handle, $eldnah);
                $rightVar = $children[1]->accept($this, $handle, self::AS_VALUE);
                if ($leftVar instanceof \Hoathis\Lua\Model\ValueGroup) {
                    $symbols = $leftVar->getValue();
                } else {
                    $symbols = array($leftVar);
                }

                if ($rightVar instanceof \Hoathis\Lua\Model\ValueGroup) {
                    $values = $rightVar->getValue();
                } else {
                    $values = array($rightVar);
                }
                $this->setValueGroupToValueGroup($symbols, $values, $assignation_local);
                break;

            case '#expression_group':
                $group = new \Hoathis\Lua\Model\ValueGroup(null);
                foreach ($children as $child) {
                    $group->addValue($child->accept($this, $handle, $eldnah));
                }
                return $group;

            case '#onlyfirst':
                $childValue = $children[0]->accept($this, $handle, $eldnah);
                if ($childValue instanceof \Hoathis\Lua\Model\ValueGroup) {
                    $values = $childValue->getValue();
                    return $values[0];
                } else {
                    return $childValue;
                }

            case '#negative':
                return -($children[0]->accept($this, $handle, self::AS_VALUE));

            case '#length':
                $value = $children[0]->accept($this, $handle, self::AS_VALUE);
                if (true === is_array($value)) {
                    return count($value);
                } elseif (true === is_string($value)) {
                    return strlen($value);
                }
                break;

            case '#addition':
                $parent = $element->getParent();
                $child0 = $children[0]->accept($this, $handle, self::AS_VALUE);
                $child1 = $children[1]->accept($this, $handle, self::AS_VALUE);

                if (null !== $parent && '#substraction' === $parent->getId()) {
                    return new \Hoathis\Lua\Model\Value($child0->getValue() - $child1->getValue());
                }

                return new \Hoathis\Lua\Model\Value($child0->getValue() + $child1->getValue());
            case '#substraction':
                $parent = $element->getParent();
                $child0 = $children[0]->accept($this, $handle, self::AS_VALUE);
                $child1 = $children[1]->accept($this, $handle, self::AS_VALUE);

                if (null !== $parent && '#substraction' === $parent->getId() && $element === $parent->getChild(1)) {
                    return new \Hoathis\Lua\Model\Value($child0->getValue() - -$child1->getValue());
                }

                return new \Hoathis\Lua\Model\Value($child0->getValue() - $child1->getValue());

            case '#power':
                //print_r($this->_environment->_symbols);
                $child0 = $children[0]->accept($this, $handle, self::AS_VALUE);
                $child1 = $children[1]->accept($this, $handle, self::AS_VALUE);
                return new \Hoathis\Lua\Model\Value(pow($child0->getValue(), $child1->getValue()));

            case '#modulo':
                $child0 = $children[0]->accept($this, $handle, self::AS_VALUE);
                $child1 = $children[1]->accept($this, $handle, self::AS_VALUE);

                return new \Hoathis\Lua\Model\Value($child0->getValue() % $child1->getValue());

            case '#multiplication':
                $child0 = $children[0]->accept($this, $handle, self::AS_VALUE);
                $child1 = $children[1]->accept($this, $handle, self::AS_VALUE);

                return new \Hoathis\Lua\Model\Value($child0->getValue() * $child1->getValue());

            case '#division':
                $child0 = $children[0]->accept($this, $handle, self::AS_VALUE);
                $child1 = $children[1]->accept($this, $handle, self::AS_VALUE);

                if (0 == $child1->getValue()) {
                    throw new \Hoathis\Lua\Exception\Interpreter(
                    'Tried to divide %f by zero, impossible.', 0, $child0->getValue());
                }

                return new \Hoathis\Lua\Model\Value($child0->getValue() / $child1->getValue());

            case '#concatenation':
                $child0 = $children[0]->accept($this, $handle, self::AS_VALUE);
                $child1 = $children[1]->accept($this, $handle, self::AS_VALUE);

                return new \Hoathis\Lua\Model\Value($child0->getValue() . $child1->getValue());

            case '#comparison':
                $val1       = $children[0]->accept($this, $handle, self::AS_VALUE);
                $comparison = $children[1]->getValueToken();
                $val2       = $children[2]->accept($this, $handle, self::AS_VALUE);
                switch ($comparison) {
                    case 'dequal':
                        return new \Hoathis\Lua\Model\Value($val1->getValue() === $val2->getValue());
                    case 'nequal':
                        return new \Hoathis\Lua\Model\Value($val1->getValue() !== $val2->getValue());
                    case 'lt':
                        if (is_numeric($val1->getValue()) && is_numeric($val2->getValue()) || is_string($val1->getValue())
                            && is_string($val2->getValue())) {
                            return new \Hoathis\Lua\Model\Value($val1->getValue() < $val2->getValue());
                        } // TODO must manage when comparing two tables
                        break;
                    case 'gt':
                        if (is_numeric($val1->getValue()) && is_numeric($val2->getValue()) || is_string($val1->getValue())
                            && is_string($val2->getValue())) {
                            return new \Hoathis\Lua\Model\Value($val1->getValue() > $val2->getValue());
                        } // TODO must manage when comparing two tables
                        break;
                    case 'lte':
                        if (is_numeric($val1->getValue()) && is_numeric($val2->getValue()) || is_string($val1->getValue())
                            && is_string($val2->getValue())) {
                            return new \Hoathis\Lua\Model\Value($val1->getValue() <= $val2->getValue());
                        }
                        // TODO must manage when comparing two tables

                        break;
                    case 'gte':
                        if (is_numeric($val1->getValue()) && is_numeric($val2->getValue()) || is_string($val1->getValue())
                            && is_string($val2->getValue())) {
                            return new \Hoathis\Lua\Model\Value($val1->getValue() >= $val2->getValue());
                        } // TODO must manage when comparing two tables

                        break;
                }
                break;

            case '#local_function':
                $local_function = true;
            case '#function':
                $symbolChild    = $children[0];
                $selfFunction   = ($symbolChild->getId() === '#table_access_self');
                $symbol         = $symbolChild->accept($this, $handle, self::AS_SYMBOL);
            case "#function_lambda":
                if (false === isset($selfFunction)) {
                    $selfFunction = false;
                }
                $nbchildren = count($children);
                $body       = $children[$nbchildren - 1];
                if ($nbchildren > 2) {              // there are parameters
                    $parameters = $children[1]->accept($this, $handle, self::AS_SYMBOL);
                }
                if (false === isset($parameters)) {
                    $parameters = array();
                }
                if ($selfFunction) {        // a function declared with colon in a table accepts a hidden first parameter called "self"
                    array_unshift($parameters, 'self');
                }
                if (false === isset($symbol)) {
                    $closuresymbol = 'lambda_' . md5(print_r($body, true));
                } else {
                    $closuresymbol = $symbol;
                }
                $closure = new \Hoathis\Lua\Model\Closure(
                    $closuresymbol, $this->_environment, $parameters, $body
                );
                if (true === isset($symbol)) {         // it's a function declaration with the symbol
                    if ($symbol instanceof \Hoathis\Lua\Model\Value) {          // symbol is an array that must receive a closure
                        $symbol->setValue(new \Hoathis\Lua\Model\Value($closure));
                        return $symbol;
                    } else {
                        if (isset($local_function)) {
                            $this->_environment->localSet($symbol,
                                new \Hoathis\Lua\Model\Variable($symbol, $this->_environment));
                        } else {
                            $this->_environment[$symbol] = new \Hoathis\Lua\Model\Variable($symbol, $this->_environment);
                        }
                        $this->_environment[$symbol]->setValue(new \Hoathis\Lua\Model\Value($closure));
                        return $this->_environment[$symbol];
                    }
                } else {                // it's a lambda function
                    return new \Hoathis\Lua\Model\Value($closure); //, \Hoathis\Lua\Model\Value::REFERENCE);
                }
                break;

            case '#tpointfcn':      // when a ... is used in function parameters
                if (false === empty($children)) {
                    $parameters = $children[0]->accept($this, $handle, self::AS_SYMBOL);
                    if (false === is_array($parameters)) {
                        $parameters = array($parameters);
                    }
                } else {
                    $parameters = array();
                }
                $parameters[] = '...';
                return $parameters;

            case '#function_call':
                $selfFunction = ($children[0]->getId() === '#table_access_self');
                $symbol       = $children[0]->accept($this, $handle, self::AS_SYMBOL);
                if (true === isset($children[1])) {
                    $arguments = $children[1]->accept($this, $handle, self::AS_SYMBOL);
                } else {
                    $arguments = array();
                }
//                var_dump($symbol);

                if ($symbol instanceof \Hoathis\Lua\Model\Value) {
                    $closure = $symbol->getValue();
                } else {
//                    var_dump($children[0]);
//                    var_dump($symbol);
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

                    if (false === isset($this->_environment[$symbol])) {
                        throw new \Hoathis\Lua\Exception\Interpreter(
                        'Unknown symbol %s()', 42, $symbol);
                    }
                    $closure = $this->_environment[$symbol]->getValue()->getValue();
                    if (!($closure instanceof \Hoathis\Lua\Model\Closure)) {
                        throw new \Hoathis\Lua\Exception\Interpreter(
                        'Symbol %s() is not a function.', 42, $symbol);
                    }
                }

                $oldEnvironment = $this->_environment;
                //$this->_environment = $closure;
                if (true === $selfFunction) {           // a function called with colon in a table receives a hidden parameter called self (the function container)
                    array_unshift($arguments, $symbol->getContainer());
                }
                $out = $closure->call($arguments, $this);
                //$this->_environment = $oldEnvironment;

                return $out;

            case '#return':
                if (false === empty($children)) {
                    $val = $children[0]->accept($this, $handle, self::AS_VALUE);
                    return new \Hoathis\Lua\Model\ReturnedValue($val);
                }
                break;

            case '#arguments':
                if (false === empty($children)) {
                    $children[0] = $children[0]->accept($this, $handle, self::AS_VALUE);
                    if ($children[0] instanceof \Hoathis\Lua\Model\ValueGroup) {
                        $children = $children[0]->getValue();
                    }
                }

                return $children;

            case '#parameters':
                foreach ($children as &$child) {
                    $child = $child->accept($this, $handle, $eldnah);
                }

                return $children;

            case '#table':
                $arr          = array();
                $numericIndex = 1;      // this variable is for compatibility between php array and lua table (first numeric index is 1)
                foreach ($children as $child) {
                    $field = $child->accept($this, $handle, $eldnah);
                    $value = $field['value'];
                    if (true === isset($field['key'])) {
                        $key       = $field['key'];
                        $arr[$key] = $value;
                    } else {
                        $arr[$numericIndex] = $value;
                        $numericIndex++;
                    }
                }
                $newVal = new \Hoathis\Lua\Model\Value($arr, \Hoathis\Lua\Model\Value::REFERENCE);
                return $newVal;

            case '#field_val':
            case '#field_name':
            case '#field':
                $nbchildren = count($children);

                switch ($nbchildren) {
                    case 1:
                        return array('value' => $children[0]->accept($this, $handle, self::AS_VALUE));
                    case 2:
                        if ('#field_val' === $type) {
                            $nameChild = $children[0]->accept($this, $handle, self::AS_VALUE)->getValue();
                        } else {
                            $nameChild = $children[0]->accept($this, $handle, self::AS_SYMBOL);
                        }
                        $valueChild = $children[1]->accept($this, $handle, self::AS_VALUE);

                        return array('key' => $nameChild, 'value' => $valueChild);
                }
                break;

            case '#table_access_self':
            case '#table_access':
                $precValue = $children[0]->accept($this, $handle, self::AS_VALUE);
                if (true === is_null($precValue->getValue())) {
                    throw new \Hoathis\Lua\Exception\Interpreter(
                    'Unknown symbol %s', 1, $children[0]->getValueValue());
                }
                $symbol = $children[0]->getValueValue();
                $var    = $precValue->getValue();
                if (false === is_array($var) && (!($var instanceof \ArrayAccess))) {
                    throw new \Hoathis\Lua\Exception\Interpreter(
                    'Symbol %s is not a table', 1, $children[0]->getValueValue());
                }

                $nbchildren = count($children);
                $sep_       = '.';
                $_sep       = '';
                $mode       = self::AS_SYMBOL;
                $parentVar  = null;
                for ($i = 1; $i < $nbchildren - 1; $i++) {
                    if ($children[$i]->getValueToken() === 'bracket_') {
                        $sep_ = '[\'';
                        $_sep = '\']';
                        $mode = self::AS_VALUE;
                    } else {
//                        var_dump($children[$i]);
                        if ($mode === self::AS_VALUE) {
                            $field = $children[$i]->accept($this, $handle, self::AS_VALUE)->getValue();
                        } else {
                            $field = $children[$i]->accept($this, $handle, self::AS_SYMBOL);
                        }
//                        var_dump($field);
                        $symbol .= $sep_ . $field . $_sep;
                        if (($var instanceof \Hoathis\Lua\Model\Wrapper && false === isset($var[$field])) || (true === is_array($var)
                            && false === array_key_exists($field, $var) )) {
                            throw new \Hoathis\Lua\Exception\Interpreter(
                            'attempt to index field \'%s\' (a nil value) in %s', 13, array($field, $symbol));
                        } else {
                            $parentVar = $var[$field];
                            if ($parentVar instanceof \Hoathis\Lua\Model\Value) {
                                $var = $parentVar->getValue();
                            } else {
                                $var = $parentVar;
                            }
                            $sep_ = '.';
                            $_sep = '';
                            $mode = self::AS_SYMBOL;
                            if (false === is_array($var) && !($var instanceof \Hoathis\Lua\Model\Wrapper)) {
                                throw new \Hoathis\Lua\Exception\Interpreter(
                                'Symbol %s is not a table', 1, $symbol);
                            }
                        }
                    }
                }
                if ($mode === self::AS_VALUE) {
                    $field = $children[$i]->accept($this, $handle, self::AS_VALUE)->getValue();
                } else {
                    $field = $children[$i]->getValueValue();
                }
                if ($parentVar instanceof \Hoathis\Lua\Model\Value) {
                    $precValue = $parentVar;
                }
                $symbol .= $sep_ . $children[$i]->getValueValue() . $_sep;
//                var_dump($symbol);
//                    var_dump($var);
                if (($var instanceof \Hoathis\Lua\Model\Wrapper && false === isset($var[$field])) || (true === is_array($var)
                    && false === array_key_exists($field, $var) )) {
                    if ($eldnah === self::AS_VALUE) {
                        $var[$field] = null;
                    } else {
                        $newval      = null;
                        $var[$field] = new \Hoathis\Lua\Model\Value($newval);
                        if ($parentVar instanceof \Hoathis\Lua\Model\Value) {
                            $parentVar->setValue($var);
                        }
                    }
                }
                $precValue->setValue(new \Hoathis\Lua\Model\Value($var));
                if ($var[$field] instanceof \Hoathis\Lua\Model\Value && '#table_access_self' === $type) {
                    $var[$field]->setContainer($precValue);
                }
//                var_dump($var[$field]);
                return $var[$field];


            case '#and':
                $leftVal = $children[0]->accept($this, $handle, $eldnah);
                if (self::valueAsBool($leftVal->getValue())) {
                    return $children[1]->accept($this, $handle, $eldnah);
                } else {
                    return new \Hoathis\Lua\Model\Value(false);
                }
                break;


            case '#or':
                $leftVal = $children[0]->accept($this, $handle, $eldnah);
                if (self::valueAsBool($leftVal->getValue())) {
                    return $leftVal;
                }
                return $children[1]->accept($this, $handle, $eldnah);

            case '#not':
                $val = $children[0]->accept($this, $handle, $eldnah);
                return new \Hoathis\Lua\Model\Value(!self::valueAsBool($val));

            case '#if':
                $conditionPos = 0;
                $ifDone       = false;
                $nbchildren   = count($children);
                // loop for each if/elseif
                while (false === $ifDone) {
                    $conditions = $children[$conditionPos]->accept($this, $handle, self::AS_VALUE);
                    if (true === self::valueAsBool($conditions->getValue())) {
                        $oldEnvironment = $this->_environment;
                        $data           = $element->getData();
                        if (false === isset($data['env'])) {
                            $data['env'] = new \Hoathis\Lua\Model\Environment('block', $this->_environment);
                        }
                        $this->_environment = $data['env'];
                        $val                = $children[$conditionPos + 1]->accept($this, $handle, $eldnah);
                        if ($val instanceof \Hoathis\Lua\Model\ReturnedValue || $val instanceof \Hoathis\Lua\Model\BreakStatement) {
                            $this->_environment = $oldEnvironment;
                            return $val;
                        }
                        $ifDone = true;
                    }
                    if ($conditionPos + 3 < $nbchildren) {      // there is an else or elseif part
                        if ('elseif' === $children[$conditionPos + 2]->getValueToken()) {
                            $conditionPos = $conditionPos + 3;  // condition of elseif
                        } else {            // the else statement
                            $oldEnvironment = $this->_environment;
                            $data           = $element->getData();
                            if (false === isset($data['env'])) {
                                $data['env'] = new \Hoathis\Lua\Model\Environment('block', $this->_environment);
                            }
                            $this->_environment = $data['env'];
                            $val                = $children[$conditionPos + 3]->accept($this, $handle, $eldnah);
                            if ($val instanceof \Hoathis\Lua\Model\ReturnedValue || $val instanceof \Hoathis\Lua\Model\BreakStatement) {
                                $this->_environment = $oldEnvironment;
                                return $val;
                            }
                            $ifDone = true;
                        }
                    } else {        // nothing more to do
                        $ifDone = true;
                    }
                }
                if (true === isset($oldEnvironment)) {
                    $this->_environment = $oldEnvironment;
                }
                break;

            case '#do_while_loop':
            case '#while_loop':
                $nbchildren = count($children);

                // store current environment and initiate loop environment
                $oldEnvironment = $this->_environment;
                $data           = $element->getData();
                if (false === isset($data['env'])) {
                    $data['env'] = new \Hoathis\Lua\Model\Environment('block', $this->_environment);
                }

                if ('#while_loop' === $type) {
                    $condChanger          = false;                    // used to invert the loop end condition
                    $conditionPos         = 0;                      // in while_loop condition is at the beginning
                    $firstStmt            = 1;
                    $lastStmt             = $nbchildren - 1;
                    $conditionEnvironment = $this->_environment;        // while condition is evaluated with parent environment
                    $condition            = $children[$conditionPos]->accept($this, $handle, $eldnah);
                } else {
                    $condChanger          = true;                    // used to invert the loop end condition
                    $conditionPos         = $nbchildren - 1;        // in do_while_loop condition is at the end
                    $firstStmt            = 0;
                    $lastStmt             = $nbchildren - 2;
                    $conditionEnvironment = $data['env'];               // repeat until condition is evaluated with nested environment
                    $condition            = new \Hoathis\Lua\Model\Value(false); // simulate first condition to iterate at least once with "repeat" loop
                }
                $val                = null;
                $this->_environment = $data['env'];
                while (($condChanger xor true === self::valueAsBool($condition->getValue())) && !($val instanceof \Hoathis\Lua\Model\BreakStatement)) {      // break stop the loop
                    for ($i = $firstStmt; $i <= $lastStmt && !($val instanceof \Hoathis\Lua\Model\BreakStatement); $i++) {
                        $val = $children[$i]->accept($this, $handle, $eldnah);
                        if ($val instanceof \Hoathis\Lua\Model\ReturnedValue) {
                            $this->_environment = $oldEnvironment;
                            return $val;            // there is a return in the while
                        }
                    }
                    $this->_environment = $conditionEnvironment;        // condition must be evaluated in a specific environment
                    $condition          = $children[$conditionPos]->accept($this, $handle, $eldnah);
                    $this->_environment = $data['env'];
                }
                $this->_environment = $oldEnvironment;
                break;

            case '#for_loop':
                $oldEnvironment = $this->_environment;
                $data           = $element->getData();
                if (false === isset($data['env'])) {
                    $data['env'] = new \Hoathis\Lua\Model\Environment('block', $this->_environment);
                }
                $this->_environment = $data['env'];
                $nbchildren         = count($children);
                $varName            = $children[0]->accept($this, $handle, $eldnah);
                $firstVal           = $children[1]->accept($this, $handle, $eldnah)->getValue();
                $lastVal            = $children[2]->accept($this, $handle, $eldnah)->getValue();
                if ($nbchildren === 5) {
                    $step = $children[3]->accept($this, $handle, $eldnah)->getValue();
                    $code = $children[4];
                } else {
                    $step = 1;
                    $code = $children[3];
                }
                for ($i = $firstVal; $i <= $lastVal; $i += $step) {
                    $this->setValueToSymbol($varName, $i, true);
                    $stmtValue = $code->accept($this, $handle, $eldnah);
                    if ($stmtValue instanceof \Hoathis\Lua\Model\BreakStatement) {
                        break;
                    } elseif ($stmtValue instanceof \Hoathis\Lua\Model\ReturnedValue) {
                        $returnedValue = $stmtValue;
                        break;
                    }
                }
                $this->_environment = $oldEnvironment;
                if (true === isset($returnedValue)) {
                    return $returnedValue;
                }
                break;

            case '#for_in_loop':
                $oldEnvironment     = $this->_environment;
                $this->_environment = new \Hoathis\Lua\Model\Environment('block', $this->_environment);
                $forVars            = $children[0]->accept($this, $handle, self::AS_SYMBOL);
                $iterator           = $children[1]->accept($this, $handle, self::AS_VALUE);
                $forBlock           = $children[2];
                if ($iterator instanceof \Hoathis\Lua\Model\ValueGroup) {
                    $iteratorValues = $iterator->getValue();
                    if (!($iteratorValues[0]->getValue() instanceof \Hoathis\Lua\Model\Closure)) {
                        $this->_environment = $oldEnvironment;
                        throw new \Hoathis\Lua\Exception\Interpreter(
                        'Invalid first value in for in loop', 1);
                    }
                    $iteratorFunction = $iteratorValues[0]->getValue();
                    // TODO maybe add more controls about iteratorSubject type, etc.
                    $iteratorSubject  = $iteratorValues[1];
                    if (true === isset($iteratorValues[2])) {
                        $iteratorStart = $iteratorValues[2];
                    } else {
                        $iteratorStart = new \Hoathis\Lua\Model\Value(null);
                    }
                    $args = array($iteratorSubject, $iteratorStart);
                    //$oldForEnvironment = $this->_environment;
                    //$this->_environment = $iteratorFunction;
                    $vals = $iteratorFunction->call($args, $this);
                    //$this->_environment = $oldForEnvironment;
                    if (!($vals instanceof \Hoathis\Lua\Model\ValueGroup)) {
                        throw new \Hoathis\Lua\Exception\Interpreter(
                        'Invalid iterator that does\'nt return two values', 1);
                    }
                    while (false === is_null($vals[0])) {
                        $this->setValueGroupToValueGroup($forVars, $vals, true);
                        $blockVal = $forBlock->accept($this, $handle, self::AS_VALUE);
                        if ($blockVal instanceof \Hoathis\Lua\Model\ReturnedValue) {
                            $this->_environment = $oldEnvironment;
                            return $blockVal;
                        } elseif ($blockVal instanceof \Hoathis\Lua\Model\BreakStatement) {
                            break;
                        }
                        $args = array($iteratorSubject, $vals[0]);
                        //$oldForEnvironment = $this->_environment;
                        //$this->_environment = $iteratorFunction;
                        $vals = $iteratorFunction->call($args, $this);
                        //$this->_environment = $oldForEnvironment;
                        if (false === is_null($vals)            // null is the value of a non returning function
                            && !($vals instanceof \Hoathis\Lua\Model\ValueGroup)) {
                            throw new \Hoathis\Lua\Exception\Interpreter(
                            'Invalid iterator that does\'nt return two values', 1);
                        }
                    }
                }
                $this->_environment = $oldEnvironment;
                break;

            case '#break':
                return new \Hoathis\Lua\Model\BreakStatement(null);

            case 'token':
                $token = $element->getValueToken();
                $value = $element->getValueValue();

                switch ($token) {

                    case 'identifier':
                        if (self::AS_VALUE === $eldnah) {
                            return $this->_environment[$value]->getValue();
                        }
                        return $value;

                    case 'number':
                        if (intval($value) == $value) {
                            // parse $value string as int
                            $value = intval($value);
                        } else {
                            // parse $value string as float
                            $value = floatval($value);
                        }

                        return new \Hoathis\Lua\Model\Value($value);

                    case 'string':
                        $val = preg_replace('/([\'"])(.*?)\1/', '$2', $value);
                        return new \Hoathis\Lua\Model\Value($val); //@todo attention ca trim trop!
                    case 'longstring':
                        $val = preg_replace('/\[(=*)\[((?:.|\n)*?)\]\1\]/', '$2', $value);
                        return new \Hoathis\Lua\Model\Value($val); //@todo attention ca trim trop!

                    case 'nil':
                        return new \Hoathis\Lua\Model\Value(null);

                    case 'false':
                        return new \Hoathis\Lua\Model\Value(false);

                    case 'true':
                        return new \Hoathis\Lua\Model\Value(true);

                    case 'tpoint':
                        if (false === $this->_environment->isFunctionContext()) {
                            throw new \Hoathis\Lua\Exception\Interpreter(
                            'Symbol ... is not available outside of function.', 1, $token);
                        }
                        if (false === isset($this->_environment['...'])) {
                            throw new \Hoathis\Lua\Exception\Interpreter(
                            'Symbol ... is unknown in this function.', 1, $token);
                        }
                        return $this->_environment['...']->getValue();

                    default:
                        throw new \Hoathis\Lua\Exception\Interpreter(
                        'Token %s is not yet implemented.', 1, $token);
                }
                break;

            default:
                throw new \Hoathis\Lua\Exception\Interpreter(
                '%s is not yet implemented.', 2, $type);
        }
    }

    public static function valueAsBool($val)
    {
        if (true === is_null($val) || false === $val) {
            return false;
        } else {
            return true;
        }
    }

    /**
     *
     */
    public function getRoot()
    {

        return $this->_environment;
    }

    /**
     *
     */
    public function setRoot(\Hoathis\Lua\Model\Environment $env)
    {

        $this->_environment = $env;
    }

    public function setValueToSymbol($symbol, $value, $local = false)
    {
        if (true === $local && false === $this->_environment->localExists($symbol)) {
            $this->_environment->localSet($symbol,
                new \Hoathis\Lua\Model\Variable(
                $symbol, $this->_environment
            ));
        } elseif (false === isset($this->_environment[$symbol])) {
            $this->_environment[$symbol] = new \Hoathis\Lua\Model\Variable(
                $symbol, $this->_environment
            );
        }
        if ($value instanceof \Hoathis\Lua\Model\Value) {
            if ($value->isReference()) {
                //$value->copyAsReferenceTo($this->_environment[$symbol]);
                $this->_environment[$symbol]->setValue($value);
            } else {
                $this->_environment[$symbol]->setValue($value);
            }
        } elseif ($value instanceof \Hoathis\Lua\Model\WrapperScalar) {
            $this->_environment[$symbol]->setValue(new \Hoathis\Lua\Model\Value($value->getValue()));
        } else {
            $this->_environment[$symbol]->setValue(new \Hoathis\Lua\Model\Value($value));
        }
    }

    public function setValueGroupToValueGroup($psymbols, $pvalues, $local = false)
    {
        if ($psymbols instanceof \Hoathis\Lua\Model\ValueGroup) {
            $symbols = $psymbols->getValue();
        } else {
            $symbols = $psymbols;
        }
        if ($pvalues instanceof \Hoathis\Lua\Model\ValueGroup) {
            $values = $pvalues->getValue();
        } else {
            $values = $pvalues;
        }
        $nbSymbols = count($symbols);
        $nbValues  = count($values);
        for ($i = 0; $i < $nbSymbols; $i++) {
            $symbol = $symbols[$i];
            if ($i < $nbValues) {
                $value = $values[$i];
            } else {
                $value = new \Hoathis\Lua\Model\Value(null);
            }
            if ($symbol instanceof \Hoathis\Lua\Model\Value) {      // use for table access
                if ($value instanceof \Hoathis\Lua\Model\Value) {
                    if ($value->isReference()) {
                        $symbol->setReference($value->getReference());
                    } else {
                        $symbol->setValue($value->getValue());
                    }
                } else {
                    $symbol->setValue($value);
                }
            } else {            // $symbol is an identifier
                $this->setValueToSymbol($symbol, $value, $local);
            }
        }
    }

    public function stdPrint()
    {
        $args = func_get_args();
        $sep  = '';
        foreach ($args as $arg) {
            echo $sep;
            if (true === is_null($arg)) {
                echo 'nil';
            } elseif (false === $arg) {
                echo 'false';
            } elseif (true === is_array($arg)) {
                echo 'array';
            } elseif (true === is_callable($arg) || $arg instanceof \Hoathis\Lua\Model\Closure) {
                echo 'function';
            } else {
                echo $arg;
            }
            $sep = "\t";
        }
        echo PHP_EOL;
    }

    public function stdIpairs($table)
    {
        $iter = new \Hoathis\Lua\Model\Closure('__iter', $this->_environment, array(),
            function ($a, $i) {
            $i = $i + 1;
            if (isset($a[$i])) {
                $returnedValues = new \Hoathis\Lua\Model\ValueGroup(array());
                $returnedValues->addValue(new \Hoathis\Lua\Model\Value($i));
                $returnedValues->addValue(new \Hoathis\Lua\Model\Value($a[$i]));
                return $returnedValues;
            }
            return null;
        });
        $returnedValues = new \Hoathis\Lua\Model\ValueGroup(array());
        $returnedValues->addValue(new \Hoathis\Lua\Model\Value($iter));
        $returnedValues->addValue(new \Hoathis\Lua\Model\Value($table, \Hoathis\Lua\Model\Value::REFERENCE));
        $returnedValues->addValue(new \Hoathis\Lua\Model\Value(0));
        return $returnedValues;
    }

    public function stdNext($table, $key)
    {
        $vg   = new \Hoathis\Lua\Model\ValueGroup(null);
        $keys = array_keys($table);
        usort($keys,
            function ($a, $b) {         // the docs says it takes keys in an undefined order, but it seems Lua5.1 list numeric keys first and other keys then
            if (is_numeric($a)) {
                if (is_numeric($b)) {
                    return $a - $b;
                } else {
                    return -1;
                }
            } elseif (is_numeric($b)) {
                return 1;
            } else {
                return $a <= $b ? -1 : 0;
            }
        });

        if (true === is_null($key)) {       // null when the loop is called for the first element
            $place = 0;
        } else {
            $place = 1 + array_search($key, $keys);
        }

        if ($place < count($keys)) {
            $newKey = $keys[$place];
            $vg->addValue(new \Hoathis\Lua\Model\Value($newKey));
            $vg->addValue(new \Hoathis\Lua\Model\Value($table[$newKey]));
        } else {
            $vg->addValue(null);
            $vg->addValue(new \Hoathis\Lua\Model\Value(null));
        }
        return $vg;
    }

    public function stdPairs($table)
    {
        $returnedValues = new \Hoathis\Lua\Model\ValueGroup(array());
        $returnedValues->addValue(new \Hoathis\Lua\Model\Value(new \Hoathis\Lua\Model\Closure('__next',
            $this->_environment, array(), array($this, 'stdNext'))));
        $returnedValues->addValue(new \Hoathis\Lua\Model\Value($table, \Hoathis\Lua\Model\Value::REFERENCE));
        $returnedValues->addValue(new \Hoathis\Lua\Model\Value(null));
        return $returnedValues;
    }
}