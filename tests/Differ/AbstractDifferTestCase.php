<?php

/*
 * This file is part of PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace PhpCsFixer\Tests\Differ;

use PHPUnit\Framework\TestCase;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 */
abstract class AbstractDifferTestCase extends TestCase
{
    final public function testIsDiffer()
    {
        $className = preg_replace(
            '/Test$/',
            '',
            str_replace(
                'PhpCsFixer\\Tests\\Differ\\',
                'PhpCsFixer\\Differ\\',
                get_called_class()
            )
        );

        $differ = new $className();

        $this->assertInstanceOf(\PhpCsFixer\Differ\DifferInterface::class, $differ);
    }

    final protected function oldCode()
    {
        return <<<'PHP'
<?php
class Foo extends Bar {
    function __construct($foo, $bar) {
        $this->foo = $foo;
        $this->bar = $bar;
    }
}
PHP;
    }

    final protected function newCode()
    {
        return <<<'PHP'
<?php
class Foo extends Bar {
    public function __construct($foo, $bar)
    {
        $this->foo = $foo;
        $this->bar = $bar;
    }
}
PHP;
    }
}
