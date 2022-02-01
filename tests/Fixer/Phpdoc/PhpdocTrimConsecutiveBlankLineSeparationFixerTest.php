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

    public function provideFixCases(): array
    {
        return [
            'no changes' => ['<?php /** Summary. */'],
            'only Summary and Description' => [
                '<?php
                    /**
                     * Summary.
                     *
                     * Description.
                     *
                     *
                     *
                     */',
                '<?php
                    /**
                     * Summary.
                     *
                     *
                     * Description.
                     *
                     *
                     *
                     */',
            ],
            'basic phpdoc' => [
                '<?php
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
                     */',
                '<?php
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
                     */',
            ],
            'extra blank lines in description' => [
                '<?php
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
                     */',
            ],
            'extra blank lines after annotation' => [
                '<?php
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
                     */',
                '<?php
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
                     */',
            ],
            'extra blank lines between annotations when no Summary no Description' => [
                '<?php
                    /**
                     * @param string $expected
                     * @param string $input
                     *
                     * @dataProvider provideFix56Cases
                     */',
                '<?php
                    /**
                     * @param string $expected
                     * @param string $input
                     *
                     *
                     * @dataProvider provideFix56Cases
                     */',
            ],
        ];
    }
}
