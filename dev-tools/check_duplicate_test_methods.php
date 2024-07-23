#!/usr/bin/env php
<?php

declare(strict_types=1);

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

require_once __DIR__.'/../vendor/autoload.php';

$testClassNames = array_filter(
    array_keys(require __DIR__.'/../vendor/composer/autoload_classmap.php'),
    static fn (string $className): bool => str_starts_with($className, 'PhpCsFixer\Tests\\')
);

if ([] === $testClassNames) {
    echo 'Run: composer dump-autoload --optimize --working-dir=', realpath(__DIR__.'/..'), PHP_EOL;

    exit(1);
}

$duplicatesFound = false;

foreach ($testClassNames as $testClassName) {
    $class = new ReflectionClass($testClassName);

    $alreadyFoundMethods = [];
    foreach ($class->getMethods() as $method) {
        if (!str_starts_with($method->getName(), 'test')) {
            continue;
        }

        $startLine = $method->getStartLine();
        $length = $method->getEndLine() - $startLine;
        if (3 === $length) { // open and closing brace are included - this checks for single line methods
            continue;
        }

        $source = file($method->getFileName());
        $candidateContent = implode('', array_slice($source, $startLine, $length));
        if (str_contains($candidateContent, '$this->doTest(')) {
            continue;
        }

        $foundInDuplicates = false;
        foreach ($alreadyFoundMethods as $methodKey => $methodContent) {
            if ($candidateContent === $methodContent) {
                echo 'Duplicate in ', $testClassName, ': methods ', $methodKey, ' and ', $method->getName(), PHP_EOL;
                $duplicatesFound = true;
                $foundInDuplicates = true;
            }
        }
        if (!$foundInDuplicates) {
            $alreadyFoundMethods[$method->getName()] = $candidateContent;
        }
    }
}

exit($duplicatesFound ? 1 : 0);
