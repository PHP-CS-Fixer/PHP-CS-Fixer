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

namespace PhpCsFixer\Tests\Fixer\Internal;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Internal\ConfigurableFixerTemplateFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Internal\ConfigurableFixerTemplateFixer>
 *
 * @requires OS Linux|Darwin
 */
final class ConfigurableFixerTemplateFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(): void
    {
        self::markTestIncomplete('Tests not implemented for this class, run the rule on codebase and check if PHPStan accepts the changes.');
    }

    /**
     * @return iterable<int, array{}>
     */
    public static function provideFixCases(): iterable
    {
        yield []; // no tests implemented
    }
}
