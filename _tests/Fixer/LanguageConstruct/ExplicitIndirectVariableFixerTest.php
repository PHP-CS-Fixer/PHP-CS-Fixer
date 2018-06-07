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

namespace PhpCsFixer\Tests\Fixer\LanguageConstruct;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Filippo Tessarotto <zoeslam@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\LanguageConstruct\ExplicitIndirectVariableFixer
 */
final class ExplicitIndirectVariableFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideTestFixCases
     * @requires PHP 7.0
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideTestFixCases()
    {
        return [
            [
                '<?php echo ${$foo}($bar);',
                '<?php echo $$foo($bar);',
            ],
            [
                '<?php echo ${$foo}[\'bar\'][\'baz\'];',
                '<?php echo $$foo[\'bar\'][\'baz\'];',
            ],
            [
                '<?php echo $foo->{$bar}[\'baz\'];',
                '<?php echo $foo->$bar[\'baz\'];',
            ],
            [
                '<?php echo $foo->{$bar}[\'baz\']();',
                '<?php echo $foo->$bar[\'baz\']();',
            ],
            [
                '<?php echo $
/* C1 */
// C2
{$foo}
// C3
;',
                '<?php echo $
/* C1 */
// C2
$foo
// C3
;',
            ],
        ];
    }
}
