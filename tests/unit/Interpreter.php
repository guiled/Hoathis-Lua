<?php
namespace tests\unit\Hoathis\Lua\Visitor;

use \tests\unit\Luatoum;

class Interpreter extends Luatoum {

    public function testPrint() {
        $this
            ->assert('Test if print send to output')
            ->lua('print(42);')->output("42" . PHP_EOL);
    }

    public function testArithmetic() {
        $this
            ->assert('Addition')
            ->lua('print(1+2);')->output("3" . PHP_EOL)

            ->lua('a=3;b=4;print(a+b);')->output("7" . PHP_EOL)

            ->assert('Test division multiplication parenthesis')
            ->lua('print((4/2)*3);')->output("6" . PHP_EOL)

//            ->assert('Test left assignation of basic operators')
//            ->luaOutput('print(4/2*3);')->isEqualTo("6" . PHP_EOL)
            ;
    }

    public function testPhpLuaIntegration() {
        $this
            ->if($a = 1)
            ->assert('Wrapping scalar value : read value')
            ->lua('print(a);')->wrap('a', $a)->output($a . PHP_EOL)
            
            ->assert('Wrapping scalar value : assign value')
            ->lua('b=a;print(b);')->wrap('a', $a)->output($a . PHP_EOL)

            ->assert('Wrapping scalar value : write value')
            ->lua('a=2;print(a);')->wrap('a', $a)->output("2" . PHP_EOL)

            ->assert('Wrapping scalar value : write value from var')
            ->lua('b=2;a=b;print(a);')->wrap('a', $a)->output("2" . PHP_EOL)

            ->assert('Return value is retrieved by PHP')
            ->lua('return 42;')->returns(42)
            ->lua('c=43;return c;')->returns(43)
            ->lua('c=44;return c+1;')->returns(45)
            ->lua('return 1,2;')->returns(array(1, 2))

            ->assert('Return a lua table as php array (beware of indexes)')
            ->lua('return {1,2};')->returns(array(1 =>1, 2 => 2))

            ->assert('Variable receiving a wrapper scalar is returned')
            ->if($b = 2)
            ->lua('c=b;return c;')->wrap('b', $b)->lua(2)


            //->assert('Wrapping scarlar value : retrieve value')
            //->exec('a=4;')
            //->integer($a)->isEqualTo(4)

            ;
    }

    public function testArrayHandling() {
        $this
            ->assert('Simple array creation')
            ->lua('a={1,2;3};')->isParsed()
            ->lua('a={1,2;')->isNotParsed();

    }


}