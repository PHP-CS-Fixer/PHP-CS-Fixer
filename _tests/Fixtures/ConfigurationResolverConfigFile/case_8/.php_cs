<?php

$config = PhpCsFixer\Config::create();
$config->setRiskyAllowed(true);
$config->setRules(array('php_unit_construct' => true));
$config->setUsingCache(false);
$config->setFormat('xml');

return $config;
