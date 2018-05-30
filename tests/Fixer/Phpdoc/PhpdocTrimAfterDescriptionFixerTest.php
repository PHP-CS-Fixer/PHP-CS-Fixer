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
 * @author Nobu Funaki <nobu.funaki@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocTrimAfterDescriptionFixer
 */
final class PhpdocTrimAfterDescriptionFixerTest extends AbstractFixerTestCase
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
            'basic' => [
                <<<'EOF'
                <?php
                    /**
                     * Summary.
                     *
                     * Description.
                     *
                     * @var int
                     *
                     *
                     * @return int
                     *
                     *
                     * foo
                     */

EOF
                , <<<'EOF'
                <?php
                    /**
                     * Summary.
                     *
                     *
                     * Description.
                     *
                     *
                     * @var int
                     *
                     *
                     * @return int
                     *
                     *
                     * foo
                     */

EOF
            ],
            'multiple extra blank lines' => [
                <<<'EOF'
                <?php
                    /**
                     * Summary.
                     *
                     * Description.
                     *
                     * @var int
                     */

EOF
                , <<<'EOF'
                <?php
                    /**
                     * Summary.
                     *
                     *
                     *
                     * Description.
                     *
                     *
                     *
                     * @var int
                     */

EOF
            ],
            'extra blank lines after summary' => [
                <<<'EOF'
                <?php
                    /**
                     * Summary.
                     *
                     */

EOF
                , <<<'EOF'
                <?php
                    /**
                     * Summary.
                     *
                     *
                     *
                     */

EOF
            ],
        ];
    }

    /**
     * @param string $expected
     *
     * @dataProvider provideNoChangeCases
     */
    public function testNoChange($expected)
    {
        $this->doTest($expected);
    }

    public function provideNoChangeCases()
    {
        return [
            'summary only' => [<<<'EOF'
                <?php
                    /**
                     * Summary.
                     *
                     */

EOF
            ],
            'inline doc' => [<<<'EOF'
                <?php
                    /** Summary. */

EOF
            ],
            'extra blank lines in description' => [
                <<<'EOF'
                <?php
                    /**
                     * Summary.
                     *
                     * Description has multiple blank lines:
                     *
                     *
                     * End.
                     *
                     * @var int
                     */

EOF
            ],
            'extra blank lines after annotation' => [
                <<<'EOF'
                <?php
                    /**
                     * Summary.
                     *
                     * Description.
                     *
                     * @var int
                     *
                     * Ignore the below blank lines:
                     *
                     *
                     * End.
                     */

EOF
            ],
        ];
    }
}
