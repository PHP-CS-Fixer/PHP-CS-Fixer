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

namespace PhpCsFixer\Tests\Fixer\Whitespace;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;
use PhpCsFixer\WhitespacesFixerConfig;

/**
 * @author Gregor Harlan
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Whitespace\HeredocIndentationFixer
 */
final class HeredocIndentationFixerTest extends AbstractFixerTestCase
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

    public static function provideFixCases(): iterable
    {
        yield [
            <<<'EOD_'
                <?php
                    foo(<<<EOD
                        EOD
                    );
                EOD_,
            <<<'EOD_'
                <?php
                    foo(<<<EOD
                EOD
                    );
                EOD_,
        ];

        yield [
            <<<'EOD_'
                <?php
                    foo(<<<EOD

                        EOD
                    );
                EOD_,
            <<<'EOD_'
                <?php
                    foo(<<<EOD

                EOD
                    );
                EOD_,
        ];

        yield [
            <<<'EOD_'
                <?php
                    foo(<<<EOD
                        abc

                            def
                        EOD
                    );
                EOD_,
            <<<'EOD_'
                <?php
                    foo(<<<EOD
                abc

                    def
                EOD
                    );
                EOD_,
        ];

        yield [
            <<<'EOD_'
                <?php
                    foo(<<<'EOD'

                        abc
                            def

                        EOD
                    );
                EOD_,
            <<<'EOD_'
                <?php
                    foo(<<<'EOD'

                abc
                    def

                EOD
                    );
                EOD_,
        ];

        yield [
            <<<'EOD_'
                <?php
                    foo(<<<'EOD'
                        abc
                            def
                        EOD
                    );
                EOD_,
            <<<'EOD_'
                <?php
                    foo(<<<'EOD'
                            abc
                                def
                            EOD
                    );
                EOD_,
        ];

        yield [
            <<<'EOD_'
                <?php
                    foo(<<<EOD
                        $abc
                            $def
                        {$ghi}
                        EOD
                    );
                EOD_,
            <<<'EOD_'
                <?php
                    foo(<<<EOD
                $abc
                    $def
                {$ghi}
                EOD
                    );
                EOD_,
        ];

        yield [
            <<<'EOD_'
                <?php
                    $a = <<<'EOD'
                        <?php
                            $b = <<<FOO
                        abc
                        FOO;
                        EOD;
                EOD_,
            <<<'EOD_'
                <?php
                    $a = <<<'EOD'
                <?php
                    $b = <<<FOO
                abc
                FOO;
                EOD;
                EOD_,
        ];

        yield [
            /* EXPECTED */ '
<?php
    foo(<<<EOD
          '.'
        abc
          '.'
        def
          '.'
        EOD
    );',
            /* INPUT */ '
<?php
    foo(<<<EOD
        '.'
      abc
        '.'
      def
        '.'
      EOD
    );',
        ];

        yield [
            /* EXPECTED */ '
<?php
    foo(<<<EOD

        abc

        def

        EOD
    );',
            /* INPUT */ '
<?php
    foo(<<<EOD
  '.'
      abc
  '.'
      def
  '.'
      EOD
    );',
        ];

        yield [
            <<<'EOD_'
                <?php foo(<<<EOD
                    EOD
                );
                EOD_,
            <<<'EOD_'
                <?php foo(<<<EOD
                EOD
                );
                EOD_,
        ];

        yield [
            <<<'EOD_'
                <?php
                    foo(<<<EOD
                    abc

                        def
                    EOD
                    );
                EOD_,
            <<<'EOD_'
                <?php
                    foo(<<<EOD
                abc

                    def
                EOD
                    );
                EOD_,
            ['indentation' => 'same_as_start'],
        ];

        yield [
            <<<'EOD_'
                <?php
                    foo(<<<EOD
                    abc

                        def
                    EOD
                    );
                EOD_,
            <<<'EOD_'
                <?php
                    foo(<<<EOD
                        abc

                            def
                        EOD
                    );
                EOD_,
            ['indentation' => 'same_as_start'],
        ];
    }

    public function testWithWhitespacesConfig(): void
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t"));

        $expected = <<<EOD
            <?php
            \t\$a = <<<'EOD'
            \t\tabc
            \t\t    def
            \t\t\tghi
            \t\tEOD;
            EOD;

        $input = <<<EOD_
            <?php
            \t\$a = <<<'EOD'
            abc
                def
            \tghi
            EOD;
            EOD_;

        $this->doTest($expected, $input);
    }
}
