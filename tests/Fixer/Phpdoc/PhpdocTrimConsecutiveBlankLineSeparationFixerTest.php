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

namespace PhpCsFixer\Tests\Fixer\Phpdoc;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Nobu Funaki <nobu.funaki@gmail.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocTrimConsecutiveBlankLineSeparationFixer
 */
final class PhpdocTrimConsecutiveBlankLineSeparationFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield 'no changes' => ['<?php /** Summary. */'];

        yield 'only Summary and Description' => [
            <<<'EOD'
                <?php
                                    /**
                                     * Summary.
                                     *
                                     * Description.
                                     *
                                     *
                                     *
                                     */
                EOD,
            <<<'EOD'
                <?php
                                    /**
                                     * Summary.
                                     *
                                     *
                                     * Description.
                                     *
                                     *
                                     *
                                     */
                EOD,
        ];

        yield 'basic phpdoc' => [
            <<<'EOD'
                <?php
                                    /**
                                     * Summary.
                                     *
                                     * Description.
                                     *
                                     * @var int
                                     *
                                     * @return int
                                     *
                                     * foo
                                     *
                                     * bar
                                     *
                                     *
                                     */
                EOD,
            <<<'EOD'
                <?php
                                    /**
                                     * Summary.
                                     *
                                     *
                                     * Description.
                                     *
                                     *
                                     * @var int
                                     *
                                     *
                                     *
                                     *
                                     * @return int
                                     *
                                     *
                                     * foo
                                     *
                                     *
                                     * bar
                                     *
                                     *
                                     */
                EOD,
        ];

        yield 'extra blank lines in description' => [
            <<<'EOD'
                <?php
                                    /**
                                     * Summary.
                                     *
                                     * Description has multiple blank lines:
                                     *
                                     *
                                     *
                                     * End.
                                     *
                                     * @var int
                                     */
                EOD,
        ];

        yield 'extra blank lines after annotation' => [
            <<<'EOD'
                <?php
                                    /**
                                     * Summary without description.
                                     *
                                     * @var int
                                     *
                                     * This is still @var annotation description...
                                     *
                                     * But this is not!
                                     *
                                     * @internal
                                     */
                EOD,
            <<<'EOD'
                <?php
                                    /**
                                     * Summary without description.
                                     *
                                     *
                                     * @var int
                                     *
                                     * This is still @var annotation description...
                                     *
                                     *
                                     *
                                     *
                                     * But this is not!
                                     *
                                     *
                                     *
                                     *
                                     *
                                     * @internal
                                     */
                EOD,
        ];

        yield 'extra blank lines between annotations when no Summary no Description' => [
            <<<'EOD'
                <?php
                                    /**
                                     * @param string $expected
                                     * @param string $input
                                     *
                                     * @dataProvider provideFix56Cases
                                     */
                EOD,
            <<<'EOD'
                <?php
                                    /**
                                     * @param string $expected
                                     * @param string $input
                                     *
                                     *
                                     * @dataProvider provideFix56Cases
                                     */
                EOD,
        ];
    }
}
