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
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\AbstractPhpdocTypesFixer
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocTypesFixer
 */
final class PhpdocTypesFixerTest extends AbstractFixerTestCase
{
    public function testConversion()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param boolean|array|Foo $bar
     *
     * @return int|float
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param Boolean|Array|Foo $bar
     *
     * @return inT|Float
     */

EOF;
        $this->doTest($expected, $input);
    }

    public function testArrayStuff()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param string|string[] $bar
     *
     * @return int[]
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param STRING|String[] $bar
     *
     * @return inT[]
     */

EOF;
        $this->doTest($expected, $input);
    }

    public function testMixedAndVoid()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param mixed $foo
     *
     * @return void
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param Mixed $foo
     *
     * @return Void
     */

EOF;
        $this->doTest($expected, $input);
    }

    public function testIterableFix()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param iterable $foo
     *
     * @return Itter
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param Iterable $foo
     *
     * @return Itter
     */

EOF;
        $this->doTest($expected, $input);
    }

    public function testMethodAndPropertyFix()
    {
        $expected = <<<'EOF'
<?php
/**
 * @method self foo()
 * @property int $foo
 * @property-read boolean $bar
 * @property-write mixed $baz
 */

EOF;

        $input = <<<'EOF'
<?php
/**
 * @method Self foo()
 * @property Int $foo
 * @property-read Boolean $bar
 * @property-write MIXED $baz
 */

EOF;

        $this->doTest($expected, $input);
    }

    public function testThrows()
    {
        $expected = <<<'EOF'
<?php
/**
 * @throws static
 */

EOF;

        $input = <<<'EOF'
<?php
/**
 * @throws STATIC
 */

EOF;

        $this->doTest($expected, $input);
    }

    public function testInlineDoc()
    {
        $expected = <<<'EOF'
<?php
    /**
     * Does stuffs with stuffs.
     *
     * @param array $stuffs {
     *     @var bool $foo
     *     @var int  $bar
     * }
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * Does stuffs with stuffs.
     *
     * @param array $stuffs {
     *     @var Bool $foo
     *     @var INT  $bar
     * }
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testWithConfig()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @param self|array|Foo $bar
     *
     * @return int|float|callback
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @param SELF|Array|Foo $bar
     *
     * @return inT|Float|callback
     */

EOF;

        $this->fixer->configure(['groups' => ['simple', 'meta']]);
        $this->doTest($expected, $input);
    }

    public function testWrongConfig()
    {
        $this->expectException(\PhpCsFixer\ConfigurationException\InvalidFixerConfigurationException::class);
        $this->expectExceptionMessageRegExp('/^\[phpdoc_types\] Invalid configuration: The option "groups" .*\.$/');

        $this->fixer->configure(['groups' => ['__TEST__']]);
    }
}
