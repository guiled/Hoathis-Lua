<?php

namespace tests;

use atoum\atoum\asserter;

class Lua extends asserter {

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

    public function isParsed($value) {
        try {
            $ast = $compiler->parse($input);
            $this->pass();
        } catch (\Hoa\Compiler\Exception $e) {
            $this->fail('This code can not be parsed "' . $value . '".' . PHP_EOL . "Parser message : " . $e->getMessage());
        }
    }

    public function output($value) {

    }

}
