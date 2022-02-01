<?php

declare(strict_types=1);

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
 * @author Graham Campbell <hello@gjcampbell.co.uk>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocNoAccessFixer
 */
final class PhpdocNoAccessFixerTest extends AbstractFixerTestCase
{
    public function testFixAccess(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     */

EOF;

        $input = <<<'EOF'
<?php
    /**
     * @access public
     */

EOF;

        $this->doTest($expected, $input);
    }

    public function testFixMany(): void
    {
        $expected = <<<'EOF'
<?php
/**
 * Hello!
 * @notaccess bar
 */

EOF;

        $input = <<<'EOF'
<?php
/**
 * Hello!
 * @access private
 * @notaccess bar
 * @access foo
 */

EOF;

        $this->doTest($expected, $input);
    }

    public function testDoNothing(): void
    {
        $expected = <<<'EOF'
<?php
    /**
     * @var access
     */

EOF;

        $this->doTest($expected);
    }
}
