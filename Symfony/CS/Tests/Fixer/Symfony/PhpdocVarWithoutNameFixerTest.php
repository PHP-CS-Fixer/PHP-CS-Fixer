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

namespace Symfony\CS\Tests\Fixer\Symfony;

use Symfony\CS\Tests\Fixer\AbstractFixerTestBase;

/**
 * @author Graham Campbell <graham@mineuk.com>
 */
class PhpdocVarWithoutNameFixerTest extends AbstractFixerTestBase
{
    public function testFixVar()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @var string Hello!
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @var string $foo Hello!
     */

EOF;

        $this->makeTest($expected, $input);
    }

    public function testFixType()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @var int|null
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @var int|null $bar
     */

EOF;

        $this->makeTest($expected, $input);
    }

    public function testDoNothing()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @var Foo\Bar This is a variable.
     */

EOF;

        $this->makeTest($expected);
    }

    public function testFixVarWithOtherAnnotation()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @var string Hello!
     *
     * @deprecated
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @var string $foo Hello!
     *
     * @deprecated
     */

EOF;

        $this->makeTest($expected, $input);
    }

    public function testFixVarWithNestedKeys()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @var array {
     *     @var bool   $required Whether this element is required
     *     @var string $label    The display name for this element
     * }
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @var array $options {
     *     @var bool   $required Whether this element is required
     *     @var string $label    The display name for this element
     * }
     */

EOF;

        $this->makeTest($expected, $input);
    }

    public function testSingleLine()
    {
        $expected = <<<'EOF'
<?php
    /** @var Foo\Bar $bar */
    $bar;
EOF;

        $this->makeTest($expected);
    }

    public function testEmpty()
    {
        $expected = <<<'EOF'
<?php
    /**
     *
     */

EOF;

        $this->makeTest($expected);
    }

    public function testInlineDoc()
    {
        $expected = <<<'EOF'
<?php
    /**
     * Initializes this class with the given options.
     *
     * @param array $options {
     *     @var bool   $required Whether this element is required
     *     @var string $label    The display name for this element
     * }
     */

EOF;

        $this->makeTest($expected);
    }

    public function testInlineDocAgain()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param int[] $stuff {
     *     @var int $foo
     * }
     *
     * @return void
     */

EOF;

        $this->makeTest($expected);
    }
}
