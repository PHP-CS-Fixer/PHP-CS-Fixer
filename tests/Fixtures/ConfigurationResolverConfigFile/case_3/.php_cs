<?php

if (!class_exists('Test3Config')) {
    class Test3Config extends Symfony\CS\Config {}
}

return Test3Config::create();
