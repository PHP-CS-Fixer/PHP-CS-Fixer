<?php

/*
 * This file is part of the PHP CS Fixer.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *     Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Symfony;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Graham Campbell <graham@mineuk.com>
 */
class PhpdocScalarFixerTest extends AbstractFixerTestBase
{
    public function testBasicFix()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @return int
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @return integer
     */

EOF;

        $this->makeTest($expected, $input);
    }

    public function testPropertyFix()
    {
        $expected = <<<'EOF'
<?php
/**
 * @method int foo()
 * @property int $foo
 * @property-read bool $bar
 * @property-write float $baz
 */

EOF;

        $input = <<<'EOF'
<?php
/**
 * @method integer foo()
 * @property integer $foo
 * @property-read boolean $bar
 * @property-write double $baz
 */

EOF;

        $this->makeTest($expected, $input);
    }

    public function testDoNotModifyVariables()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param int $integer
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param integer $integer
     */

EOF;

        $this->makeTest($expected, $input);
    }

    public function testFixWithTabsOnOneLine()
    {
        $expected = "<?php /**\t@return\tbool\t*/";

        $input = "<?php /**\t@return\tboolean\t*/";

        $this->makeTest($expected, $input);
    }

    public function testFixMoreThings()
    {
        $expected = <<<'EOF'
<?php
    /**
     * Hello there mr integer!
     *
     * @param int|float $integer
     * @param int|int[] $foo
     *
     * @return string|bool
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * Hello there mr integer!
     *
     * @param integer|real $integer
     * @param int|integer[] $foo
     *
     * @return string|boolean
     */

EOF;

        $this->makeTest($expected, $input);
    }

    public function testFixVar()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @var int Some integer value.
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @var integer Some integer value.
     */

EOF;

        $this->makeTest($expected, $input);
    }

    public function testFixVarWithMoreStuff()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @var bool|int|Double Booleans, integers and doubles.
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @var boolean|integer|Double Booleans, integers and doubles.
     */

EOF;

        $this->makeTest($expected, $input);
    }

    public function testFixType()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @type float
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @type real
     */

EOF;

        $this->makeTest($expected, $input);
    }

    public function testDoNotFix()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @var notaboolean
     */

EOF;

        $this->makeTest($expected);
    }

    public function testComplexMix()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @var notabooleanthistime|bool|integerr
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @var notabooleanthistime|boolean|integerr
     */

EOF;

        $this->makeTest($expected, $input);
    }

    public function testDoNotModifyComplexTag()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @Type("boolean")
     */
EOF;

        $this->makeTest($expected);
    }

    public function testDoNotModifyStrings()
    {
        $expected = <<<'EOF'
<?php

$string = '
    /**
     * @var boolean
     */
';

EOF;

        $this->makeTest($expected);
    }

    public function testEmptyDocBlock()
    {
        $expected = <<<'EOF'
<?php
    /**
     *
     */

EOF;

        $this->makeTest($expected);
    }

    public function testWrongCasedPhpdocTagIsNotAltered()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @Param boolean
     *
     * @Return int
     */

EOF;
        $this->makeTest($expected);
    }

    public function testInlineDoc()
    {
        $expected = <<<'EOF'
<?php
    /**
     * Does stuffs with stuffs.
     *
     * @param array $stuffs {
     *     @type bool $foo
     *     @type int $bar
     * }
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * Does stuffs with stuffs.
     *
     * @param array $stuffs {
     *     @type boolean $foo
     *     @type integer $bar
     * }
     */

EOF;

        $this->makeTest($expected, $input);
    }
}
