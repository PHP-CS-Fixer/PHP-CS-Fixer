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

namespace PhpCsFixer\Tests\Fixer\Alias;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author SpacePossum
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Alias\NoAliasLanguageConstructCallFixer
 */
final class NoAliasLanguageConstructCallFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            [
                '<?php exit;',
                '<?php die;',
            ],
            [
                '<?php exit ("foo");',
                '<?php die ("foo");',
            ],
            [
                '<?php exit (1); EXIT(1);',
                '<?php DIE (1); EXIT(1);',
            ],
            [
                '<?php
                    echo "die";
                    // die;
                    /* die(1); */
                    echo $die;
                    echo $die(1);
                    echo $$die;
                ',
            ],
        ];
    }
}
