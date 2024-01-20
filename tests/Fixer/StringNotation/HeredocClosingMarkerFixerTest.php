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

/**
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\StringNotation\HeredocClosingMarkerFixer
 */
final class HeredocClosingMarkerFixerTest extends AbstractFixerTestCase
{
    /**
     * @param array<string, mixed> $config
     *
     * @dataProvider provideFixCases
     */
    public function testFix(string $expected, ?string $input = null, array $config = []): void
    {
        $this->fixer->configure($config);

        $this->doTest($expected, $input);
    }

    /**
     * @return iterable<array{0: string, 1?: null|string, 2?: array<string, mixed>}>
     */
    public static function provideFixCases(): iterable
    {
        yield 'heredoc' => [
            <<<'PHP'
                <?php $a = <<<EOD
                xxx EOD xxx
                EOD;
                PHP,
            <<<'PHP'
                <?php $a = <<<TEST
                xxx EOD xxx
                TEST;
                PHP,
        ];

        yield 'nowdoc' => [
            <<<'PHP'
                <?php $a = <<<'EOD'
                xxx EOD xxx
                EOD;
                PHP,
            <<<'PHP'
                <?php $a = <<<'TEST'
                xxx EOD xxx
                TEST;
                PHP,
        ];

        yield 'heredoc /w custom preferred closing marker' => [
            <<<'PHP'
                <?php $a = <<<EOF
                xxx
                EOF;
                PHP,
            <<<'PHP'
                <?php $a = <<<TEST
                xxx
                TEST;
                PHP,
            ['closing_marker' => 'EOF'],
        ];

        yield 'heredoc /w custom explicit style' => [
            <<<'PHP'
                <?php $a = <<<"EOD"
                xxx
                EOD;
                $b = <<<"EOD"
                xxx2
                EOD;
                $b = <<<'EOD'
                xxx3
                EOD;
                PHP,
            <<<'PHP'
                <?php $a = <<<TEST
                xxx
                TEST;
                $b = <<<"TEST"
                xxx2
                TEST;
                $b = <<<'TEST'
                xxx3
                TEST;
                PHP,
            ['explicit_heredoc_style' => true],
        ];

        yield 'heredoc /w b' => [
            <<<'PHP'
                <?php $a = b<<<EOD
                xxx EOD xxx
                EOD;
                PHP,
            <<<'PHP'
                <?php $a = b<<<TEST
                xxx EOD xxx
                TEST;
                PHP,
        ];

        yield 'heredoc /w B' => [
            <<<'PHP'
                <?php $a = B<<<EOD
                xxx EOD xxx
                EOD;
                PHP,
            <<<'PHP'
                <?php $a = B<<<TEST
                xxx EOD xxx
                TEST;
                PHP,
        ];

        yield 'heredoc and reserved closing marker' => [
            <<<'PHP_'
                <?php $a = <<<PHP
                xxx
                PHP;
                PHP_,
        ];

        yield 'heredoc and reserved closing marker - different case' => [
            <<<'PHP_'
                <?php $a = <<<PHP
                xxx
                PHP;
                $a = <<<PHP
                PHP;
                PHP_,
            <<<'PHP'
                <?php $a = <<<php
                xxx
                php;
                $a = <<<Php
                Php;
                PHP,
        ];

        yield 'heredoc and reserved custom closing marker' => [
            <<<'PHP'
                <?php $a = <<<Žlutý
                xxx
                Žlutý;
                $aNormCase = <<<Žlutý
                xxx
                Žlutý;
                $aNormCase = <<<Žlutý
                xxx
                Žlutý;
                $b = <<<EOD
                xxx2
                EOD;
                $c = <<<EOD
                xxx3
                EOD;
                PHP,
            <<<'PHP_'
                <?php $a = <<<Žlutý
                xxx
                Žlutý;
                $aNormCase = <<<ŽluTý
                xxx
                ŽluTý;
                $aNormCase = <<<ŽLUTÝ
                xxx
                ŽLUTÝ;
                $b = <<<Žlutý2
                xxx2
                Žlutý2;
                $c = <<<PHP
                xxx3
                PHP;
                PHP_,
            ['reserved_closing_markers' => ['Žlutý']],
        ];

        yield 'no longer colliding reserved marker recovery' => [
            <<<'PHP'
                <?php
                $a = <<<CSS
                    CSS;
                $a = <<<CSS
                    CSS;
                $a = <<<CSS_
                    CSS
                    CSS_;
                $a = <<<CSS
                    CSS_
                    CSS;
                PHP,
            <<<'PHP'
                <?php
                $a = <<<CSS_
                    CSS_;
                $a = <<<CSS__
                    CSS__;
                $a = <<<CSS__
                    CSS
                    CSS__;
                $a = <<<CSS__
                    CSS_
                    CSS__;
                PHP,
        ];

        yield 'heredoc /w content starting with preferred closing marker' => [
            <<<'PHP'
                <?php $a = <<<EOD_
                EOD xxx
                EOD_;
                PHP,
            <<<'PHP'
                <?php $a = <<<TEST
                EOD xxx
                TEST;
                PHP,
        ];

        yield 'heredoc /w content starting with whitespace and preferred closing marker' => [
            <<<'PHP'
                <?php $a = <<<EOD_
                 EOD xxx
                EOD_;
                PHP,
            <<<'PHP'
                <?php $a = <<<TEST
                 EOD xxx
                TEST;
                PHP,
        ];

        yield 'heredoc /w content starting with preferred closing marker and single quote' => [
            <<<'PHP'
                <?php $a = <<<EOD_
                EOD'
                EOD_;
                PHP,
            <<<'PHP'
                <?php $a = <<<TEST
                EOD'
                TEST;
                PHP,
        ];

        yield 'heredoc /w content starting with preferred closing marker and semicolon' => [
            <<<'PHP'
                <?php $a = <<<EOD_
                EOD;
                EOD_;
                PHP,
            <<<'PHP'
                <?php $a = <<<TEST
                EOD;
                TEST;
                PHP,
        ];

        yield 'heredoc /w content ending with preferred closing marker' => [
            <<<'PHP'
                <?php $a = <<<EOD
                xxx EOD
                EOD;
                PHP,
            <<<'PHP'
                <?php $a = <<<TEST
                xxx EOD
                TEST;
                PHP,
        ];
    }
}
