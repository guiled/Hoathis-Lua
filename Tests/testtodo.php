<?php

require __DIR__ . '/../../../Hoa/Core/Core.php';

from('Hoa')
        ->import('File.Read')
        ->import('Compiler.Llk.~')
        ->import('Compiler.Visitor.Dump');

from('Hoathis')
        ->import('Lua.Visitor.Interpreter');
$compiler = \Hoa\Compiler\Llk::load(
                new \Hoa\File\Read('hoa://Library/Lua/Grammar.pp')
);

$tests = [
    [
        'desc' => 'Acces aux tableaux',
        'code' => "a={b={c=1}};a.b.c=3;print(a.b.c);a={b={c={d=1}}};a.b.c.d=3;print(a['b'].c['d']);print(a.b.c.d);",
        'output' => "3\n3\n3\n"
    ],
    [
        'desc' => 'Acces aux tableaux par symbol ou par valeur',
        'code' => "b='c';f='e';a={[b]={c={e=function () return 1 end}},d=2};print(a[b].c[f]())",
        'output' => "1\n"
    ],
    [
        'desc' => 'Return dans un IF',
        'code' => 'function a(x) if (x % 2 == 0) then return \'pair\' else return \'impair\' end end;print(a(2));print(a(3));',
        'output' => "pair\nimpair\n"
    ],
    [
        'desc' => 'Simple tests of break',
        'code' => 'i = 0;while i < 10 do print(i);i = i+1;if i == 3 then break;end;end;',
        'output' => "0\n1\n2\n"
    ],
    [
        'desc' => 'Test break at the end',
        'code' => 'i=0;while i<10 do print(i);i=i+1;break;end;',
        'output' => "0\n"
    ],
    [
        'desc' => 'Test break not at the end',
        'code' => 'print(\'must fail\');i=0;while i<10 do print(i);break;i=i+1;end;',
        'exception' => true
    ],
    [
        'desc' => 'local variables and static like variable',
        'code' => 'do local i=0; function a() i=i+1;print(i);end;end;print(i);a();a();',
        'output' => "nil\n1\n2\n"
    ],
    [
        'desc' => 'returns a function',
        'code' => 'function a() local function b(x) print(x); end; return b;end;c=a();c(42)',
        'output' => "42\n"
    ],
    [
        'desc' => 'test for in',
        'code' => 'function iter(a,i) i = i+1 local v=a[i] if v then return i,v end;end;tab={4,5,6};for k,v in iter,tab do print(k,v) end;print(k,v)',
        'output' => "1\t4\n2\t5\n3\t6\nnil\tnil\n"
    ],
    [
        'desc' => 'Environnement locaux, et repeat until',
        'code' => 'do local a=1;function c() print(a);a=a+1;end;c();end;print(a);c();repeat local a=42;print(a);until a>40;print(a);c();',
        'output' => "1\nnil\n2\n42\nnil\n3\n"
    ],
    [
        'desc' => 'Array with function',
        'code' => 'a={b={c=1}};function a.b() print(1);end;a.b()',
        'output' => "1\n"
    ],
    [
        'desc' => 'Selfed functions',
        'code' => 'a={};function a:b(x) print(self[x]) end;a[1]=\'foo\';a[2]=\'bar\';a:b(1);a.b(a,2)',
        'output' => "foo\nbar\n"
    ],
    [
        'desc' => 'Triple point notation',
        'code' => 'function a(x,...) print(x,...);end;a(1,2,3,4);',
        'output' => "1\t2\t3\t4\n"
    ],
    [
        'desc' => 'Parenthesis enclosed expression returns only first value',
        'code' => 'function a(...) print((...));end;a(1,2,3);',
        'output' => "1\n"
    ],
    [
        'desc' => 'Function that returns an array',
        'code' => 'r=function () return {a=1,b=r}; end;print(r().a)',
        'output' => "1\n"
    ],
    [
        'desc' => 'Function that returns an array containing a function...',
        'code' => 'r=function () return {a=1,b=r}; end;a={b=r};print(a.b().a)',
        'output' => "1\n"
    ],
//    [ // This test fails because of cyclic recursion problem- 20140323
//        'desc' => 'Function that returns an array containing a function...',
//        'code' => 'r=function () return {a=1,b=r}; end;a={b=r};print(a.b().b().a)',
//        'desc' => 'Function that returns an array containing a function...',
//        'code' => 'r=function () return {a=1,b=r}; end;a={b=r};print(a.b().b().a)',
//        'output' => "1\n"
//    ],

    // test.a = test.d(1)+1;print(test.a+1);print(test.getnew().getnew().a)

    /*
> function a()
>> return print,1,2
>> end;
> ((a))(42);
> ((a()))(42);
42
> (a())(42);
42
> a()(42);
42
> print(a())
function: 01A30EA0      1       2
> print((a()))
function: 01A30EA0
     */


    /*
     * function a() b={a=1,b=print} return b,42 end;(a()).b(5); --> 42
     */

    /*
     * function a() return 1,2 end;function b() return 3,4,a(); end;print(b());  --> 3  4   1   2
     * function a() return 1,2 end;function b() return 3,4,a(); end;print(b(),1); --> should display 3  1
     */
];

foreach ($tests as $test) {
    $visitor = new \Hoathis\Lua\Visitor\Interpreter();
    try {
        $ast = $compiler->parse($test['code']);
        ob_start();
        $visitor->visit($ast);
        $output = str_replace("\r", '', ob_get_clean());
    } catch (Exception $ex) {
        if (false === empty($test['exception'])) {
            $output = $test['output'] = 'ok';
        } else {
            $output = $ex;
        }
    }
    if ($output !== $test['output']) {
        echo 'FAILED for ', $test['desc'], PHP_EOL;
        echo 'Tested code :', $test['code'], PHP_EOL;
        echo 'FAILED for ', $test['desc'], PHP_EOL;
        echo 'Tested code :', $test['code'], PHP_EOL;
        echo 'Output : <', $output, '>', md5($output), PHP_EOL;
        echo 'Awaited output : <', $test['output'], '>', md5($test['output']), PHP_EOL;
    } else {
        echo 'SUCCESS for ', $test['desc'], PHP_EOL;
    }
}
