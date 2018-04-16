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

namespace PhpCsFixer\Tests\Fixer\Phpdoc;

use PhpCsFixer\Tests\Test\AbstractFixerTestCase;

/**
 * @author Graham Campbell <graham@alt-three.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocVarWithoutNameFixer
 */
final class PhpdocVarWithoutNameFixerTest extends AbstractFixerTestCase
{
    /**
     * @dataProvider provideFixVarCases
     *
     * @param string      $expected
     * @param string|null $input
     */
    public function testFixVar($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    /**
     * @dataProvider provideFixVarCases
     *
     * @param string      $expected
     * @param string|null $input
     */
    public function testFixType($expected, $input = null)
    {
        $expected = str_replace('@var', '@type', $expected);
        if (null !== $input) {
            $input = str_replace('@var', '@type', $input);
        }

        $this->doTest($expected, $input);
    }

    public function provideFixVarCases()
    {
        return array(
            'testFixVar' => array(
<<<'EOF'
<?php
    /**
     * @var string Hello!
     */

EOF
,
<<<'EOF'
<?php
    /**
     * @var string $foo Hello!
     */

EOF
,
            ),
            'testFixType' => array(
<<<'EOF'
<?php
    /**
     * @var int|null
     */

EOF
,
<<<'EOF'
<?php
    /**
     * @var int|null $bar
     */

EOF
,
            ),
            'testDoNothing' => array(
                <<<'EOF'
<?php
    /**
     * @var Foo\Bar This is a variable.
     */

EOF
            ),
            'testFixVarWithOtherAnnotation' => array(
                <<<'EOF'
<?php
    /**
     * @var string Hello!
     *
     * @deprecated
     */

EOF
                ,
                <<<'EOF'
<?php
    /**
     * @var string $foo Hello!
     *
     * @deprecated
     */

EOF
                ,
            ),
            'testFixVarWithNestedKeys' => array(
<<<'EOF'
<?php
    /**
     * @var array {
     *     @var bool   $required Whether this element is required
     *     @var string $label    The display name for this element
     * }
     */

EOF
                ,
<<<'EOF'
<?php
    /**
     * @var array $options {
     *     @var bool   $required Whether this element is required
     *     @var string $label    The display name for this element
     * }
     */

EOF
            ),
            'testSingleLine' => array(
<<<'EOF'
                <?php
    /** @var Foo\Bar $bar */
    $bar;
EOF
                ,
            ),
            'testEmpty' => array(
<<<'EOF'
                <?php
    /**
     *
     */

EOF
                ,
            ),
            'testInlineDoc' => array(
<<<'EOF'
                <?php
    /**
     * Initializes this class with the given options.
     *
     * @param array $options {
     *     @var bool   $required Whether this element is required
     *     @var string $label    The display name for this element
     * }
     */

EOF
                ,
            ),
            'testInlineDocAgain' => array(
<<<'EOF'
<?php
    /**
     * @param int[] $stuff {
     *     @var int $foo
     * }
     *
     * @return void
     */

EOF
                ,
            ),
        );
    }
}
