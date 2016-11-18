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

use PhpCsFixer\Test\AbstractFixerTestCase;

/**
 * @author Graham Campbell <graham@alt-three.com>
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 */
final class PhpdocTypeToVarFixerTest extends AbstractFixerTestCase
{
    /**
     * @param string      $expected
     * @param null|string $input
     *
     * @dataProvider provideCases
     */
    public function testFix($expected, $input = null)
    {
        $this->doTest($expected, $input);
    }

    public function provideCases()
    {
        return array(
            array(
                '<?php
    /**
     *
     */',
            ),
            array(
                '<?php
    /**
     * @var string Hello!
     */',
                '<?php
    /**
     * @type string Hello!
     */',
            ),
            array(
                '<?php /** @var string Hello! */',
                '<?php /** @type string Hello! */',
            ),
            array(
                '<?php
    /**
     * Initializes this class with the given options.
     *
     * @param array $options {
     *     @var bool   $required Whether this element is required
     *     @var string $label    The display name for this element
     * }
     */',
                '<?php
    /**
     * Initializes this class with the given options.
     *
     * @param array $options {
     *     @type bool   $required Whether this element is required
     *     @type string $label    The display name for this element
     * }
     */',
            ),
        );
    }
}
