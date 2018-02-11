<?php

namespace Hoathis\Lua\Tests;

use atoum\asserter;

class Lua extends asserter
{
    protected $ast;
    protected $message;
    protected $executed;
    protected $output;
    protected $return;
    protected $env;
    protected $code;

    const LF      = "\n";
    const PREG_LF = '\n';

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

    public function __get($property)
    {
        switch (strtolower($property)) {
            case 'isparsed':
            case 'isnotparsed':
            case 'returnsarray':
            case 'dumpast':
                return $this->{$property}();
            default:
                return parent::__get($property);
        }
    }

    public function __call($method, $arguments)
    {
        switch (strtolower($method)) {
            case 'integer':
            case 'float':
            case 'string':
            case 'array':
            case 'boolean':
            case 'object':
            case 'variable':
            case 'function':
                $variableName = $arguments[0];
                if (is_string($variableName) && $this->getVisitor()->getEnvironment()->exists($variableName)) {
                    $value = $this->getVisitor()->getEnvironment()->get($variableName);
                    return parent::__call($method, [$value->toPHP()]);
                }
        }
        return parent::__call($method, $arguments);
    }

    public function getVisitor()
    {
        if (!$this->visitor) {
            $this->visitor = new \Hoathis\Lua\Visitor\Interpreter();
        }

        return $this->visitor;
    }

    public function getCompiler()
    {
        if (!$this->compiler) {
            $this->compiler = \Hoa\Compiler\Llk::load(
                    new \Hoa\File\Read('Grammar.pp')
            );
        }

        return $this->compiler;
    }

    protected function parse()
    {
        if (!$this->ast) {
            try {
                $this->ast = $this->getCompiler()->parse($this->code);
                (new \Hoathis\Lua\Visitor\LL2LR)->visit($this->ast);
            } catch (\Hoa\Compiler\Exception $e) {
                $this->message = 'This code can not be parsed "' . $this->code . '".' . PHP_EOL . "Parser message : " . $e->getMessage();
            }
        }
    }

    protected function execute()
    {
        if (!$this->executed) {
            $this->parse();
            $this->isParsed();
            try {
                ob_start();
                $this->return   = $this->getVisitor()->visit($this->ast);
                $this->executed = true;
                $this->output   = ob_get_clean();
            } catch (\Hoathis\Lua\Exception\Interpreter $e) {
                $this->message = 'There is a problem during execution of "' . $this->code . '"' . PHP_EOL . "Visitor message : " . $e->getMessage();
            }
        }
    }

    public function dumpAST()
    {
        $this->parse();
        $this->getTest()->dump((new \Hoa\Compiler\Visitor\Dump)->visit($this->ast));

        return $this;
    }

    public function setWith($value = null)
    {
        parent::setWith($value);
        return $this->code($value);
    }

    public function code($code)
    {
        $this->code     = $code;
        $this->ast      = null;
        $this->executed = false;
        $this->return   = null;
        $this->output   = null;
        $this->env      = null;

        return $this;
    }

    public function wrap($name, &$value)
    {
        if (!$this->env) {
            $this->env = $this->getVisitor()->getRoot();
        }
        $this->env->wrap($name, $value);

        return $this;
    }

    public function isParsed($failMessage = null)
    {
        $this->parse();
        if ($this->ast) {
            $this->pass();
        } else {
            $this->fail($failMessage !== null ? $failMessage : sprintf('Lua Code "%s" can not be parsed', $this->code));
        }

        return $this;
    }

    public function isNotParsed($failMessage = null)
    {
        $this->parse();
        if (!$this->ast) {
            $this->pass();
        } else {
            $this->fail($failMessage !== null ? $failMessage : sprintf('Lua Code "%s" can be parsed', $this->code));
        }

        return $this;
    }

    public function output($value, $failMessage = null)
    {
        $this->execute();

        if ($this->output === $value) {
            $this->pass();
        } else {
            $this->fail($failMessage !== null ? $failMessage : sprintf('Lua Code "%s" does not output "%s" but "%s"' . PHP_EOL . "AST : " . PHP_EOL . "%s",
                        $this->code, $value, $this->output, (new \Hoa\Compiler\Visitor\Dump())->visit($this->ast)));
        }

        return $this;
    }

    public function outputLF($value, $failMessage = null)
    {
        $this->execute();

        if ($this->output === $value . self::LF) {
            $this->pass();
        } else {
            // The following code makes the failure message more readable when it is not a "new line end caracter" error
            // by displaying value and output without this new line caracter
            $chompOutput = preg_replace('/' . self::PREG_LF . '$/', '', $this->output);
            $chompValue  = preg_replace('/' . self::PREG_LF . '$/', '', $value . self::LF);
            if ($chompOutput === $chompValue) {
                $this->fail($failMessage !== null ? $failMessage : sprintf('New line end caracter error in Lua Code "%s" that does not output "%s" but "%s"' . PHP_EOL . "AST : " . PHP_EOL . "%s",
                            $this->code, $value . self::LF, $this->output,
                            (new \Hoa\Compiler\Visitor\Dump())->visit($this->ast)));
            } else {
                $this->fail($failMessage !== null ? $failMessage : sprintf('Lua Code "%s" does not output "%s" but "%s"' . PHP_EOL . "AST : " . PHP_EOL . "%s",
                            $this->code, $chompValue, $chompOutput,
                            (new \Hoa\Compiler\Visitor\Dump())->visit($this->ast)));
            }
        }

        return $this;
    }

    public function returns($value, $failMessage = null)
    {
        $this->execute();

        if ($this->return === $value) {
            $this->pass();
        } else {
            $this->fail($failMessage !== null ? $failMessage : sprintf('Lua Code "%s" does not return "%s"',
                        $this->code, $value));
        }

        return $this;
    }

    public function reset()
    {
        $this->visitor = null;

        return $this;
    }

    public function returnsArray()
    {
        $this->execute();
        if (\is_array($this->return)) {
            return $this->generator->array($this->return);
        } else {
            $this->fail('Lua Code "%s" does not return an array', $this->code);
        }

        return $this;
    }

    public function hasVariable($variableName, $failMessage = null)
    {
        $this->execute();

        if ($this->getVisitor()->getEnvironment()->exists($variableName)) {
            $this->pass();
        } else {
            $failMessage = $failMessage ?? sprintf('Lua variable %s does not exist', $variableName);
            $this->fail($failMessage);
        }

        return $this;
    }

    public function hasNotVariable($variableName, $failMessage = null)
    {
        $this->execute();

        if (!$this->getVisitor()->getEnvironment()->exists($variableName)) {
            $this->pass();
        } else {
            $failMessage = $failMessage ?? sprintf('Lua variable %s does exist', $variableName);
            $this->fail($failMessage);
        }

        return $this;
    }

    public function getVariable($variableName)
    {
        $this->execute();

        if ($this->getVisitor()->getEnvironment()->exists($variableName)) {
            $value = $this->getVisitor()->getEnvironment()->get($variableName);
            return $value->toPHP();
        } else {
            return null;
        }
    }
}