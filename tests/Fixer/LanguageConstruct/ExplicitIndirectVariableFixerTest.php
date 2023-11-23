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
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFixCases(): iterable
    {
        yield 'variable variable function call' => [
            '<?php echo ${$foo}($bar);',
            '<?php echo $$foo($bar);',
        ];

        yield 'variable variable array fetch' => [
            '<?php echo ${$foo}[\'bar\'][\'baz\'];',
            '<?php echo $$foo[\'bar\'][\'baz\'];',
        ];

        yield 'dynamic property access' => [
            '<?php echo $foo->{$bar}[\'baz\'];',
            '<?php echo $foo->$bar[\'baz\'];',
        ];

        yield 'dynamic property access with method call' => [
            '<?php echo $foo->{$bar}[\'baz\']();',
            '<?php echo $foo->$bar[\'baz\']();',
        ];

        yield 'variable variable with comments between dollar signs' => [
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
        ];
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, ?string $input): void
    {
        $this->doTest($expected, $input);
    }

    public static function provideFix80Cases(): iterable
    {
        yield 'dynamic property fetch with nullsafe operator' => [
            '<?php echo $foo?->{$bar}["baz"];',
            '<?php echo $foo?->$bar["baz"];',
        ];

        yield 'dynamic property fetch with nullsafe operator and method call' => [
            '<?php echo $foo?->{$bar}["baz"]();',
            '<?php echo $foo?->$bar["baz"]();',
        ];
    }
}
