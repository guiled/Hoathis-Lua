<?php
namespace tests\unit\Hoathis\Lua\Visitor;

use \tests\unit\Luatoum;

class Interpreter extends Luatoum {

    public function testPrint() {
        $this
            ->assert('Test if print send to output')
            ->luaOutput('print(42);')->isEqualTo("42" . PHP_EOL);
    }

    public function testArithmetic() {
        $this
            ->assert('Addition')
            ->luaOutput('print(1+2);')->isEqualTo("3" . PHP_EOL)
            ->if
            ->luaOutput('a=3;b=4;print(a+b);')->isEqualTo("7" . PHP_EOL)

            ->assert('Test left assignation of basic operators')
            ->luaOutput('print(4/2*3);')->isEqualTo("6" . PHP_EOL)
            ;
    }

    public function testPhpLuaIntegration() {
        $this
            ->if ($env = $this->getVisitor()->getRoot())
                ->and($a = 1)
                ->and($env->wrap('a', $a))
            ->assert('Wrapping scalar value : read value')
            ->luaOutput('print(a);')->isEqualTo($a . PHP_EOL)

            ->assert('Wrapping scalar value : assign value')
            ->luaOutput('b=a;print(b);')->isEqualTo($a . PHP_EOL)

            ->assert('Wrapping scalar value : write value')
            ->luaOutput('a=2;print(a);')->isEqualTo("2" . PHP_EOL)

            ->assert('Wrapping scalar value : write value from var')
            ->luaOutput('b=2;a=b;print(a);')->isEqualTo("2" . PHP_EOL)

            ->assert('Return value is retrieved by PHP')
            ->integer($this->execute('return 42;'))->isEqualTo(42)
            ->integer($this->execute('c=43;return c;'))->isEqualTo(43)
            ->integer($this->execute('c=44;return c+1;'))->isEqualTo(45)
            ->array($this->execute('return 1,2;'))->isEqualTo(array(1, 2))

            ->assert('Return a lua table as php array (beware of indexes)')
            ->array($this->execute('return {1,2};'))->isEqualTo(array(1 =>1, 2 => 2))

            ->assert('Variable receiving a wrapper scalar is returned')
            ->if($b = 2)
            ->and($env->wrap('b', $b))
            ->integer($this->execute('c=b;return c;'))->isEqualTo(2)


            //->assert('Wrapping scarlar value : retrieve value')
            //->exec('a=4;')
            //->integer($a)->isEqualTo(4)

            ;
    }
}