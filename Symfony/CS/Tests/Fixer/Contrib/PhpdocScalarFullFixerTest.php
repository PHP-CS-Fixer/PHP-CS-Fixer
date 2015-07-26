<?php

/*
 * This file is part of the PHP CS utility.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Symfony\CS\Tests\Fixer\Contrib;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Konrad Cerny <info@konradcerny.cz>
 */
class PhpdocScalarFullFixerTest extends AbstractFixerTestBase
{
    public function testBasicFix()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @return integer
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @return int
     */

EOF;

        $this->makeTest($expected, $input);
    }

    public function testAllPhpDocVariants()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @var integer|boolean|string|float
     * @type integer|boolean|string|float
     * @param integer|boolean|string|float
     * @param integer|boolean|float
     * @param integer|boolean|float
     * @return integer|boolean|string|float
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @var int|bool|string|float
     * @type int|bool|string|float
     * @param int|bool|string|float
     * @param int|bool|double
     * @param int|bool|real
     * @return int|bool|string|float
     */

EOF;

        $this->makeTest($expected, $input);
    }

    public function testDoNotModifyVariables()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param integer $integer
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param int $integer
     */

EOF;

        $this->makeTest($expected, $input);
    }

    public function testFixWithTabsOnOneLine()
    {
        $expected = "<?php /**\t@return\tboolean\t*/";

        $input = "<?php /**\t@return\tbool\t*/";

        $this->makeTest($expected, $input);
    }

    public function testFixMoreThings()
    {
        $expected = <<<'EOF'
<?php
    /**
     * Hello there mr integer!
     *
     * @param integer|string|boolean $integer
     *
     * @return string|boolean
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * Hello there mr integer!
     *
     * @param int|string|bool $integer
     *
     * @return string|bool
     */

EOF;

        $this->makeTest($expected, $input);
    }

    public function testFixVar()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @var integer Some integer value.
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @var int Some integer value.
     */

EOF;

        $this->makeTest($expected, $input);
    }

    public function testFixVarWithMoreStuff()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @var boolean|integer|Double Booleans, integers and doubles.
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @var bool|int|Double Booleans, integers and doubles.
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
     * @var notabooleanthistime|boolean|integerr|string|integer|SplObjectStorage
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @var notabooleanthistime|bool|integerr|string|int|SplObjectStorage
     */

EOF;
        $this->makeTest($expected, $input);
    }

    public function testDoNotModifyStrings()
    {
        $expected = <<<'EOF'
<?php

$string = '
    /**
     * @var bool
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
}
