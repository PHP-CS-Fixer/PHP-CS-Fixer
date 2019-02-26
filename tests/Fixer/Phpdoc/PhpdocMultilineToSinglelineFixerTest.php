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
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocMultilineToSinglelineFixer
 */
final class PhpdocMultilineToSinglelineFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideFixCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideFixCases()
    {
        return [
            [
                <<<'EOF'
<?php
/** @var string $foo */
$foo = 'foo';
EOF,
                <<<'EOF'
<?php
/**
 * @var string $foo
 */
$foo = 'foo';
EOF,
            ],
            [
                <<<'EOF'
<?php
/** @var string $foo with description */
$foo = 'foo';
EOF,
                <<<'EOF'
<?php
/**
 * @var string $foo with description
 */
$foo = 'foo';
EOF,
            ],
            [
                <<<'EOF'
<?php
/**
 * With descriptino above annotation
 *
 * @var string $foo
 */
$foo = 'foo';
EOF,
            ],
            [
                <<<'EOF'
<?php
/**
 * just a comment
 */
$foo = 'foo';
EOF,
            ],
            [
                <<<'EOF'
<?php
/**
 * @deprecated
 * @internal
 */
$foo = 'foo';
EOF,
            ],
            [
                <<<'EOF'
<?php
/** @deprecated @internal */
$foo = 'foo';
EOF,
                <<<'EOF'
<?php
/**
 * @deprecated @internal
 */
$foo = 'foo';
EOF,
            ],
        ];
    }
}
