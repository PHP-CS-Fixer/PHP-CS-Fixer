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

namespace PhpCsFixer\Tests\Fixer\Strict;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Strict\DeclareStrictTypesFixer
 */
final class DeclareStrictTypesFixerTest extends AbstractFixerTestCase
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
        yield [
            '<?php
declare(ticks=1);
//
declare(strict_types=1);

namespace A\B\C;
class A {
}',
        ];

        yield [
            '<?php declare/* A b C*/(strict_types=1);',
        ];

        yield 'monolithic file with closing tag' => [
            '<?php /**/ /**/ deClarE  (strict_types=1)    ?>',
            '<?php /**/ /**/ deClarE  (STRICT_TYPES=1)    ?>',
        ];

        yield 'monolithic file with closing tag and extra new line' => [
            '<?php /**/ /**/ deClarE  (strict_types=1)    ?>'."\n",
            '<?php /**/ /**/ deClarE  (STRICT_TYPES=1)    ?>'."\n",
        ];

        yield 'monolithic file with closing tag and extra content' => [
            '<?php /**/ /**/ deClarE  (STRICT_TYPES=1)    ?>Test',
        ];

        yield [
            '<?php            DECLARE  (    strict_types=1   )   ;',
        ];

        yield [
            '<?php
                /**/
                declare(strict_types=1);',
        ];

        yield [
            '<?php declare(strict_types=1);

                phpinfo();',
            '<?php

                phpinfo();',
        ];

        yield [
            '<?php declare(strict_types=1);

/**
 * Foo
 */
phpinfo();',
            '<?php

/**
 * Foo
 */
phpinfo();',
        ];

        yield [
            '<?php declare(strict_types=1);

// comment after empty line',
            '<?php

// comment after empty line',
        ];

        yield [
            '<?php declare(strict_types=1);
// comment without empty line before',
            '<?php
// comment without empty line before',
        ];

        yield [
            '<?php declare(strict_types=1);
phpinfo();',
            '<?php phpinfo();',
        ];

        yield [
            '<?php declare(strict_types=1);
$a = 456;
',
            '<?php
$a = 456;
',
        ];

        yield [
            '<?php declare(strict_types=1);
/**/',
            '<?php /**/',
        ];

        yield [
            '<?php declare(strict_types=1);',
            '<?php declare(strict_types=0);',
        ];

        yield ['  <?php echo 123;']; // first statement must be an open tag

        yield ['<?= 123;']; // first token open with echo is not fixed

        yield 'empty file /wo open tag' => [
            '',
        ];

        yield 'empty file /w open tag' => [
            '<?php declare(strict_types=1);',
            '<?php',
        ];

        yield 'non-empty file /wo open tag' => [
            'x',
        ];

        yield 'non-empty file /w open tag' => [
            'x<?php',
        ];

        yield 'file with shebang /w open tag' => [
            <<<'EOD'
                #!x
                <?php declare(strict_types=1);
                EOD,
            <<<'EOD'
                #!x
                <?php
                EOD,
        ];

        yield 'file with shebang /wo open tag' => [
            <<<'EOD'
                #!x
                y
                EOD,
        ];

        yield 'file with shebang not followed by open tag' => [
            <<<'EOD'
                #!x
                #!not_a_shebang
                <?php
                EOD,
        ];
    }

    /**
     * @dataProvider provideWithWhitespacesConfigCases
     */
    public function testWithWhitespacesConfig(string $expected, ?string $input = null): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest($expected, $input);
    }

    public static function provideWithWhitespacesConfigCases(): iterable
    {
        yield [
            "<?php declare(strict_types=1);\r\nphpinfo();",
            "<?php\r\n\tphpinfo();",
        ];

        yield [
            "<?php declare(strict_types=1);\r\nphpinfo();",
            "<?php\nphpinfo();",
        ];
    }
}
