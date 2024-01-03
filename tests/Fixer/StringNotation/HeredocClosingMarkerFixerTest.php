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
            <<<'EOF'
                <?php $a = <<<EOD
                xxx EOD xxx
                EOD;

                EOF,
            <<<'EOF'
                <?php $a = <<<TEST
                xxx EOD xxx
                TEST;

                EOF,
        ];

        yield 'nowdoc' => [
            <<<'EOF'
                <?php $a = <<<'EOD'
                xxx EOD xxx
                EOD;

                EOF,
            <<<'EOF'
                <?php $a = <<<'TEST'
                xxx EOD xxx
                TEST;

                EOF,
        ];

        yield 'heredoc /w custom preferred closing marker' => [
            <<<'EOD'
                <?php $a = <<<EOF
                xxx
                EOF;

                EOD,
            <<<'EOD'
                <?php $a = <<<TEST
                xxx
                TEST;

                EOD,
            ['closing_marker' => 'EOF'],
        ];

        yield 'heredoc /w b' => [
            <<<'EOF'
                <?php $a = b<<<EOD
                xxx EOD xxx
                EOD;

                EOF,
            <<<'EOF'
                <?php $a = b<<<TEST
                xxx EOD xxx
                TEST;

                EOF,
        ];

        yield 'heredoc /w B' => [
            <<<'EOF'
                <?php $a = B<<<EOD
                xxx EOD xxx
                EOD;

                EOF,
            <<<'EOF'
                <?php $a = B<<<TEST
                xxx EOD xxx
                TEST;

                EOF,
        ];

        yield 'heredoc and reserved closing marker' => [
            <<<'EOF'
                <?php $a = <<<PHP
                xxx
                PHP;

                EOF,
        ];

        yield 'heredoc and reserved custom closing marker' => [
            <<<'EOF'
                <?php $a = <<<Žlutý
                xxx
                Žlutý;
                $b = <<<EOD
                xxx2
                EOD;
                $c = <<<EOD
                xxx3
                EOD;

                EOF,
            <<<'EOF'
                <?php $a = <<<Žlutý
                xxx
                Žlutý;
                $b = <<<Žlutý2
                xxx2
                Žlutý2;
                $c = <<<PHP
                xxx3
                PHP;

                EOF,
            ['reserved_closing_markers' => ['Žlutý']],
        ];

        yield 'heredoc /w content starting with preferred closing marker' => [
            <<<'EOF'
                <?php $a = <<<EOD_
                EOD xxx
                EOD_;

                EOF,
            <<<'EOF'
                <?php $a = <<<TEST
                EOD xxx
                TEST;

                EOF,
        ];

        yield 'heredoc /w content starting with whitespace and preferred closing marker' => [
            <<<'EOF'
                <?php $a = <<<EOD_
                 EOD xxx
                EOD_;

                EOF,
            <<<'EOF'
                <?php $a = <<<TEST
                 EOD xxx
                TEST;

                EOF,
        ];

        yield 'heredoc /w content starting with preferred closing marker and single quote' => [
            <<<'EOF'
                <?php $a = <<<EOD_
                EOD'
                EOD_;

                EOF,
            <<<'EOF'
                <?php $a = <<<TEST
                EOD'
                TEST;

                EOF,
        ];

        yield 'heredoc /w content starting with preferred closing marker and semicolon' => [
            <<<'EOF'
                <?php $a = <<<EOD_
                EOD;
                EOD_;

                EOF,
            <<<'EOF'
                <?php $a = <<<TEST
                EOD;
                TEST;

                EOF,
        ];

        yield 'heredoc /w content ending with preferred closing marker' => [
            <<<'EOF'
                <?php $a = <<<EOD
                xxx EOD
                EOD;

                EOF,
            <<<'EOF'
                <?php $a = <<<TEST
                xxx EOD
                TEST;

                EOF,
        ];
    }
}
