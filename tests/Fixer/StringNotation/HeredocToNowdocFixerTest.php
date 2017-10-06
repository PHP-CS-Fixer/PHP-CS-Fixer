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

namespace PhpCsFixer\Tests\Fixer\StringNotation;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Gregor Harlan <gharlan@web.de>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\StringNotation\HeredocToNowdocFixer
 */
final class HeredocToNowdocFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideTestFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideTestFixCases()
    {
        return [
            [<<<'EOF'
<?php $a = <<<'TEST'
Foo $bar \n
TEST;

EOF
            ],
            [<<<'EOF'
<?php $a = <<<'TEST'
TEST;

EOF
            , <<<'EOF'
<?php $a = <<<TEST
TEST;

EOF
            ],
            [<<<'EOF'
<?php $a = <<<'TEST'
Foo \\ $bar \n
TEST;

EOF
            , <<<'EOF'
<?php $a = <<<TEST
Foo \\\\ \$bar \\n
TEST;

EOF
            ],
            [<<<'EOF'
<?php $a = <<<'TEST'
Foo
TEST;

EOF
            , <<<'EOF'
<?php $a = <<<"TEST"
Foo
TEST;

EOF
            ],
            [<<<'EOF'
<?php $a = <<<TEST
Foo $bar
TEST;

EOF
            ],
            [<<<'EOF'
<?php $a = <<<TEST
Foo \\$bar
TEST;

EOF
            ],
            [<<<'EOF'
<?php $a = <<<TEST
Foo \n $bar
TEST;

EOF
            ],
            [<<<'EOF'
<?php $a = <<<TEST
Foo \x00 $bar
TEST;

EOF
            ],
            [<<<'EOF'
<?php
$html = <<<   'HTML'
a
HTML;

EOF
            , <<<'EOF'
<?php
$html = <<<   HTML
a
HTML;

EOF
            ],
            [<<<'EOF'
<?php $a = <<<           'TEST'
Foo
TEST;

EOF
            , <<<'EOF'
<?php $a = <<<           "TEST"
Foo
TEST;

EOF
            ],
            [<<<EOF
<?php echo <<<'TEST'\r\nFoo\r\nTEST;

EOF
            , <<<EOF
<?php echo <<<TEST\r\nFoo\r\nTEST;

EOF
            ],
        ];
    }
}
