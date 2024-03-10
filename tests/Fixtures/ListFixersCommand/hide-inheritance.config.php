<?php

$finder = PhpCsFixer\Finder::create();

$config = new PhpCsFixer\Config();
return $config
    ->setRules([
        // The rules from PSR1 will not be shown in the output of the command
        '@PSR1' => true,
        
        // Only this rule will be shown in the output of the command
        'blank_line_after_namespace' => true,
    ])
    ->setRiskyAllowed(true)
    ->setFinder($finder);
