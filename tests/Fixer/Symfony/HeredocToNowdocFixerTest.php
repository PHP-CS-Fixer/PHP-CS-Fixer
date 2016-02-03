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

namespace PhpCsFixer\Tests\Fixer\Symfony;

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Gregor Harlan <gharlan@web.de>
 *
 * @internal
 */
final class HeredocToNowdocFixerTest extends AbstractFixerTestCase
{
    /**
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
        );
    }
}
