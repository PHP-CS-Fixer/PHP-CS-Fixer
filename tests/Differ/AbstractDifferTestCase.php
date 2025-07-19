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

namespace PhpCsFixer\Tests\Differ;

use PhpCsFixer\Differ\DifferInterface;
use PhpCsFixer\Preg;
use PhpCsFixer\Tests\TestCase;

/**
 * @author Andreas Möller <am@localheinz.com>
 *
 * @internal
 */
abstract class AbstractDifferTestCase extends TestCase
{
    final public function testIsDiffer(): void
    {
        $className = Preg::replace(
            '/Test$/',
            '',
            str_replace(
                'PhpCsFixer\Tests\Differ\\',
                'PhpCsFixer\Differ\\',
                static::class
            )
        );

        $differ = new $className();

        self::assertInstanceOf(DifferInterface::class, $differ);
    }

    final protected function oldCode(): string
    {
        return <<<'PHP'
            <?php

            function baz($options)
            {
                if (!array_key_exists("foo", $options)) {
                    throw new \InvalidArgumentException();
                }

                return json_encode($options);
            }

            PHP;
    }

    final protected function newCode(): string
    {
        return <<<'PHP'
            <?php

            function baz($options)
            {
                if (!\array_key_exists("foo", $options)) {
                    throw new \InvalidArgumentException();
                }

                return json_encode($options);
            }

            PHP;
    }
}
