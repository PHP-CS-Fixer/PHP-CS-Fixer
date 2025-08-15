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

namespace PhpCsFixer\Tests\Test;

use PhpCsFixer\Fixer\FixerInterface;
use PhpCsFixer\FixerFactory;

/**
 * @internal
 */
final class TestCaseUtils
{
    public static function getFixerByName(string $name): FixerInterface
    {
        static $fixers = null;

        if (null === $fixers) {
            $factory = new FixerFactory();
            $factory->registerBuiltInFixers();

            $fixers = [];
            foreach ($factory->getFixers() as $fixer) {
                $fixers[$fixer->getName()] = $fixer;
            }
        }

        if (!\array_key_exists($name, $fixers)) {
            throw new \InvalidArgumentException(\sprintf('Fixer "%s" does not exist.', $name));
        }

        return $fixers[$name];
    }
}
