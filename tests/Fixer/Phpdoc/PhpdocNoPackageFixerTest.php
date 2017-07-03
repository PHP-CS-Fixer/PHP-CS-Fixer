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
 * @covers \PhpCsFixer\AbstractProxyFixer
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocNoPackageFixer
 */
final class PhpdocNoPackageFixerTest extends AbstractFixerTestCase
{
    public function testFixPackage()
    {
        $expected = <<<'EOF'
<?php
    /**
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @package Foo\Bar
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testFixSubpackage()
    {
        $expected = <<<'EOF'
<?php
    /**
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @subpackage Foo\Bar\Baz
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testFixMany()
    {
        $expected = <<<'EOF'
<?php
/**
 * Hello!
 */

EOF;

        $input = <<<'EOF'
<?php
/**
 * Hello!
 * @package
 * @subpackage
 */

EOF;

        $this->doTest($expected, $input);
    }

    public function testDoNothing()
    {
        $expected = <<<'EOF'
<?php
    /**
     * @var package
     */

EOF;

        $this->doTest($expected);
    }
}
