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

namespace PhpCsFixer\Tests\Fixer\StringNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\StringNotation\StringLineEndingFixer
 *
 * @extends AbstractFixerTestCase<\PhpCsFixer\Fixer\StringNotation\StringLineEndingFixer>
 *
 * @author Ilija Tovilo <ilija.tovilo@me.com>
 *
 * @no-named-arguments Parameter names are not covered by the backward compatibility promise.
 */
final class StringLineEndingFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null): void
    {
        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{string, string}>
     */
    public static function provideFixCases(): iterable
    {
        $heredocTemplate = "<?php\n\$a=\n<<<EOT\n%s\n\nEOT;\n";
        $nowdocTemplate = "<?php\n\$a=\n<<<'EOT'\n%s\n\nEOT;\n";
        $input = '/**
* @SWG\Get(
*     path="/api/v0/cards",
*     operationId="listCards",
*     tags={"Банковские карты"},
*     summary="Возвращает список банковских карт."
*  )
*/';

        yield [
            "<?php \$a = 'my\nmulti\nline\nstring';\r\n",
            "<?php \$a = 'my\r\nmulti\nline\r\nstring';\r\n",
        ];

        yield [
            "<?php \$a = \"my\nmulti\nline\nstring\";\r\n",
            "<?php \$a = \"my\r\nmulti\nline\r\nstring\";\r\n",
        ];

        yield [
            "<?php \$a = \"my\nmulti\nline\nstring\nwith\n\$b\ninterpolation\";\r\n",
            "<?php \$a = \"my\r\nmulti\nline\r\nstring\nwith\r\n\$b\ninterpolation\";\r\n",
        ];

        yield [
            \sprintf($heredocTemplate, $input),
            \sprintf($heredocTemplate, str_replace("\n", "\r", $input)),
        ];

        yield [
            \sprintf($heredocTemplate, $input),
            \sprintf($heredocTemplate, str_replace("\n", "\r\n", $input)),
        ];

        yield [
            \sprintf($nowdocTemplate, $input),
            \sprintf($nowdocTemplate, str_replace("\n", "\r", $input)),
        ];

        yield [
            \sprintf($nowdocTemplate, $input),
            \sprintf($nowdocTemplate, str_replace("\n", "\r\n", $input)),
        ];

        yield [
            \sprintf(str_replace('<<<', 'b<<<', $nowdocTemplate), $input),
            \sprintf(str_replace('<<<', 'b<<<', $nowdocTemplate), str_replace("\n", "\r\n", $input)),
        ];

        yield [
            \sprintf(str_replace('<<<', 'B<<<', $nowdocTemplate), $input),
            \sprintf(str_replace('<<<', 'B<<<', $nowdocTemplate), str_replace("\n", "\r\n", $input)),
        ];

        yield [
            \sprintf(str_replace('<<<', 'b<<<', $heredocTemplate), $input),
            \sprintf(str_replace('<<<', 'b<<<', $heredocTemplate), str_replace("\n", "\r\n", $input)),
        ];

        yield [
            \sprintf(str_replace('<<<', 'B<<<', $heredocTemplate), $input),
            \sprintf(str_replace('<<<', 'B<<<', $heredocTemplate), str_replace("\n", "\r\n", $input)),
        ];

        yield 'not T_CLOSE_TAG, do T_INLINE_HTML' => [
            "<?php foo(); ?>\r\nA\n\n",
            "<?php foo(); ?>\r\nA\r\n\r\n",
        ];

        yield [
            "<?php \$a = b'my\nmulti\nline\nstring';\r\n",
            "<?php \$a = b'my\r\nmulti\nline\r\nstring';\r\n",
        ];
    }

    public function testWithWhitespacesConfig(): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t", "\r\n"));

        $this->doTest(
            "<?php \$a = 'my\r\nmulti\r\nline\r\nstring';",
            "<?php \$a = 'my\nmulti\nline\nstring';"
        );
    }
}
