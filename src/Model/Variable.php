<?php

namespace Hoathis\Lua\Model;

/**
 * Class \Hoathis\Lua\Model\Variable.
 *
 * Variable.
 *
 * @author     Ivan Enderlin <ivan.enderlin@hoa-project.net>
 * @copyright  Copyright © 2007-2013 Ivan Enderlin.
 * @license    New BSD License
 */

class Variable {

    protected $_symbol      = null;
    protected $_environment = null;
    protected $_value       = null;


    public function __construct ( $symbol, Environment $environment ) {

        $this->_symbol      = $symbol;
        $this->_environment = $environment;

        return;
    }

    public function getName ( ) {

        return $this->_symbol;
    }

    public function setValue ( $value ) {

        $old          = $this->_value;
        $this->_value = $value;

        return $old;
    }

    public function getValue ( ) {

        return $this->_value;
    }
}

