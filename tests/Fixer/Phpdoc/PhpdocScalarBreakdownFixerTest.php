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
 * @author Andreas Frömer <blubb0r05+github@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\AbstractPhpdocTypesFixer
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocScalarBreakdownFixer
 */
final class PhpdocScalarBreakdownFixerTest extends AbstractFixerTestCase
{
    public function testBasicFix()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @return bool|float|int|string
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @return scalar
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testPropertyFix()
    {
        $expected = <<<'EOF'
<?php
/**
 * @method bool|float|int|string foo()
 * @property bool|float|int|string $foo
 * @property-read bool|float|int|string $bar
 * @property-write bool|float|int|string $baz
 */

EOF;

        $input = <<<'EOF'
<?php
/**
 * @method scalar foo()
 * @property scalar $foo
 * @property-read scalar $bar
 * @property-write scalar $baz
 */

EOF;

        $this->doTest($expected, $input);
    }

    public function testDoNotModifyVariables()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param bool|float|int|string $scalar
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param scalar $scalar
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testFixWithTabsOnOneLine()
    {
        $expected = "<?php /**\t@return\tbool|float|int|string\t*/";

        $input = "<?php /**\t@return\tscalar\t*/";

        $this->doTest($expected, $input);
    }

    public function testFixMoreThings()
    {
        $expected = <<<'EOF'
<?php
    /**
     * Hello there mr scalar!
     *
     * @param bool|float|int|string $scalar
     *
     * @return bool|float|int|string
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * Hello there mr scalar!
     *
     * @param scalar $scalar
     *
     * @return scalar
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testFixVar()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @var bool|float|int|string Some integer value.
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @var scalar Some integer value.
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testFixType()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @type bool|float|int|string
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @type scalar
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testDoNotFix()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @var scalara
     */

EOF;

        $this->doTest($expected);
    }

    public function testComplexMix()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @var notabooleanthistime|bool|float|int|string|integerr
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @var notabooleanthistime|scalar|integerr
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testDoNotModifyComplexTag()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @Type("scalar")
     */
EOF;

        $this->doTest($expected);
    }

    public function testDoNotModifyStrings()
    {
        $expected = <<<'EOF'
<?php

$string = '
    /**
     * @var scalar
     */
';

EOF;

        $this->doTest($expected);
    }

    public function testEmptyDocBlock()
    {
        $expected = <<<'EOF'
<?php
    /**
     *
     */

EOF;

        $this->doTest($expected);
    }

    public function testWrongCasedPhpdocTagIsNotAltered()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @Param scalar
     *
     * @Return scalar
     */

EOF;
        $this->doTest($expected);
    }

    public function testInlineDoc()
    {
        $expected = <<<'EOF'
<?php
    /**
     * Does stuffs with stuffs.
     *
     * @param array $stuffs {
     *     @type bool|float|int|string $foo
     * }
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * Does stuffs with stuffs.
     *
     * @param array $stuffs {
     *     @type scalar $foo
     * }
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testScalarNullable()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @var bool|float|int|string|null
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @var ?scalar
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testScalarCollection()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param Collection<bool|float|int|string>
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param Collection<scalar>
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testScalarArrayConversion()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param (bool|float|int|string)[]
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param scalar[]
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testNullableScalarArrayConversion()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param (bool|float|int|string|null)[]
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param ?scalar[]
     */

EOF;

        $this->doTest($expected, $input);
    }
}
