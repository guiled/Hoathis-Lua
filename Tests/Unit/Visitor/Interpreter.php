<?php
namespace Hoathis\Lua\Tests\Unit\Visitor;

use Hoathis\Lua\Tests\Luatoum;

class Interpreter extends Luatoum {

    public function testGlobalEnvironment()
    {
        $this
            ->assert('Global environment is _G and _ENV')
            ->lua('')
                ->hasVariable('_G')
                ->hasVariable('_ENV')
            ->array('_G')->isIdenticalTo($this->lua->getVariable('_ENV'))
        ;
    }

    public function testDeclaration()
    {
        $this
            ->assert('Declaration of a symbol')
            ->lua('a=true')->isParsed->hasVariable('a')
            ->boolean('a')->isTrue
            ->assert('New environment')
            ->lua('b=true')->hasNotVariable('a')->hasVariable('b')->hasNotVariable('c')
                ->code('a.=2')->isNotParsed
                ->code('local _b=2')->isParsed //@todo tester l'exécution du code
        ;
    }

    public function identifierGenerator()
    {
        $data = $this->realdom->newDisjunction();
        $regex = new \Hoa\Realdom\Regex('#[_a-zA-Z]\w*#');
        $keywords = [
            'and',
            'break',
            'do',
            'elseif',
            'else',
            'end',
            'false',
            'for',
            'function',
            'goto',
            'if',
            'in',
            'local',
            'nil',
            'not',
            'or',
            'repeat',
            'return',
            'then',
            'true',
            'until',
            'while'];
        // prevent to generate a reserved word
        array_walk($keywords, [$regex, 'discredit']);
        $data[] = $regex;
        //$data = $this->realdom->regex('#[_a-zA-Z]\w*#');
        return $this->sampleMany($data, 20);
    }


    /**
     * @dataProvider identifierGenerator
     * @param String $identifier
     */
    public function testDeclarationIdentifier($identifier)
    {
        $this
            ->assert('Declaration of a symbol')
            ->given($value = mt_rand(0, 3000))
            ->lua($identifier . '='.$value)->hasVariable($identifier)
            ->integer($identifier)->isEqualTo($value)
        ;
    }

    public function testPrint()
    {
        $this
            ->assert('Test if print sends to output')
            ->lua('print(41);')->outputLF('41')
            ->code('a=42;print(a);')->outputLF("42")->hasVariable('a')
            ->code('print(1.2)')->outputLF("1.2")
        ;
    }

    public function testType()
    {
        $this
            ->assert('Test type basic function')
            ->lua('print(type(1))')->outputLF('number')
            ->lua('print(type("1"))')->outputLF('string')
            ->lua('print(type(true))')->outputLF('boolean')
            ->lua('print(type(false))')->outputLF('boolean')
            ->lua('print(type(1/0))')->outputLF('number')
            ->lua('print(type(0/0))')->outputLF('number')
            ->lua('print(type(a))')->outputLF('nil')
        ;
    }

    public function testNumberNotation()
    {
        $this
            ->assert('Test float declaration')
            ->lua('a=1.2')->float('a')->isIdenticalTo(1.2)
            ->lua('a=1.0;print(a)')->outputLF('1.0')
            ->float('a')->isIdenticalTo(1.0)
        ;
    }

   public function testArithmetic()
    {
        $this
            ->assert('Addition')
            ->lua('print(1+2)')->outputLF("3")

            ->lua('a=3;b=4;print(a+b);')->outputLF("7")

            ->assert('Substraction')
            ->lua('print(100-58)')->outputLF('42')
            ->code('a=3;print(4-1-a);')->outputLF('0')
            ->code('a=2;print(4-1+a);')->outputLF('5')
            ->code('a=2;print(4-(1+a));')->outputLF('1')

            ->assert('Test division multiplication parenthesis')
            ->lua('print(2*3);')->outputLF("6")
            ->lua('print(4/2);')->outputLF("2.0")
            ->lua('print((4/2)*3);')->outputLF("6.0")
            ->lua('print(1/0)')->outputLF("inf")
            ->lua('print(2/0)')->outputLF("inf")
            ->lua('print(0/0)')->outputLF("nan")

            ->assert('Test left precedence of basic operators')
            ->lua('print(6/3*2)')->outputLF("4.0")
            ->lua('print(4/2*3/1*5);')->outputLF("30.0")
            ->lua('print(4/2*3+1*5);')->outputLF("11.0")
            ->lua('print(4/2*(3+1)*5);')->outputLF("40.0")
            ->lua('print(4-2+3)')->outputLF('5')

            ->assert('Test negative')
            ->lua('a=-2')->integer('a')->isEqualTo(-2)
            ->lua('b=a^3')->integer('b')->isEqualTo(-8)
            ->lua('c=b*b')->integer('c')->isEqualTo(64)

            ->assert('Test floor division')
            ->lua('a=9//2;')->float('a')->isEqualTo(4)
            ->lua('a=-11//3;')->float('a')->isEqualTo(-4)
            ->lua('print(1//0.0)')->outputLF('inf')

            ->assert('Test modulo')
            ->assert('Test exponentiation')
            ->lua('print(2^2)')->outputLF('4')
            ->lua('print(2^3^2)')->outputLF('512')
            ->lua('a=2;b=3;c=2;print(a^b^c)')->outputLF('512')

            ->assert('Test arithmetics with nan')
            ->lua('a=0/0')->output('')
            ->code('print(a)')->outputLF('nan')
            ;
    }

    public function testComparison()
    {
        $this
            ->assert('Compare the global environment _G and _ENV')
            ->lua('print(_G==_ENV)')->outputLF('true')
        ;
    }
/*
    public function testPhpLuaIntegration() {
        $this
            ->if($a = 1)
            ->assert('Wrapping scalar value : read value')
            ->lua('print(a);')->wrap('a', $a)->output($a . PHP_EOL)
                ->code('print(a);a=a+1;')->output($a . PHP_EOL)

            ->assert('Wrapping scalar value : assign value')
                ->code('print(a);')->output(($a+1) . PHP_EOL)
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
            ->lua('a={1,2;3};return a;')->isParsed()
            ->returnsArray()->isEqualTo(array(1=>1,2=>2,3=>3))
            ->code('return a;')->returnsArray()->isEqualTo(array(1=>1,2=>2,3=>3));

    }
*/

}
