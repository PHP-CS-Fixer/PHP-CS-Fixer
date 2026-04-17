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
 * @covers \PhpCsFixer\Fixer\LanguageConstruct\ExitParenthesesFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\LanguageConstruct\ExitParenthesesFixer>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class ExitParenthesesFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: null|string}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'bare exit statement' => [
            '<?php exit();',
            '<?php exit;',
        ];

        yield 'bare die statement' => [
            '<?php die();',
            '<?php die;',
        ];

        yield 'uppercase exit preserves casing' => [
            '<?php EXIT();',
            '<?php EXIT;',
        ];

        yield 'mixed case die preserves casing' => [
            '<?php Die();',
            '<?php Die;',
        ];

        yield 'exit already with empty parentheses' => [
            '<?php exit();',
        ];

        yield 'exit with argument' => [
            '<?php exit(0);',
        ];

        yield 'die with string argument' => [
            '<?php die("bye");',
        ];

        yield 'exit inside if with no braces' => [
            '<?php if ($x) exit();',
            '<?php if ($x) exit;',
        ];

        yield 'die inside if with no braces' => [
            '<?php if ($x) die();',
            '<?php if ($x) die;',
        ];

        yield 'exit inside arrow function' => [
            '<?php $f = fn () => exit();',
            '<?php $f = fn () => exit;',
        ];

        yield 'exit followed by closing tag' => [
            "<?php exit()?>\n",
            "<?php exit?>\n",
        ];

        yield 'die followed by closing tag' => [
            "<?php die()?>\n",
            "<?php die?>\n",
        ];

        yield 'exit with space before semicolon' => [
            '<?php exit() ;',
            '<?php exit ;',
        ];

        yield 'exit with newline before semicolon' => [
            "<?php exit()\n;",
            "<?php exit\n;",
        ];

        yield 'string containing exit is not touched' => [
            '<?php $x = "exit";',
        ];

        yield 'comment containing exit is not touched' => [
            "<?php // exit;\n\$x = 1;",
        ];

        yield 'heredoc containing exit is not touched' => [
            "<?php \$x = <<<EOT\nexit\nEOT;\n",
        ];

        yield 'multiple exits on separate lines' => [
            "<?php\nif (\$a) exit();\nif (\$b) die();\n",
            "<?php\nif (\$a) exit;\nif (\$b) die;\n",
        ];

        yield 'nested ternary with exit' => [
            '<?php $x ? exit() : null;',
            '<?php $x ? exit : null;',
        ];

        yield 'exit with comment between keyword and semicolon' => [
            '<?php exit() /* done */ ;',
            '<?php exit /* done */ ;',
        ];

        yield 'exit with parenthesis on next line already' => [
            "<?php exit\n(0);",
        ];
    }

    /**
     * @dataProvider provideFix80Cases
     *
     * @requires PHP 8.0
     */
    public function testFix80(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<string, array{0: string, 1?: null|string}>
     */
    public static function provideFix80Cases(): iterable
    {
        yield 'exit inside match arm' => [
            "<?php match (\$x) {\n    1 => exit(),\n    default => null,\n};",
            "<?php match (\$x) {\n    1 => exit,\n    default => null,\n};",
        ];
    }
}
