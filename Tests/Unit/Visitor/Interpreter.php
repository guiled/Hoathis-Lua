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
            ->lua('a=3;print(4-1-a);')->outputLF('0')
            ->lua('a=2;print(4-1+a);')->outputLF('5')
            ->lua('a=1+2-3+4-5+6-7+8-9')->integer('a')->isEqualTo(-3)
            ->lua('a=2;print(4-1+a);')->outputLF('5')
            ->lua('a=2;print(4-(1+a));')->outputLF('1')

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
            ->lua('a=9//2;')->integer('a')->isIdenticalTo(4)
            ->lua('a=-11//3;')->integer('a')->isIdenticalTo(-4)
            ->lua('print(1//0.0)')->outputLF('inf')

            // @todo et travailler sur la regle suivante
            /*
             * With the exception of exponentiation and float division, the arithmetic operators work as follows: If both operands are integers, the operation is performed over integers and the result is an integer. Otherwise, if both operands are numbers or strings that can be converted to numbers (see §3.4.3), then they are converted to floats, the operation is performed following the usual rules for floating-point arithmetic (usually the IEEE 754 standard), and the result is a float. 
             */
            ->lua('a=9//2*4')->integer('a')->isIdenticalTo(16)
            ->lua('a=23//2/4')->float('a')->isIdenticalTo(2.75)
            ->lua('a=2*23//3/4')->float('a')->isIdenticalTo(3.75)
            //->lua('a=9.0//2*2')->float('a')->isIdenticalTo(8.0)

            ->assert('Test modulo')
            //->lua('a=5%2')->integer('a')->isIdenticalTo(1)

            ->assert('Test exponentiation')
            ->lua('print(2^2)')->outputLF('4')
            ->lua('print(2^3^2)')->outputLF('512')
            ->lua('a=2;b=3;c=2;print(a^b^c)')->outputLF('512')

            ->assert('Test arithmetics with nan')
            ->lua('a=0/0')->output('')
            ->code('print(a)')->outputLF('nan')
            ;
    }

    public function testArithmetic2()
    {
        $this
            ->lua('print(5257^9- -2464/6-9491+5*4/337//3156//8+3//2- -8+7%26* -73)')->outputLF('3.0664916974245e+33')
            /*->lua('print(783// -3*7//6%6+4/4//5//4/7/9-3236+4+6/51-9/5+5%2618*1384%1*1596-127*1%2/3%4* -842-77+5134%223//6+7-2% -411-5//1% -9349/9*3)')->outputLF('501.65098039216')
            ->lua('print( -4127/ -85//2//9-9)')->outputLF('-7.0')
            ->lua('print(189%4//819%6-172+ -2*5/ -8* -62*1- -4624* -4+5*8// -9211//8+3*3*8^8//9%2+3+257%4-3829)')->outputLF('-22571.5')
            ->lua('print(99+754+2//3%7*692%1335//547+8628+7-9//2*4294*2832% -23*4*339+21%2-3*2^2+ -6859-9%7*3114/86//8617%1*7/2465+9* -5/4-19)')->outputLF('22927.75')
            ->lua('print(83-4558)')->outputLF('-4475')
            ->lua('print(1+1+813% -1335%343*7//5-9^3/6+ -233%4+86+ -1+5213/5//2^8%2%9%2/3/ -6666// -71-9% -18)')->outputLF('206.5')
            ->lua('print(7-3//1%4*1*9/92*4)')->outputLF('5.8260869565217')
            ->lua('print( -98//3//3/9//2375/6219%5//5*8-456+5*8%82^1*356%21)')->outputLF('-454.0')
            ->lua('print(28-1%586+3//17)')->outputLF('27')
            ->lua('print(2688+ -641/4/7//8/346+2648//7+1327-5/7554-6% -96-581*6669*9%1)')->outputLF('4482.9906675788')
            ->lua('print( -248-9/7+67*49-9+6*3-2*816//3-6^1-1/7*3*4253%2%4)')->outputLF('2492.0')
            ->lua('print(252*2//6%1%4%5/52*325% -9-3579//2//3-3*5//8+ -199/3838*5+6-633-1//2-1-3226-3/ -7+7/759*6/4572-2855/9/57//33%5/234^4//3*9- -767%2/7% -4)')->outputLF('-4446.9735232202')
            ->lua('print(3832+6+7/8798/7-887+6%2+2/657/6719-92%57/1/6283-1-6-9^4-6+5)')->outputLF('-3618.005456472')
            ->lua('print(72%8-8//22+ -843^2/41*6*8727//8383+3//8-2%2+7//6%55/18/36-1// -89% -691%116^8^4//3-4363/798/25% -481-55-6%8%7/68/6/77/ -3/5* -82)')->outputLF('-inf')
            ->lua('print( -28^9//2644+7-3454^4-6//3+7//145%8-8// -5/5+3/9+6+71^1+ -337+6^9%48+7243^5/5// -1841+2874*8-9^1//2- -39//4693+7%1-9//1962+4%248-3229/795-692//6+9//3-9//7)')->outputLF('-2.3078837511537e+15')
            ->lua('print(33*7-687// -8// -56-6-3127*6^2-8786%8^2+4+64-2//443*5*2-99//5//1+1%9%6/4)')->outputLF('-112316.75')
            ->lua('print(243*8*6156-2612/6%6* -3932-2//7^5%8/3*6%9+8//2+6112//6*5/4^1+2-2*6376+2919/19+7%5//7344/413/9124% -4952/3//8//8+8//5% -3// -517//7- -86%5^4+7^8-6*175)')->outputLF('17732262.798246')
            ->lua('print( -134*71//7*9*6/8+5+5829//352* -3957/8-2977+4/7858// -73//1-18//5%5%132%8%8//6/9/6+753%529+6%1/1673^2%8*7/2795% -389*6+5-9+3229%79/4*6// -896+2/9)')->outputLF('-19847.777777778')
            ->lua('print(326//57* -6*1//86/5464^2//8%7%5532+78+7+4^9*7//3588//2//3164-6//2// -1^6/9%2//82^1*9%9* -45-6+9//6/6259//1// -484/4+7879/5/3)')->outputLF('610.26666666667')
            ->lua('print(756-6/3%4^6*8549//7-4/8785*96// -3114/2//41%36)')->outputLF('-1721.0')
            ->lua('print( -1^5/5*6*4827%87//8+8%375*5^9- -527%1+6*2+6-5*985/3+154%2/3*2412/2%5// -87/6+1/1-6//439%4/1*8-2%34/6%485-4//3)')->outputLF('15623380.0')
            ->lua('print(7//2*7424/5/796//7/618/5/7-1%5^7- -9354-3//7^9%3*7/8+7/97//622*712+939+9^4*3*742+8-9)')->outputLF('14615077.0')
            ->lua('print( -394/3%998-542+9*7/2/5*765-149+5892*6+4+5//4/8)')->outputLF('40351.291666667')
            ->lua('print(9/94/7/2627* -745-4943*6*836/7-1%4125*6/3961- -424+1*8-53-1%4+7% -4/9-2-447-1+1-7%6562*7/9+9-5%2+2%4913/5*9% -7934-8*1825+944+ -57%982%3)')->outputLF('-3563666.5323778')
            ->lua('print( -91-7//9/9*6+9* -171*1836%65%21-3194*8228%4/1*8549//4%7^2+4+8/ -52-9135+7^6)')->outputLF('108437.84615385')
            ->lua('print( -17/ -6%8^7//4387+83^6*4^6^5+1+ -71//5/6+6%821//8//472^1%6+7* -65^6//9-738+2*8^4-4/27*6+853+9+ -16/37-4-8//5-3^5// -6159+ -8434/6-1%75-687+4338+4-5%5889)')->outputLF('inf')
            ->lua('print(9758/7486% -462^5^6-7%4977- -6378%1-1%4-21+4%2-8*3%8/ -27/1%7)')->outputLF('-inf')
            ->lua('print( -742%5^7/7+1%7%1*5774/8*3-3%5267%4+3^2// -15)')->outputLF('11050.714285714')
            ->lua('print(281/ -63*8-28-682%7829//4/6652-9%4*2// -196-4-5/9^1% -4+8*7-4* -1//8%3)')->outputLF('-9.2636514617874')
            ->lua('print(61/8943//7173-7361/8/5*1%9779*5-5//96-2-92-4^1% -7+3//6//9%123/616- -54/7-8-339/8//839//4^9* -939/435/2%889/6//1/5/972%9/2+4+6^5%8^6/2994%3//3)')->outputLF('-1007.4107142857')
            ->lua('print(6611/5*4*5*1%8^3^8*1884/3664/ -9574%2-5323*2+59* -6775-6-77+52-4576^6//2+3%8+9-4//186*166%6%94*5-8/4%6522%7-9+ -7/7*1*9252%873^2)')->outputLF('-4.5907759630864e+21')
            ->lua('print(7287+9822% -56- -989%7%3*8/ -7866^3+6592/7%77+7635*9/ -1/854)')->outputLF('7190.2517564403')
            ->lua('print( -74//3/68+5821//2%1+7*4182-2^9*2%8*872-4*292%636/9/1+ -94// -99+82//91^8-2/9//62*8)')->outputLF('29214.52124183')
            ->lua('print( -149-652// -58^9+628//8//621^7*564/ -6198%566+64)')->outputLF('-84.0')
            ->lua('print( -878^2//6^1/179/934+21+4-7-6*555+2*6^6/9* -361//7// -59-9//7*8//3411%6%2//9479//62/697/955%179/3*7124%5+39/7^6//5% -8*5*57+8//6/2+25+69/3141^2+ -9%5-4+ -7*7)')->outputLF('5722.7315156129')
            ->lua('print(81- -453//9^4/6/1/5//9/3/3/4+457^5+ -9-5-2*9/7)')->outputLF('19933382494121.0')
            ->lua('print(326-4-18+92-8%7%9+ -965%22% -2%765% -535-656^7-1-4-8*1//8*4//9226% -8*848+ -3323%8-77+6//7*2-79+ -4)')->outputLF('-5.2278952317195e+19')
            ->lua('print(1//59/828% -44//4%3*393+6+8649-9- -7%84/663*5-1^7//3^7+5*7+743-5/692+3*5+1458-9824- -871//9%2%6+1)')->outputLF('1072.4120807505')
            ->lua('print(3^7-7-7+25/84/1-3% -96/5%2/5782*2%17+7-5%1^3*4819% -6/2/5*935- -53-964+ -8% -4288*3/7%8%5/6645- -3649+7//9//15*7//3752//49)')->outputLF('4918.2978227362')
            ->lua('print(9987*2/56%5+23//1-8-4-2/ -5829-4%8738-8%5+3%5%8+4//7*577*9+8*726%6*896+3317%8%5%5+ -7536*9*4-87/2565% -7*7+7/4172/3/2783)')->outputLF('-271238.55851216')
            ->lua('print(4252/8%7/4^6/2-8431* -15+8)')->outputLF('126473.00079346')
            ->lua('print( -8+419-374%12//8/5%6//9+1//6*7311%9%8%8/8%2%6^5)')->outputLF('411.0')
            ->lua('print(1227%5+7/7964+4+5+291//93/6*9*2*4- -7*9*358//7/1* -1*1/3-61/3+1/6// -582+2+5-2195%3893-5*8% -4596-6)')->outputLF('1313.667545622')
            ->lua('print( -4652*366*7166*7/156*3977% -2251^1^4+6+678*752/1-12+9%5+4//9/2-3//555//7*48//1*1*3*8647*5*447/2*4//5)')->outputLF('509419.46166992')
            ->lua('print( -97+1^8%8+48/1//8^3* -96+5415-7-1%63+9-3//8/66%2671*1363-9*5* -26//24+32+496/8/7%3/3465+8+1*5+7//4-6-7+1+8%7*51^8// -7)')->outputLF('-6538277790369.0')
            ->lua('print( -8%6/1%4241+ -3818/49*1+8-5//7+926/ -1%7//9*8^9%474/9738+56/24-6-812/2/6//5/1//286/5-8%3-81// -51)')->outputLF('-69.585034013605')
            ->lua('print(22%1*731%2/9/ -965+7^3+9+6//5+8+3329+3^9*1*8276*85+5- -357/6-4%3/473/6%1/7/1-9%7^6^2+2/1//1- -2-2//4+5+66)')->outputLF('13846207000.5')
            ->lua('print(57/7374^4+2//3//1//112%5//2%1262*3+4%2587-3+1%6//3675%9/6^8*7-1-9-9-1%15/6-1-3531+3-6-762/8*7^8+2146//8*1//4+6^9+8222/687)')->outputLF('-539023073.44869')
            ->lua('print(9/4*2264-1^9*4%7^8+5%8//7/969/4/ -8+5*7- -86*1//837/86- -3)')->outputLF('5128.011627907')
             * 
             */
        ;
    }

    public function testComparison()
    {
        $this
            ->assert('Compare the global environment _G and _ENV')
            ->lua('print(_G==_ENV)')->outputLF('true')
        ;
    }

/*    public function testMultiAssignation()
    {
        $this
            ->assert('Test the assignation of many variables with coma separator')
            ->lua('a,b=1,2')
                ->integer('a')->isEqualTo(1)
                ->integer('b')->isEqualTo(2)
            ->assert('Multiassignation with too few values')
            ->lua('a,b=1;print(b)')
                ->integer('a')->isEqualTo(1)
                ->outputLF('nil')
            ->assert('Multiassignation order')
            ->lua('a,a=1,2')
                ->integer('a')->isEqualtTo(1)
            ->lua(<<<lua
local t = setmetatable({}, {__newindex =
   function(t, k, v)
      print(k)
   end
})

local function f(s)
   print(s)
   return s:upper()
end

t[f("a")], t[f("b")] = f("c"), f("d")
lua
                )->output("a\nb\nc\nd\nB\nA\n")
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
