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
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\LanguageConstruct\GetClassToClassKeywordFixer
 *
 * @requires PHP 8.0
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\LanguageConstruct\GetClassToClassKeywordFixer>
 *
 * @author John Paul E. Balandan, CPA <paulbalandan@gmail.com>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class GetClassToClassKeywordFixerTest extends AbstractFixerTestCase
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
            '
<?php

$before = $before::class;
$after = $after::class;
',
            '
<?php

$before = get_class($before);
$after = get_class($after);
',
        ];

        yield [
            '<?php $abc::class;',
            '<?php get_class($abc);',
        ];

        yield [
            '<?php $a::class  ;',
            '<?php get_class( $a );',
        ];

        yield [
            '<?php $b::class;',
            '<?php \get_class($b);',
        ];

        yield [
            '<?php $c::class;',
            '<?php GET_class($c);',
        ];

        yield [
            '<?php $d::class/* a */;',
            '<?php get_class($d/* a */);',
        ];

        yield [
            '<?php $e::class /** b */;',
            '<?php get_class($e /** b */);',
        ];

        yield [
            '<?php $f::class   ;',
            '<?php get_class ( $f );',
        ];

        yield [
            '<?php $g::class/* x */  /* y */;',
            '<?php \get_class(/* x */ $g /* y */);',
        ];

        yield [
            '<?php $h::class;',
            '<?php get_class(($h));',
        ];

        yield [
            "<?php\necho \$bar::class\n    \n;\n",
            "<?php\necho get_class(\n    \$bar\n);\n",
        ];

        yield [
            '<?php get_class;',
        ];

        yield [
            '<?php get_class($this);',
        ];

        yield [
            '<?php get_class();',
        ];

        yield [
            '<?php get_class(/* $a */);',
        ];

        yield [
            '<?php get_class(/** $date */);',
        ];

        yield [
            '<?php $a = get_class(12);',
        ];

        yield [
            '<?php get_class($a.$b);',
        ];

        yield [
            '<?php get_class($a === $b);',
        ];

        yield [
            '<?php get_class($foo->bar);',
        ];

        yield [
            '<?php get_class($$foo);',
        ];

        yield [
            '<?php get_class($arr[$bar]);',
        ];

        yield [
            '<?php \a\get_class($foo);',
        ];

        yield [
            '<?php
class A
{
    public function get_class($foo) {}
}
',
        ];

        yield [
            '<?php get_class($a, $b);',
        ];
    }
}
