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

namespace PhpCsFixer\Tests\Fixer\FunctionNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 * @coversNothing
 */
final class NoOptionalArgumentsWithDefaultValueFixerTest extends AbstractFixerTestCase
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
                '<?php htmlspecialchars($var);',
                '<?php htmlspecialchars($var, ENT_COMPAT, null, true);',
            ],
            [
                '<?php htmlspecialchars($var, encoding: "UTF-8");',
                '<?php htmlspecialchars($var, ENT_COMPAT, "UTF-8");',
            ],
            [
                '<?php htmlspecialchars(encoding: "UTF-8", string: $var);',
                '<?php htmlspecialchars(flags: ENT_COMPAT, encoding: "UTF-8", string: $var);',
            ],
        ];
    }
}
