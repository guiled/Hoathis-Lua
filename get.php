<?php

require 'vendor/autoload.php';

$grammar  = new Hoa\File\Read('hoa://Library/Regex/Grammar.pp');
$compiler = Hoa\Compiler\Llk\Llk::load($grammar);
$nbop = 10;
$ast      = $compiler->parse("(?:(?: \-|.{0}|.{0}|.{0})[1-9]{1,4})(?:(?:\+|/|\*|\-|//|%)(?: \-|.{0}|.{0}|.{0})[1-9]{1,4}|(?:\+|/|\*|\-|//|\^|%)[1-9]){1,$nbop}");

$generator = new Hoa\Regex\Visitor\Isotropic(new Hoa\Math\Sampler\Random());

//for ($i = 0; $i < 500; $i++) {
    $calcul = $generator->visit($ast);
    $result = exec("lua -e \"print($calcul)\"");
    echo "lua('print($calcul)')->outputLF('$result')", PHP_EOL;
    //echo "print($calcul)", PHP_EOL;
//}