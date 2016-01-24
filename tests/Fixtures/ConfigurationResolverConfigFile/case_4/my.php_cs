<?php

if (!class_exists('Test4Config')) {
    class Test4Config extends Symfony\CS\Config {}
}

return Test4Config::create();
