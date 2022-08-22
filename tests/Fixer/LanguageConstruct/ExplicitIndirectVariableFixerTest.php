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
     * @dataProvider provideTestFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public function provideTestFixCases(): array
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

    /**
     * @param mixed $expected
     * @param mixed $input
     *
     * @dataProvider provideTestFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80($expected, $input): void
    {
        $this->doTest($expected, $input);
    }

    public function provideTestFix80Cases(): array
    {
        return [
            [
                '<?php echo $foo?->{$bar}["baz"];',
                '<?php echo $foo?->$bar["baz"];',
            ],
            [
                '<?php echo $foo?->{$bar}["baz"]();',
                '<?php echo $foo?->$bar["baz"]();',
            ],
        ];
    }
}
