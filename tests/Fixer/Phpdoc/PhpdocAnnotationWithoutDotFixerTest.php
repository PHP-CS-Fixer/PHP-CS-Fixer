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
 * @author Dariusz Rumiński <dariusz.ruminski@gmail.com>
 *
 * @internal
 *
 * @covers \PhpCsFixer\Fixer\Phpdoc\PhpdocAnnotationWithoutDotFixer
 */
final class PhpdocAnnotationWithoutDotFixerTest extends AbstractFixerTestCase
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
                '<?php
    /**
     * Summary.
     *
     * Description.
     *
     * @param string|null $str   some string
     * @param string $genrb      Optional. The path to the "genrb" executable
     * @param string $ellipsis1  Ellipsis is this: ...
     * @param string $ellipsis2  Ellipsis is this: 。。。
     * @param string $ellipsis3  Ellipsis is this: …
     * @param bool   $isStr      Is it a string?
     * @param int    $int        Some multiline
     *                           description. With many dots.
     *
     * @return array result array
     *
     * @SomeCustomAnnotation This is important sentence that must not be modified.
     */',
                '<?php
    /**
     * Summary.
     *
     * Description.
     *
     * @param string|null $str   Some string.
     * @param string $genrb      Optional. The path to the "genrb" executable
     * @param string $ellipsis1  Ellipsis is this: ...
     * @param string $ellipsis2  Ellipsis is this: 。。。
     * @param string $ellipsis3  Ellipsis is this: …
     * @param bool   $isStr      Is it a string?
     * @param int    $int        Some multiline
     *                           description. With many dots.
     *
     * @return array Result array。
     *
     * @SomeCustomAnnotation This is important sentence that must not be modified.
     */',
            ],
            [
                // invalid char inside line won't crash the fixer
                '<?php
    /**
     * @var string This: '.chr(174).' is an odd character.
     * @var string This: '.chr(174).' is an odd character 2nd time。
     */',
            ],
            [
                '<?php
    /**
     * @deprecated since version 2. Use emergency() which is PSR-3 compatible.
     */',
            ],
            [
                '<?php
    /**
     * @internal This method is public to be usable as callback. It should not
     *           be used in user code.
     */',
            ],
            [
                '<?php
    /**
     * @deprecated this is
     *             deprecated
     */',
                '<?php
    /**
     * @deprecated This is
     *             deprecated.
     */',
            ],
            [
                '<?php
    /**
     * @return bool|null returns `true` if the class has a single-column ID
     *                   and Returns `false` otherwise
     */',
                '<?php
    /**
     * @return bool|null Returns `true` if the class has a single-column ID
     *                   and Returns `false` otherwise.
     */',
            ],
        ];
    }
}
