<?php

if (!class_exists('Test2Config')) {
    class Test2Config extends Symfony\CS\Config {}
}

return Test2Config::create();
