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

namespace PhpCsFixer\Tests\Fixer\Semicolon;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @internal
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\Semicolon\NoSinglelineWhitespaceBeforeSemicolonsFixer>
 *
 * @covers \PhpCsFixer\Fixer\Semicolon\NoSinglelineWhitespaceBeforeSemicolonsFixer
 *
 * @author John Kelly <wablam@gmail.com>
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 */
final class NoSinglelineWhitespaceBeforeSemicolonsFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<int, array{0: string, 1?: string}>
     */
    public static function provideFixCases(): iterable
    {
        yield [
            '<?php for ($uu = 0; ; ++$uu) {}',
            '<?php for ($uu = 0    ;    ; ++$uu) {}',
        ];

        yield [
            '<?php
$this
    ->setName(\'readme1\')
    ->setDescription(\'Generates the README content, based on the fix command help\')
;',
        ];

        yield [
            '<?php
$this
    ->setName(\'readme2\')
    ->setDescription(\'Generates the README content, based on the fix command help\')
    ;',
        ];

        yield [
            '<?php echo "$this->foo(\'with param containing ;\') ;";',
            '<?php echo "$this->foo(\'with param containing ;\') ;" ;',
        ];

        yield [
            '<?php $this->foo();',
            '<?php $this->foo() ;',
        ];

        yield [
            '<?php $this->foo(\'with param containing ;\');',
            '<?php $this->foo(\'with param containing ;\') ;',
        ];

        yield [
            '<?php $this->foo(\'with param containing ) ; \');',
            '<?php $this->foo(\'with param containing ) ; \') ;',
        ];

        yield [
            '<?php $this->foo("with param containing ) ; ");',
            '<?php $this->foo("with param containing ) ; ")  ;',
        ];

        yield [
            '<?php
    $foo
        ->bar(1)
        ->baz(2)
    ;',
        ];

        yield [
            '<?php
    $foo
        ->bar(1)
        //->baz(2)
    ;',
        ];

        yield [
            '<?php $this->foo("with semicolon in string) ; ");',
        ];
    }
}
