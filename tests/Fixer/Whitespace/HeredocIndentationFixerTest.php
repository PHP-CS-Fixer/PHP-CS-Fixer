<?php

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
     * @requires PHP <7.3
     */
    public function testDoNotFix()
    {
        $this->doTest(
            <<<'TEST'
<?php
    foo(<<<EOD
abc
    def
EOD
    );
TEST
        );
    }

    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     * @requires PHP 7.3
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            [
                <<<'EXPECTED'
<?php
    foo(<<<EOD
        EOD
    );
EXPECTED
                ,
                <<<'INPUT'
<?php
    foo(<<<EOD
EOD
    );
INPUT
                ,
            ],
            [
                <<<'EXPECTED'
<?php
    foo(<<<EOD

        EOD
    );
EXPECTED
                ,
                <<<'INPUT'
<?php
    foo(<<<EOD

EOD
    );
INPUT
                ,
            ],
            [
                <<<'EXPECTED'
<?php
    foo(<<<EOD
        abc

            def
        EOD
    );
EXPECTED
                ,
                <<<'INPUT'
<?php
    foo(<<<EOD
abc

    def
EOD
    );
INPUT
                ,
            ],
            [
                <<<'EXPECTED'
<?php
    foo(<<<'EOD'

        abc
            def

        EOD
    );
EXPECTED
                ,
                <<<'INPUT'
<?php
    foo(<<<'EOD'

abc
    def

EOD
    );
INPUT
                ,
            ],
            [
                <<<'EXPECTED'
<?php
    foo(<<<'EOD'
        abc
            def
        EOD
    );
EXPECTED
                ,
                <<<'INPUT'
<?php
    foo(<<<'EOD'
            abc
                def
            EOD
    );
INPUT
                ,
            ],
            [
                <<<'EXPECTED'
<?php
    foo(<<<EOD
        $abc
            $def
        {$ghi}
        EOD
    );
EXPECTED
                ,
                <<<'INPUT'
<?php
    foo(<<<EOD
$abc
    $def
{$ghi}
EOD
    );
INPUT
                ,
            ],
            [
                <<<'EXPECTED'
<?php
    $a = <<<'EOD'
        <?php
            $b = <<<FOO
        abc
        FOO;
        EOD;
EXPECTED
                ,
                <<<'INPUT'
<?php
    $a = <<<'EOD'
<?php
    $b = <<<FOO
abc
FOO;
EOD;
INPUT
                ,
            ],
            [
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
            ],
            [
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
            ],
        ];
    }

    /**
     * @requires PHP 7.3
     */
    public function testFixWithTabIndentation()
    {
        $this->fixer->setWhitespacesConfig(new WhitespacesFixerConfig("\t"));

        $expected = <<<EXPECTED
<?php
\t\$a = <<<'EOD'
\t\tabc
\t\t    def
\t\t\tghi
\t\tEOD;
EXPECTED;

        $input = <<<INPUT
<?php
\t\$a = <<<'EOD'
abc
    def
\tghi
EOD;
INPUT;

        $this->doTest($expected, $input);
    }
}
