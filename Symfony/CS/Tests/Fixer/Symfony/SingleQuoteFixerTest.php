<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Symfony;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Gregor Harlan <gharlan@web.de>
 */
class SingleQuoteFixerTest extends AbstractFixerTestBase
{
    /**
     * @dataProvider provideTestFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->makeTest($expected, $input);
    }

    public function provideTestFixCases()
    {
        return array(
            array(
                '<?php $a = \'\';',
                '<?php $a = "";',
            ),
            array(
                '<?php $a = \'foo bar0\';',
                '<?php $a = "foo bar0";',
            ),
            array(
                '<?php $a = \'foo
                    bar1\';',
                '<?php $a = "foo
                    bar1";',
            ),
            array(
                '<?php $a = \'foo\'.\'bar2\'."$baz";',
                '<?php $a = \'foo\'."bar2"."$baz";',
            ),
            array(
                '<?php $a = \'foo "bar3"\';',
                '<?php $a = "foo \"bar3\"";',
            ),
            array(<<<'EOF'
<?php $a = '\\foo\\bar4\\\\';
EOF
                , <<<'EOF'
<?php $a = "\\foo\\bar4\\\\";
EOF
            ),
            array(
                '<?php $a = \'foo "bar6\';',
                '<?php $a = "foo \"bar6";',
            ),
            array(
                '<?php $a = \'foo $bar7\';',
                '<?php $a = "foo \$bar7";',
            ),
            array('<?php $a = \'foo bar8\';'),
            array('<?php $a = \'foo "bar9"\';'),
            array('<?php $a = "foo $bar10";'),
            array('<?php $a = "foo ${bar11}";'),
            array('<?php $a = "foo\n bar12";'),
            array(<<<'EOF'
<?php $a = "\\\n";
EOF
            ),
            array('<?php $a = "foo \\" \\$$bar13";'),
        );
    }
}
