<?php

namespace tests\unit;

use atoum;
require __DIR__ . '/Lua.php';
class Luatoum extends atoum {

    public function __construct() {
        parent::__construct();
        $this->define->Lua = '\tests\Lua';
    }

}
