<?php

namespace Hoathis\Lua\Tests;

use atoum\asserter;

class Lua extends asserter {

    protected $ast;
    protected $message;
    protected $executed;
    protected $output;
    protected $return;
    protected $env;
    protected $code;

    /**
     *
     * @var \Hoa\Compiler\Llk
     */
    protected $compiler;

    /**
     *
     * @var \Hoathis\Lua\Visitor\Interpreter
     */
    protected $visitor;

    public function getVisitor() {
        if (!$this->visitor) {
            $this->visitor = new \Hoathis\Lua\Visitor\Interpreter();
        }

        return $this->visitor;
    }

    public function getCompiler() {
        if (!$this->compiler) {
            $this->compiler = \Hoa\Compiler\Llk::load(
                    new \Hoa\File\Read('Grammar.pp')
            );
        }

        return $this->compiler;
    }

    protected function parse() {
        if (!$this->ast) {
            try {
                $this->ast = $this->getCompiler()->parse($this->code);
            } catch (\Hoa\Compiler\Exception $e) {
                $this->message = 'This code can not be parsed "' . $this->code . '".' . PHP_EOL . "Parser message : " . $e->getMessage();
            }
        }
    }

    protected function execute() {
        if (!$this->executed) {
            $this->parse();
            try {
                ob_start();
                $this->return = $this->getVisitor()->visit($this->ast);
                $this->executed = true;
                $this->output = ob_get_clean();
            } catch (\Hoathis\Lua\Exception\Interpreter $e) {
                $this->message = 'There is a problem during execution of "' . $value . '"' . PHP_EOL . "Visitor message : " . $e->getMessage();
            }
        }
    }

    public function setWith($value = null) {
        parent::setWith($value);
        return $this->code($value);
    }

    public function code($code) {
        $this->code = $code;
        $this->ast = null;
        $this->executed = false;
        $this->return = null;
        $this->output = null;
        $this->env = null;

        return $this;
    }

    public function wrap($name, &$value) {
        if (!$this->env) {
            $this->env = $this->getVisitor()->getRoot();
        }
        $this->env->wrap($name, $value);

        return $this;
    }

    public function isParsed($failMessage = null) {
        $this->parse();
        if ($this->ast) {
            $this->pass();
        } else {
            $this->fail($failMessage !== null ? $failMessage : sprintf('Lua Code "%s" can not be parsed', $this->code));
        }

        return $this;
    }

    public function isNotParsed($failMessage = null) {
        $this->parse();
        if (!$this->ast) {
            $this->pass();
        } else {
            $this->fail($failMessage !== null ? $failMessage : sprintf('Lua Code "%s" can be parsed', $this->code));
        }

        return $this;
    }

    public function output($value, $failMessage = null) {
        $this->execute();

        if ($this->output === $value) {
            $this->pass();
        } else {
            $this->fail($failMessage !== null ? $failMessage : sprintf('Lua Code "%s" does not output "%s" but "%s"', $this->code, $value, $this->output));
        }

        return $this;
    }


    public function returns($value, $failMessage = null) {
        $this->execute();

        if ($this->return === $value) {
            $this->pass();
        } else {
            $this->fail($failMessage !== null ? $failMessage : sprintf('Lua Code "%s" does not return "%s"', $this->code, $value));
        }

        return $this;
    }

    public function reset() {
        $this->visitor = null;

        return $this;
    }

    public function assert($case = null) {
        $this->generator->assert($case);

        return $this;
    }

    public function returnsArray() {
        $this->execute();
        if (\is_array($this->return)) {
            return $this->generator->array($this->return);
        } else {
            $this->fail('Lua Code "%s" does not return an array', $this->code);
        }

        return $this;
    }

}
