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
        return array(
            array(<<<'EOF'
<?php $a = <<<'TEST'
Foo $bar \n
TEST;

EOF
            ),
            array(<<<'EOF'
<?php $a = <<<'TEST'
TEST;

EOF
                , <<<'EOF'
<?php $a = <<<TEST
TEST;

EOF
            ),
            array(<<<'EOF'
<?php $a = <<<'TEST'
Foo \\ $bar \n
TEST;

EOF
                , <<<'EOF'
<?php $a = <<<TEST
Foo \\\\ \$bar \\n
TEST;

EOF
            ),
            array(<<<'EOF'
<?php $a = <<<'TEST'
Foo
TEST;

EOF
                , <<<'EOF'
<?php $a = <<<"TEST"
Foo
TEST;

EOF
            ),
            array(<<<'EOF'
<?php $a = <<<TEST
Foo $bar
TEST;

EOF
            ),
            array(<<<'EOF'
<?php $a = <<<TEST
Foo \\$bar
TEST;

EOF
            ),
            array(<<<'EOF'
<?php $a = <<<TEST
Foo \n $bar
TEST;

EOF
            ),
            array(<<<'EOF'
<?php $a = <<<TEST
Foo \x00 $bar
TEST;

EOF
            ),
            array(<<<'EOF'
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
            ),
            array(<<<'EOF'
<?php $a = <<<           'TEST'
Foo
TEST;

EOF
                , <<<'EOF'
<?php $a = <<<           "TEST"
Foo
TEST;

EOF
            ),
            array(<<<EOF
<?php echo <<<'TEST'\r\nFoo\r\nTEST;

EOF
                , <<<EOF
<?php echo <<<TEST\r\nFoo\r\nTEST;

EOF
            ),
            array(<<<'EOF'
<?php $a = b<<<'TEST'
Foo $bar \n
TEST;

EOF
            ),
            array(<<<'EOF'
<?php $a = b<<<'TEST'
TEST;

EOF
            , <<<'EOF'
<?php $a = b<<<TEST
TEST;

EOF
            ),
            array(<<<'EOF'
<?php $a = b<<<'TEST'
Foo \\ $bar \n
TEST;

EOF
            , <<<'EOF'
<?php $a = b<<<TEST
Foo \\\\ \$bar \\n
TEST;

EOF
            ),
            array(<<<'EOF'
<?php $a = b<<<'TEST'
Foo
TEST;

EOF
            , <<<'EOF'
<?php $a = b<<<"TEST"
Foo
TEST;

EOF
            ),
            array(<<<'EOF'
<?php $a = b<<<TEST
Foo $bar
TEST;

EOF
            ),
            array(<<<'EOF'
<?php $a = b<<<TEST
Foo \\$bar
TEST;

EOF
            ),
            array(<<<'EOF'
<?php $a = b<<<TEST
Foo \n $bar
TEST;

EOF
            ),
            array(<<<'EOF'
<?php $a = b<<<TEST
Foo \x00 $bar
TEST;

EOF
            ),
            array(<<<'EOF'
<?php
$html = b<<<   'HTML'
a
HTML;

EOF
            , <<<'EOF'
<?php
$html = b<<<   HTML
a
HTML;

EOF
            ),
            array(<<<'EOF'
<?php $a = b<<<           'TEST'
Foo
TEST;

EOF
            , <<<'EOF'
<?php $a = b<<<           "TEST"
Foo
TEST;

EOF
            ),
            array(<<<EOF
<?php echo b<<<'TEST'\r\nFoo\r\nTEST;

EOF
            , <<<EOF
<?php echo b<<<TEST\r\nFoo\r\nTEST;

EOF
            ),
            array(<<<'EOF'
<?php $a = B<<<'TEST'
Foo $bar \n
TEST;

EOF
            ),
            array(<<<'EOF'
<?php $a = B<<<'TEST'
TEST;

EOF
            , <<<'EOF'
<?php $a = B<<<TEST
TEST;

EOF
            ),
            array(<<<'EOF'
<?php $a = B<<<'TEST'
Foo \\ $bar \n
TEST;

EOF
            , <<<'EOF'
<?php $a = B<<<TEST
Foo \\\\ \$bar \\n
TEST;

EOF
            ),
            array(<<<'EOF'
<?php $a = B<<<'TEST'
Foo
TEST;

EOF
            , <<<'EOF'
<?php $a = B<<<"TEST"
Foo
TEST;

EOF
            ),
            array(<<<'EOF'
<?php $a = B<<<TEST
Foo $bar
TEST;

EOF
            ),
            array(<<<'EOF'
<?php $a = B<<<TEST
Foo \\$bar
TEST;

EOF
            ),
            array(<<<'EOF'
<?php $a = B<<<TEST
Foo \n $bar
TEST;

EOF
            ),
            array(<<<'EOF'
<?php $a = B<<<TEST
Foo \x00 $bar
TEST;

EOF
            ),
            array(<<<'EOF'
<?php
$html = B<<<   'HTML'
a
HTML;

EOF
            , <<<'EOF'
<?php
$html = B<<<   HTML
a
HTML;

EOF
            ),
            array(<<<'EOF'
<?php $a = B<<<           'TEST'
Foo
TEST;

EOF
            , <<<'EOF'
<?php $a = B<<<           "TEST"
Foo
TEST;

EOF
            ),
            array(<<<EOF
<?php echo B<<<'TEST'\r\nFoo\r\nTEST;

EOF
            , <<<EOF
<?php echo B<<<TEST\r\nFoo\r\nTEST;

EOF
            ),
        );
    }
}
