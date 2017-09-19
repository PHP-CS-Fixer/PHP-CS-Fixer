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

namespace PhpCsFixer\Tests\Fixer\Operator;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Kuba Werłos <werlos@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Operator\PostIncrementFixer
 */
final class PostIncrementFixerTest extends AbstractFixerTestCase
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
        $preIncrementFixerTest = new PreIncrementFixerTest();

        return array_map(function (array $case) {
            return array_reverse($case);
        }, $preIncrementFixerTest->provideFixCases());
    }
}
