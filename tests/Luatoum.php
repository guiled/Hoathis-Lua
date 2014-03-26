<?php

namespace tests\unit;

use atoum;

class Luatoum extends atoum {

    /**
     *
     * @var \Hoa\Compiler\Llk
     */
    private $compiler;

    /**
     *
     * @var \Hoathis\Lua\Visitor\Interpreter
     */
    private $visitor;

    public function __construct() {
        parent::__construct();
        $this->define->Lua = 'tests\Lua';
    }

    protected function execute($input) {
        $ast = $this->getCompiler()->parse($input);

        return $this->getVisitor()->visit($ast);
    }

    protected function exec($input) {
        $this->execute($input);
        return $this;
    }

    protected function luaOutput($input) {
        return parent::output(function () use($input) {
            $this->exec($input);
        });
    }

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

}
